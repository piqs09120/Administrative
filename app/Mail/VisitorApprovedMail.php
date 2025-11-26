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
        // Ensure pass data is generated
        if (!$this->visitor->pass_data || !isset($this->visitor->pass_data['qr_code'])) {
            // Generate pass data if not exists
            $accessCode = $this->visitor->access_code ?? str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            if (!$this->visitor->access_code) {
                $this->visitor->access_code = $accessCode;
                $this->visitor->save();
            }
            
            $qrService = 'https://api.qrserver.com/v1/create-qr-code/';
            $payload = json_encode([
                'pass_id' => $this->visitor->pass_id,
                'code' => $accessCode,
            ]);
            $params = http_build_query([
                'size' => '200x200',
                'data' => $payload
            ]);
            $qrCode = "{$qrService}?{$params}";
        } else {
            $qrCode = $this->visitor->pass_data['qr_code'];
            $accessCode = $this->visitor->pass_data['access_code'] ?? $this->visitor->access_code;
        }

        return $this->subject('Your Visit is Approved - Digital Pass')
            ->view('emails.visitor-approved', [
                'visitor' => $this->visitor,
                'qrCode' => $qrCode,
                'accessCode' => $accessCode,
            ]);
    }
}


