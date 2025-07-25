<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DocumentRequest;

class DocumentRequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $documentRequest;

    public function __construct(DocumentRequest $documentRequest)
    {
        $this->documentRequest = $documentRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Document Request Status Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your document request for "' . $this->documentRequest->document->title . '" has been ' . $this->documentRequest->status . '.')
            ->line('Remarks: ' . ($this->documentRequest->remarks ?? 'None'))
            ->action('View Request', url('/document/' . $this->documentRequest->document_id))
            ->line('Thank you for using our system!');
    }

    public function toArray($notifiable)
    {
        return [
            'document_id' => $this->documentRequest->document_id,
            'status' => $this->documentRequest->status,
            'remarks' => $this->documentRequest->remarks,
        ];
    }
} 