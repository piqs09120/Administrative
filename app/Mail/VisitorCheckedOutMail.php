<?php

namespace App\Mail;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisitorCheckedOutMail extends Mailable
{
    use Queueable, SerializesModels;

    public Visitor $visitor;

    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor->fresh(['facility']);
    }

    public function build()
    {
        return $this->subject('Thank you for visiting - Checkout Summary')
            ->markdown('emails.visitor-checked-out', [
                'visitor' => $this->visitor,
            ]);
    }
}


