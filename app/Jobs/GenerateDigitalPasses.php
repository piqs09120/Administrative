<?php

namespace App\Jobs;

use App\Models\FacilityReservation;
use App\Notifications\FacilityReservationStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateDigitalPasses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $reservationId;

    public function __construct(int $reservationId)
    {
        $this->reservationId = $reservationId;
    }

    public function handle(): void
    {
        $reservation = FacilityReservation::find($this->reservationId);
        if (!$reservation) {
            return;
        }

        try {
            $reservation->logWorkflowStep('digital_pass_generation_started', 'Starting digital pass generation');

            // Generate digital passes for visitors
            $visitorData = $reservation->visitor_data ?? [];
            $digitalPasses = [];

            foreach ($visitorData as $index => $visitor) {
                if (($visitor['status'] ?? '') === 'approved') {
                    $passId = 'DP-' . $reservation->id . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                    
                    $digitalPasses[] = [
                        'pass_id' => $passId,
                        'visitor_name' => $visitor['name'],
                        'visitor_company' => $visitor['company'] ?? '',
                        'valid_from' => $reservation->start_time->format('Y-m-d H:i:s'),
                        'valid_until' => $reservation->end_time->format('Y-m-d H:i:s'),
                        'facility' => $reservation->facility->name,
                        'purpose' => $reservation->purpose,
                        'access_level' => $visitor['access_level'] ?? 'visitor',
                        'generated_at' => now()->toISOString(),
                        'status' => 'active'
                    ];
                }
            }

            // Update reservation with digital pass data
            $reservation->update([
                'digital_passes_generated' => true,
                'digital_pass_data' => $digitalPasses
            ]);

            $reservation->updateWorkflowStage('digital_passes_generated', 
                'Generated ' . count($digitalPasses) . ' digital passes for visitors');

            // Dispatch security notification job
            NotifySecurityTeam::dispatch($this->reservationId);

            Log::info('Digital passes generated successfully', [
                'reservation_id' => $reservation->id,
                'passes_count' => count($digitalPasses)
            ]);

        } catch (\Throwable $e) {
            $reservation->logWorkflowStep('digital_pass_generation_error', 'Error generating digital passes', [
                'error' => $e->getMessage()
            ]);

            Log::error('GenerateDigitalPasses failed', [
                'reservation_id' => $this->reservationId,
                'error' => $e->getMessage(),
            ]);
        }
    }

}
