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
    public $eventType;      // add
    public $legalCaseId;    // add (optional link)

    public function __construct(FacilityReservation $reservation, string $eventType = null, $legalCaseId = null)
    {
        $this->reservation = $reservation;
        $this->eventType = $eventType;
        $this->legalCaseId = $legalCaseId;
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

        if ($this->eventType === 'LEGAL_ESCALATED') {
            $mail->line('⚠️ A facility damage incident has been escalated to Legal.')
                 ->line('Facility: ' . $this->reservation->facility->name)
                 ->line('Reservation #: ' . $this->reservation->id)
                 ->line('Estimated Cost: ₱' . number_format((float)($this->reservation->damage_cost ?? 0), 2))
                 ->line('Inspection Notes: ' . ($this->reservation->inspection_notes ?: 'N/A'));
            if ($this->legalCaseId) {
                $mail->action('Open Legal Case', url('/legal/cases/' . $this->legalCaseId));
            } else {
                $mail->action('View Reservation Details', url('/facility_reservations/' . $this->reservation->id));
            }
            return $mail;
        }

        // Add status-specific content
        switch ($this->reservation->status) {
            case 'pending':
                $mail->line('Your facility reservation request has been submitted and is being processed.')
                     ->line('**Facility:** ' . $this->reservation->facility->name)
                     ->line('**Start Time:** ' . $this->reservation->start_time->format('m/d/Y h:i A'))
                     ->line('**End Time:** ' . $this->reservation->end_time->format('m/d/Y h:i A'))
                     ->line('**Purpose:** ' . ($this->reservation->purpose ?: 'Not specified'))
                     ->line('Our system is automatically checking facility availability and processing any documents you provided.');
                break;
            
            case 'approved':
                $mail->line('🎉 Great news! Your facility reservation has been approved.')
                     ->line('**Facility:** ' . $this->reservation->facility->name)
                     ->line('**Start Time:** ' . $this->reservation->start_time->format('m/d/Y h:i A'))
                     ->line('**End Time:** ' . $this->reservation->end_time->format('m/d/Y h:i A'))
                     ->line('**Purpose:** ' . ($this->reservation->purpose ?: 'Not specified'));
                
                if ($this->reservation->isAutoApproved()) {
                    $mail->line('✅ This reservation was automatically approved by our system.');
                }
                
                if ($this->reservation->digital_passes_generated) {
                    $passCount = count($this->reservation->digital_pass_data ?? []);
                    $mail->line("🎫 Digital passes have been generated for {$passCount} visitor(s).");
                }
                break;
            
            case 'denied':
                $mail->line('❌ Unfortunately, your facility reservation request has been denied.')
                     ->line('**Facility:** ' . $this->reservation->facility->name)
                     ->line('**Start Time:** ' . $this->reservation->start_time->format('m/d/Y h:i A'))
                     ->line('**End Time:** ' . $this->reservation->end_time->format('m/d/Y h:i A'))
                     ->line('**Purpose:** ' . ($this->reservation->purpose ?: 'Not specified'));
                
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
        if ($this->eventType === 'LEGAL_ESCALATED') {
            return "⚠️ Damage Escalated to Legal – {$this->reservation->facility->name}";
        }

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