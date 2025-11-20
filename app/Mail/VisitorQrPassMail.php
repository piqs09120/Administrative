<?php

namespace App\Mail;

use App\Models\VisitorQrPass;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class VisitorQrPassMail extends Mailable
{
    use Queueable, SerializesModels;

    public $qrPass;
    public $visitor;

    /**
     * Create a new message instance.
     */
    public function __construct(VisitorQrPass $qrPass)
    {
        $this->qrPass = $qrPass;
        $this->visitor = $qrPass->visitor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Visitor Pass - ' . $this->qrPass->pass_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.visitor_qr_pass',
            with: [
                'qrPass' => $this->qrPass,
                'visitor' => $this->visitor,
                'qrCodeUrl' => Storage::url($this->qrPass->qr_code_path),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach QR code image
        if ($this->qrPass->qr_code_path && Storage::exists('public/' . $this->qrPass->qr_code_path)) {
            $attachments[] = Attachment::fromPath(Storage::path('public/' . $this->qrPass->qr_code_path))
                ->as('visitor-pass-' . $this->qrPass->pass_code . '.png')
                ->withMime('image/png');
        }

        return $attachments;
    }
}



