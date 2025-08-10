<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\FacilityReservation;
use App\Models\Facility;
use App\Notifications\FacilityReservationStatusNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailNotification extends Command
{
    protected $signature = 'app:test-email-notification';
    protected $description = 'Test if email notifications are working properly';

    public function handle()
    {
        $this->info('🧪 Testing Email Notification System');
        $this->newLine();

        try {
            // Find the correct user (gigipiquero@gmail.com)
            $user = User::where('email', 'gigipiquero@gmail.com')->first();
            if (!$user) {
                $this->error('❌ User gigipiquero@gmail.com not found in database');
                return;
            }

            $this->line("📧 Testing with user: {$user->name} ({$user->email})");

            // Find or create a facility
            $facility = Facility::first();
            if (!$facility) {
                $facility = Facility::create([
                    'name' => 'Test Email Facility',
                    'description' => 'Test facility for email notifications',
                    'capacity' => 10
                ]);
                $this->line("🏢 Created test facility: {$facility->name}");
            }

            // Create a test reservation
            $reservation = FacilityReservation::create([
                'facility_id' => $facility->id,
                'reserved_by' => $user->id,
                'start_time' => now()->addHours(2),
                'end_time' => now()->addHours(4),
                'purpose' => 'Email notification test',
                'status' => 'approved',
                'requester_name' => $user->name,
                'requester_department' => $user->department ?? 'IT',
                'requester_contact' => $user->email,
                'workflow_stage' => 'approved'
            ]);

            $this->line("📝 Created test reservation ID: {$reservation->id}");

            // Test notification sending
            $this->line("📤 Attempting to send email notification...");
            
            $user->notify(new FacilityReservationStatusNotification($reservation));
            
            $this->info("✅ Email notification sent successfully!");
            $this->line("📊 Check your email inbox and database notifications table");

            // Check database notifications
            $dbNotifications = $user->notifications()->latest()->take(5)->get();
            $this->line("📄 Recent database notifications: " . $dbNotifications->count());
            
            foreach ($dbNotifications as $notification) {
                $this->line("   • {$notification->type} - {$notification->created_at}");
            }

        } catch (\Exception $e) {
            $this->error("❌ Email test failed: " . $e->getMessage());
            Log::error('Email notification test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}