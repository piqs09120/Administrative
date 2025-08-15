<?php

namespace App\Jobs;

use App\Models\FacilityReservation;
use App\Notifications\FacilityReservationStatusNotification;
use App\Services\FacilityCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckAndAutoApproveReservation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $reservationId;

    public function __construct(int $reservationId)
    {
        $this->reservationId = $reservationId;
    }

    public function handle(FacilityCalendarService $calendarService): void
    {
        $reservation = FacilityReservation::find($this->reservationId);
        if (!$reservation) {
            return;
        }

        // If already processed, skip
        if (!in_array($reservation->status, ['pending'])) {
            return;
        }

        try {
            // Log workflow step
            $reservation->logWorkflowStep('availability_check_started', 'Starting facility availability check');
            
            // Use the enhanced calendar service to check availability
            $availability = $calendarService->checkAvailability(
                $reservation->facility_id,
                $reservation->start_time,
                $reservation->end_time,
                $reservation->id
            );

            // Update availability check status (and keep model state in-sync)
            $reservation->availability_checked = true;
            $reservation->availability_checked_at = now();
            $reservation->availability_conflicts = $availability['available'] ? null : $availability['conflict_details'];
            $reservation->save();

            if ($availability['available']) {
                // No conflicts - proceed with approval logic
                $reservation->updateWorkflowStage('availability_checked', 'Facility is available for requested time');
                
                if ($reservation->canAutoApprove()) {
                    // Auto-approve the reservation
                    $reservation->update([
                        'status' => 'approved',
                        'approved_by' => null,
                        'auto_approved_at' => now(),
                        'remarks' => 'Auto-approved by system - no conflicts and no special requirements'
                    ]);
                    
                    $reservation->updateWorkflowStage('approved', 'Auto-approved by system');
                    
                    // Send confirmation email with error handling
                    try {
                        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send email notification for auto-approved reservation', [
                            'reservation_id' => $reservation->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    Log::info('Reservation auto-approved (job)', ['reservation_id' => $reservation->id]);
                } else {
                    // Requires manual review
                    $requiresReview = [];
                    if ($reservation->requires_legal_review) $requiresReview[] = 'legal review';
                    if ($reservation->requires_visitor_coordination) $requiresReview[] = 'visitor coordination';
                    
                    $reservation->update([
                        'remarks' => 'Facility available but requires ' . implode(' and ', $requiresReview)
                    ]);
                    
                    $reservation->updateWorkflowStage('pending_review', 'Requires manual review: ' . implode(', ', $requiresReview));
                    
                    // Send notification about pending review with error handling
                    try {
                        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send email notification for pending review reservation', [
                            'reservation_id' => $reservation->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            } else {
                // Conflicts found - auto-deny
                $reservation->update([
                    'status' => 'denied',
                    'remarks' => 'Facility not available for requested time period. ' . $availability['conflict_details']
                ]);
                
                $reservation->updateWorkflowStage('denied', 'Auto-denied due to facility conflicts');
                
                // Send denial notification with error handling
                try {
                    $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
                } catch (\Exception $e) {
                    Log::warning('Failed to send email notification for denied reservation', [
                        'reservation_id' => $reservation->id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                Log::info('Reservation denied due to conflicts (job)', [
                    'reservation_id' => $reservation->id,
                    'conflicts' => $availability['conflicts']
                ]);
            }
        } catch (\Throwable $e) {
            $reservation->logWorkflowStep('availability_check_error', 'Error during availability check', [
                'error' => $e->getMessage()
            ]);
            
            Log::error('CheckAndAutoApproveReservation failed', [
                'reservation_id' => $this->reservationId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}




