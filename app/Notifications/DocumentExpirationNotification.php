<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Document;

class DocumentExpirationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $document;
    protected $daysUntilExpiration;

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document, $daysUntilExpiration)
    {
        $this->document = $document;
        $this->daysUntilExpiration = $daysUntilExpiration;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $expirationDate = $this->document->retention_until->format('M d, Y');
        $daysLeft = $this->daysUntilExpiration;
        
        return (new MailMessage)
            ->subject("Document Expiration Alert: {$this->document->title}")
            ->greeting('Document Expiration Alert')
            ->line("A document is approaching its expiration date.")
            ->line("**Document:** {$this->document->title}")
            ->line("**Document ID:** {$this->document->document_uid}")
            ->line("**Department:** {$this->document->department}")
            ->line("**Confidentiality:** " . ucfirst($this->document->confidentiality))
            ->line("**Expiration Date:** {$expirationDate}")
            ->line("**Days Remaining:** {$daysLeft}")
            ->line("**Status:** " . ucfirst($this->document->status))
            ->action('View Document', route('document.show', $this->document->id))
            ->line('Please review this document and take appropriate action before it expires.')
            ->line('If no action is taken, the document will be automatically marked for disposal.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'document_expiration',
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'document_uid' => $this->document->document_uid,
            'department' => $this->document->department,
            'confidentiality' => $this->document->confidentiality,
            'expiration_date' => $this->document->retention_until->toISOString(),
            'days_remaining' => $this->daysUntilExpiration,
            'status' => $this->document->status,
            'action_required' => 'Review document before expiration',
            'action_url' => route('document.show', $this->document->id)
        ];
    }
}
