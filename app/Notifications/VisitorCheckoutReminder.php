<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Visitor;

class VisitorCheckoutReminder extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Checkout Reminder - ' . $this->visitor->name)
            ->greeting('Hello ' . $this->visitor->name . '!')
            ->line('This is a reminder that your visit is ending soon.')
            ->line('**Checkout Time:** ' . $this->visitor->expected_time_out)
            ->line('**Time Remaining:** ' . $this->minutesRemaining . ' minutes')
            ->line('Please prepare to check out at the designated time.')
            ->line('Thank you for visiting!')
            ->salutation('Best regards, Soliera Hotel');
    }

    public function toArray($notifiable)
    {
        return [
            'visitor_id' => $this->visitor->id,
            'visitor_name' => $this->visitor->name,
            'checkout_time' => $this->visitor->expected_time_out,
            'minutes_remaining' => $this->minutesRemaining,
            'type' => 'checkout_reminder'
        ];
    }
}
