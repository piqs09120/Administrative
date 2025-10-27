<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SystemActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->data['title'] ?? 'System Notification')
                    ->line($this->data['message'] ?? 'A system action has occurred.')
                    ->action('View Details', url('/'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->data['title'] ?? 'System Notification',
            'message' => $this->data['message'] ?? 'A system action has occurred.',
            'type' => $this->data['type'] ?? 'info',
            'action' => $this->data['action'] ?? null,
            'model_type' => $this->data['model_type'] ?? null,
            'model_id' => $this->data['model_id'] ?? null,
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->data['title'] ?? 'System Notification',
            'message' => $this->data['message'] ?? 'A system action has occurred.',
            'type' => $this->data['type'] ?? 'info',
            'action' => $this->data['action'] ?? null,
            'model_type' => $this->data['model_type'] ?? null,
            'model_id' => $this->data['model_id'] ?? null,
        ];
    }
}

