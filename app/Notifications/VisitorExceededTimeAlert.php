<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Visitor;

class VisitorExceededTimeAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $visitor;
    protected $minutesOverdue;

    public function __construct(Visitor $visitor, $minutesOverdue)
    {
        $this->visitor = $visitor;
        $this->minutesOverdue = $minutesOverdue;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠️ OVERDUE: Visitor Exceeded Time - ' . $this->visitor->name)
            ->greeting('⚠️ OVERDUE ALERT')
            ->line('A visitor has exceeded their expected checkout time!')
            ->line('**Visitor:** ' . $this->visitor->name)
            ->line('**Company:** ' . $this->visitor->company)
            ->line('**Expected Checkout:** ' . $this->visitor->expected_time_out)
            ->line('**Time Overdue:** ' . $this->minutesOverdue . ' minutes')
            ->line('**Host:** ' . $this->visitor->host_employee)
            ->line('**Department:** ' . $this->visitor->department)
            ->line('**Facility:** ' . ($this->visitor->facility ? $this->visitor->facility->name : 'N/A'))
            ->line('**Status:** PENDING EXIT')
            ->action('View Visitor Details', url('/visitor/' . $this->visitor->id))
            ->line('🚨 IMMEDIATE ACTION REQUIRED: Please contact the visitor and ensure proper checkout.')
            ->salutation('Soliera Hotel Security System');
    }

    public function toArray($notifiable)
    {
        return [
            'visitor_id' => $this->visitor->id,
            'visitor_name' => $this->visitor->name,
            'company' => $this->visitor->company,
            'checkout_time' => $this->visitor->expected_time_out,
            'minutes_overdue' => $this->minutesOverdue,
            'host' => $this->visitor->host_employee,
            'department' => $this->visitor->department,
            'facility' => $this->visitor->facility ? $this->visitor->facility->name : 'N/A',
            'type' => 'visitor_exceeded_time_alert',
            'priority' => 'critical',
            'alert_level' => 'overdue',
            'status' => 'pending_exit'
        ];
    }
}
