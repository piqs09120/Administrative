<?php

namespace App\Jobs;

use App\Models\FacilityReservation;
use App\Models\User;
use App\Notifications\SecurityNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifySecurityTeam implements ShouldQueue
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
            $reservation->logWorkflowStep('security_notification_started', 'Notifying security team');

            // Find security team members (users with 'security' role)
            $securityTeam = User::where('role', 'security')->get();
            
            if ($securityTeam->isEmpty()) {
                // Fallback to administrators
                $securityTeam = User::where('role', 'administrator')->get();
            }

            // Send notifications to security team
            foreach ($securityTeam as $securityMember) {
                try {
                    $securityMember->notify(new SecurityNotification($reservation));
                } catch (\Exception $e) {
                    Log::warning('Failed to send email notification to security team member', [
                        'user_id' => $securityMember->id,
                        'reservation_id' => $reservation->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue processing other members
                }
            }

            // Log the notification in workflow
            $reservation->updateWorkflowStage('security_notified', 
                'Security team notified of upcoming visitor access. ' . 
                $securityTeam->count() . ' team members notified.');

            Log::info('Security team notified successfully', [
                'reservation_id' => $reservation->id,
                'security_members_notified' => $securityTeam->count()
            ]);

        } catch (\Throwable $e) {
            $reservation->logWorkflowStep('security_notification_error', 'Error notifying security team', [
                'error' => $e->getMessage()
            ]);

            Log::error('NotifySecurityTeam failed', [
                'reservation_id' => $this->reservationId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
