<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('facility:update-status')->everyMinute();
        
        // Generate monthly reports on the 1st of each month at 9:00 AM
        $schedule->command('reports:generate-monthly --all-facilities --email=' . config('mail.admin_email', 'admin@example.com'))
                 ->monthlyOn(1, '09:00')
                 ->timezone('America/New_York');

        // Apply DMS retention policy daily at 1:30 AM
        $schedule->command('documents:apply-retention')->dailyAt('01:30');
        
        // Check document expiration daily at 8:00 AM
        $schedule->command('documents:check-expiration --days=7')->dailyAt('08:00');
        $schedule->command('documents:check-expiration --days=1')->dailyAt('08:30');
        
        // Monitor document expiration daily at 9:00 AM
        $schedule->command('documents:monitor-expiration --days=30')->dailyAt('09:00');
        
        // Check visitor checkout times every minute
        $schedule->job(new \App\Jobs\CheckVisitorCheckoutTimes())->everyMinute();
        
        // Clean up expired OTP codes every hour
        $schedule->command('otp:cleanup')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 