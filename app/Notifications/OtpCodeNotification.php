<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $otpCode;
    protected $employeeName;

    public function __construct($otpCode, $employeeName)
    {
        $this->otpCode = $otpCode;
        $this->employeeName = $employeeName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔐 Your OTP Code - Soliera Hotel Login')
            ->greeting('Hello ' . $this->employeeName . '!')
            ->line('You have requested to login to the Soliera Hotel Administrative System.')
            ->line('Please use the following One-Time Password (OTP) to complete your login:')
            ->line('**Your OTP Code: ' . $this->otpCode . '**')
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this login, please ignore this email and contact your administrator.')
            ->line('For security reasons, do not share this code with anyone.')
            ->salutation('Best regards, Soliera Hotel IT Department');
    }
}
