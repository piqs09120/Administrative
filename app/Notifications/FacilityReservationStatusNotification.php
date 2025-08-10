<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\FacilityReservation;

class FacilityReservationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;

    public function __construct(FacilityReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->getEmailSubject())
            ->greeting('Hello ' . $notifiable->name . ',');

        // Add status-specific content
        switch ($this->reservation->status) {
            case 'pending':
                $mail->line('Your facility reservation request has been submitted and is being processed.')
                     ->line('Our system is automatically checking facility availability and processing any documents you provided.');
                break;
            
            case 'approved':
                $mail->line('🎉 Great news! Your facility reservation has been approved.')
                     ->line('**Facility:** ' . $this->reservation->facility->name)
                     ->line('**Date & Time:** ' . $this->reservation->start_time->format('M j, Y g:i A') . ' - ' . $this->reservation->end_time->format('g:i A'));
                
                if ($this->reservation->isAutoApproved()) {
                    $mail->line('✅ This reservation was automatically approved by our system.');
                }
                
                if ($this->reservation->digital_passes_generated) {
                    $passCount = count($this->reservation->digital_pass_data ?? []);
                    $mail->line("🎫 Digital passes have been generated for {$passCount} visitor(s).");
                }
                break;
            
            case 'denied':
                $mail->line('❌ Unfortunately, your facility reservation request has been denied.');
                
                if ($this->reservation->hasAvailabilityConflicts()) {
                    $mail->line('**Reason:** The facility is not available for your requested time period.')
                         ->line('**Conflicts:** ' . $this->reservation->availability_conflicts);
                }
                break;
        }

        // Add workflow stage information
        $workflowStage = $this->reservation->getWorkflowStage();
        if ($workflowStage && $workflowStage !== 'submitted') {
            $mail->line('**Current Stage:** ' . ucwords(str_replace('_', ' ', $workflowStage)));
        }

        // Add remarks if any
        if ($this->reservation->remarks) {
            $mail->line('**Remarks:** ' . $this->reservation->remarks);
        }

        $mail->action('View Reservation Details', url('/facility_reservations/' . $this->reservation->id))
             ->line('Thank you for using our facility reservation system!');

        return $mail;
    }

    private function getEmailSubject()
    {
        $facilityName = $this->reservation->facility->name;
        
        switch ($this->reservation->status) {
            case 'pending':
                return "📋 Facility Reservation Submitted - {$facilityName}";
            case 'approved':
                return "✅ Facility Reservation Approved - {$facilityName}";
            case 'denied':
                return "❌ Facility Reservation Denied - {$facilityName}";
            default:
                return "📬 Facility Reservation Update - {$facilityName}";
        }
    }

    public function toArray($notifiable)
    {
        return [
            'facility_id' => $this->reservation->facility_id,
            'status' => $this->reservation->status,
            'remarks' => $this->reservation->remarks,
        ];
    }
} 