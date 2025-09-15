<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Visitor;

class AdminCheckoutAlert extends Notification implements ShouldQueue
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
            ->subject('Visitor Checkout Alert - ' . $this->visitor->name)
            ->greeting('Administrator Alert')
            ->line('A visitor is about to check out:')
            ->line('**Visitor:** ' . $this->visitor->name)
            ->line('**Company:** ' . $this->visitor->company)
            ->line('**Checkout Time:** ' . $this->visitor->expected_time_out)
            ->line('**Time Remaining:** ' . $this->minutesRemaining . ' minutes')
            ->line('**Host:** ' . $this->visitor->host_employee)
            ->line('**Department:** ' . $this->visitor->department)
            ->action('View Visitor Details', url('/visitor/' . $this->visitor->id))
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
            'type' => 'admin_checkout_alert'
        ];
    }
}
