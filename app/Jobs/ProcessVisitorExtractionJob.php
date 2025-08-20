<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVisitorExtractionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public int $reservationId;
    public array $aiResult;

    public function __construct(int $reservationId, array $aiResult)
    {
        $this->reservationId = $reservationId;
        $this->aiResult = $aiResult;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\VisitorService $visitorService, \App\Services\ReservationWorkflowService $workflowService): void
    {
        $reservation = \App\Models\FacilityReservation::find($this->reservationId);
        if (!$reservation) {
            return; // Reservation not found, exit.
        }
        
        try {
            // Perform the actual visitor data extraction
            $visitorService->extractVisitorData($reservation, $this->aiResult);
            
            // After extraction, update the visitor coordination task to 'pending' (for approval)
            $visitorTask = $reservation->tasks()->where('task_type', 'visitor_coordination')->first();
            if ($visitorTask) {
                $workflowService->updateTaskStatus($visitorTask, 'pending', 'Visitor data extracted, pending approval.');
            } else {
                // This shouldn't happen if task was created in FacilityReservationController
                $reservation->logWorkflowStep('visitor_extraction_error', 'Visitor task not found after extraction.', [
                    'task_status_update_skipped' => true
                ]);
            }
            
            $reservation->logWorkflowStep('visitor_extraction_complete', 'Visitor data extraction process completed.');
            
        } catch (\Throwable $e) {
            $reservation->logWorkflowStep('visitor_extraction_failed', 'Visitor data extraction failed.', [
                'error' => $e->getMessage()
            ]);
            \Illuminate\Support\Facades\Log::error('ProcessVisitorExtractionJob failed', [
                'reservation_id' => $this->reservationId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
