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