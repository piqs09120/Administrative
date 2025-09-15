<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Visitor;

class VisitorTimeoutSystemAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $visitor;
    protected $minutesRemaining;

    public function __construct(Visitor $visitor, $minutesRemaining)
    {
        $this->visitor = $visitor;
        $this->minutesRemaining = $minutesRemaining;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚨 URGENT: Visitor Timeout Alert - ' . $this->visitor->name)
            ->greeting('🚨 SYSTEM ALERT')
            ->line('A visitor is approaching their checkout time!')
            ->line('**Visitor:** ' . $this->visitor->name)
            ->line('**Company:** ' . $this->visitor->company)
            ->line('**Expected Checkout:** ' . $this->visitor->expected_time_out)
            ->line('**Time Remaining:** ' . $this->minutesRemaining . ' minutes')
            ->line('**Host:** ' . $this->visitor->host_employee)
            ->line('**Department:** ' . $this->visitor->department)
            ->line('**Facility:** ' . ($this->visitor->facility ? $this->visitor->facility->name : 'N/A'))
            ->action('View Visitor Details', url('/visitor/' . $this->visitor->id))
            ->line('⚠️ Please ensure proper checkout procedures are followed.')
            ->salutation('Soliera Hotel Security System');
    }

    public function toArray($notifiable)
    {
        return [
            'visitor_id' => $this->visitor->id,
            'visitor_name' => $this->visitor->name,
            'company' => $this->visitor->company,
            'checkout_time' => $this->visitor->expected_time_out,
            'minutes_remaining' => $this->minutesRemaining,
            'host' => $this->visitor->host_employee,
            'department' => $this->visitor->department,
            'facility' => $this->visitor->facility ? $this->visitor->facility->name : 'N/A',
            'type' => 'visitor_timeout_system_alert',
            'priority' => 'high',
            'alert_level' => 'urgent'
        ];
    }
}
