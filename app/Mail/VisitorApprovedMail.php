<?php

namespace App\Mail;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisitorApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Visitor $visitor;

    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor->fresh(['facility']);
    }

    public function build()
    {
        return $this->subject('Your Visit is Approved - Digital Pass')
            ->markdown('emails.visitor-approved', [
                'visitor' => $this->visitor,
                'qr' => $this->visitor->pass_data['qr_code'] ?? null,
            ]);
    }
}


