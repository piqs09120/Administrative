<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OtpCode;

class CleanupExpiredOtps extends Command
{
    protected $signature = 'otp:cleanup';
    protected $description = 'Clean up expired OTP codes';

    public function handle()
    {
        $deletedCount = OtpCode::cleanupExpired();
        
        $this->info("Cleaned up {$deletedCount} expired OTP codes.");
        
        return 0;
    }
}
