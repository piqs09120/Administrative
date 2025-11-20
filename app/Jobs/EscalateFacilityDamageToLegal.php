<?php

namespace App\Jobs;

use App\Models\FacilityReservation;
use App\Notifications\FacilityReservationStatusNotification;
use App\Services\LegalCaseService;
use App\Services\SecureDocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EscalateFacilityDamageToLegal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $reservationId;

    public function __construct(int $reservationId)
    {
        $this->reservationId = $reservationId;
    }

    public function handle(LegalCaseService $legal, SecureDocumentRepository $repo): void
    {
        $reservation = FacilityReservation::with(['facility','reserver'])->find($this->reservationId);
        if (!$reservation) return;

        try {
            // create case
            $case = $legal->createFromReservationDamage($reservation);

            // attach evidence photos if any (already in reservation->damage_photos)
            $photoPaths = $reservation->damage_photos ?? [];

            if (!empty($photoPaths)) {
                // If you attach via Document model with linked_case_id:
                foreach ($photoPaths as $path) {
                    \App\Models\Document::create([
                        'title'         => "Damage Evidence – {$reservation->facility->name}",
                        'description'   => "Evidence photo for case #{$case->case_number}",
                        'category'      => 'evidence',
                        'file_path'     => $path,
                        'uploaded_by'   => $reservation->inspected_by,
                        'status'        => 'active',
                        'linked_case_id'=> $case->id,
                        'source'        => 'facility_damage',
                    ]);
                }
            }

            // notify (optional) - adjust to your Legal role recipients if you have a notifier
            try {
                // Inform reserver (FYI) or Legal group via email/broadcast
                $reservation->remarks = trim(($reservation->remarks ? $reservation->remarks . ' ' : '') . 'Escalated to Legal.');
                $reservation->save();

                // Reuse notification with LEGAL_ESCALATED context
                if ($reservation->reserver && method_exists($reservation->reserver, 'notify')) {
                    $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation, 'LEGAL_ESCALATED', $case->id));
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to send LEGAL_ESCALATED notification', ['rid'=>$reservation->id,'error'=>$e->getMessage()]);
            }

            $reservation->logWorkflowStep('damage_escalated', 'Damage escalated to Legal', [
                'legal_case_id' => $case->id,
                'case_number'   => $case->case_number,
                'damage_cost'   => $reservation->damage_cost,
            ]);

        } catch (\Throwable $e) {
            Log::error('EscalateFacilityDamageToLegal failed', ['rid'=>$this->reservationId,'error'=>$e->getMessage()]);
            $reservation?->logWorkflowStep('damage_escalation_error', 'Error escalating to Legal', ['error'=>$e->getMessage()]);
        }
    }
}



