<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\FacilityReservation;

class SecurityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;

    public function __construct(FacilityReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        // Temporarily disable email to prevent SMTP errors during testing
        return ['database'];
    }

    public function toMail($notifiable)
    {
        $visitorCount = $this->reservation->visitor_data ? count($this->reservation->visitor_data) : 0;
        $passCount = $this->reservation->digital_pass_data ? count($this->reservation->digital_pass_data) : 0;

        $mail = (new MailMessage)
            ->subject('🔒 Security Alert: Facility Access Scheduled')
            ->greeting('Security Alert')
            ->line('A facility reservation with visitor access has been approved and requires your attention.')
            ->line('**Facility:** ' . $this->reservation->facility->name)
            ->line('**Date & Time:** ' . $this->reservation->start_time->format('M j, Y g:i A') . ' - ' . $this->reservation->end_time->format('g:i A'))
            ->line('**Purpose:** ' . ($this->reservation->purpose ?: 'Not specified'))
            ->line('**Reserved by:** ' . $this->reservation->reserver->name);

        if ($visitorCount > 0) {
            $mail->line("**Visitors Expected:** {$visitorCount}")
                 ->line("**Digital Passes Generated:** {$passCount}");
        }

        if ($this->reservation->digital_pass_data) {
            $mail->line('**Visitor Details:**');
            foreach ($this->reservation->digital_pass_data as $pass) {
                $mail->line("• {$pass['visitor_name']} ({$pass['visitor_company']}) - Pass ID: {$pass['pass_id']}");
            }
        }

        $mail->line('Please ensure security protocols are in place for the scheduled access period.')
             ->action('View Reservation Details', url('/facility_reservations/' . $this->reservation->id))
             ->line('This is an automated security notification.');

        return $mail;
    }

    public function toArray($notifiable)
    {
        $visitorCount = $this->reservation->visitor_data ? count($this->reservation->visitor_data) : 0;
        
        return [
            'type' => 'security_alert',
            'reservation_id' => $this->reservation->id,
            'facility_name' => $this->reservation->facility->name,
            'start_time' => $this->reservation->start_time->format('Y-m-d H:i:s'),
            'end_time' => $this->reservation->end_time->format('Y-m-d H:i:s'),
            'visitor_count' => $visitorCount,
            'reserver_name' => $this->reservation->reserver->name,
            'purpose' => $this->reservation->purpose,
        ];
    }
}
