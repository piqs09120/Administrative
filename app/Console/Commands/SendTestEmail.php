<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTestEmail extends Command
{
    protected $signature = 'app:send-test-email {email}';
    protected $description = 'Send a simple test email to verify SMTP is working';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("📧 Sending test email to: {$email}");
        
        try {
            Mail::raw('This is a test email from Soliera system! If you receive this, email notifications are working properly.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from Soliera - Email Notifications Working!')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("✅ Test email sent successfully!");
            $this->line("📬 Check your inbox at: {$email}");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
            Log::error('Test email failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);
        }
    }
}