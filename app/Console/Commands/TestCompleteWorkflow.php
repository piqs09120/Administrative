<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\FacilityReservation;
use App\Models\Facility;
use App\Jobs\CheckAndAutoApproveReservation;
use Illuminate\Support\Facades\Log;

class TestCompleteWorkflow extends Command
{
    protected $signature = 'app:test-complete-workflow';
    protected $description = 'Test the complete facility reservation workflow including emails';

    public function handle()
    {
        $this->info('🏢 Testing Complete Facility Reservation Workflow');
        $this->newLine();

        try {
            // Find the correct user (gigipiquero@gmail.com)
            $user = User::where('email', 'gigipiquero@gmail.com')->first();
            if (!$user) {
                $this->error('❌ User gigipiquero@gmail.com not found in database');
                return;
            }

            // Find or create a facility
            $facility = Facility::first();
            if (!$facility) {
                $facility = Facility::create([
                    'name' => 'Conference Room A',
                    'description' => 'Main conference room',
                    'capacity' => 20
                ]);
            }

            $this->line("👤 User: {$user->name} ({$user->email})");
            $this->line("🏢 Facility: {$facility->name}");
            $this->newLine();

            // Create a reservation (simulating no document upload)
            $reservation = FacilityReservation::create([
                'facility_id' => $facility->id,
                'reserved_by' => $user->id,
                'start_time' => now()->addHours(2),
                'end_time' => now()->addHours(4),
                'purpose' => 'Complete workflow test - no document',
                'status' => 'pending',
                'requester_name' => $user->name,
                'requester_department' => $user->department ?? 'IT',
                'requester_contact' => $user->email,
                'workflow_stage' => 'submitted'
            ]);

            $this->info("📝 Step 1: Reservation created (ID: {$reservation->id})");
            
            // Log workflow step
            $reservation->logWorkflowStep('no_document_proceed_to_approval', 
                'Test: No document uploaded, proceeding directly to auto-approval workflow');

            $this->line("📤 Step 2: Dispatching auto-approval job...");
            
            // Dispatch the auto-approval job (this should trigger emails)
            CheckAndAutoApproveReservation::dispatch($reservation->id);
            
            $this->info("✅ Step 3: Auto-approval job dispatched!");
            $this->line("⏳ Job will process in background and send emails");
            $this->newLine();
            
            $this->line("🔍 Expected workflow:");
            $this->line("   1. ✅ Initial submission (status: pending)");
            $this->line("   2. ⏳ Calendar availability check");
            $this->line("   3. ⏳ Auto-approval (if no conflicts)");  
            $this->line("   4. 📧 Confirmation email sent");
            $this->newLine();
            
            $this->info("📧 Check email inbox: {$user->email}");
            $this->line("📊 Also check database notifications table");
            
            // Show recent workflow logs
            $this->line("📋 Current workflow stage: {$reservation->workflow_stage}");
            
        } catch (\Exception $e) {
            $this->error("❌ Workflow test failed: " . $e->getMessage());
            Log::error('Complete workflow test failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
}