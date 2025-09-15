<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitor;
use App\Models\Facility;
use App\Jobs\CheckVisitorCheckoutTimes;
use Carbon\Carbon;

class TestVisitorTimeoutSystem extends Command
{
    protected $signature = 'visitor:test-timeout-system';
    protected $description = 'Test the visitor timeout reminder system by creating test visitors';

    public function handle()
    {
        $this->info('🧪 Testing Visitor Timeout System...');
        
        // Create test facility if it doesn't exist
        $facility = Facility::first();
        if (!$facility) {
            $facility = Facility::create([
                'name' => 'Test Facility',
                'description' => 'Test facility for timeout testing',
                'capacity' => 10,
                'status' => 'active'
            ]);
            $this->info("✅ Created test facility: {$facility->name}");
        }
        
        // Create test visitors with different timeout scenarios
        $now = Carbon::now();
        
        // Visitor 1: 10 minutes from timeout (should get email)
        $visitor1 = Visitor::create([
            'name' => 'Test Visitor 1 (10min)',
            'email' => 'test1@example.com',
            'contact' => '1234567890',
            'purpose' => 'Testing 10-minute reminder',
            'facility_id' => $facility->id,
            'time_in' => $now->subHours(1),
            'expected_time_out' => $now->addMinutes(10)->format('Y-m-d H:i:s'),
            'expected_date_out' => $now->addMinutes(10)->format('Y-m-d'),
            'status' => 'active',
            'host_employee' => 'Test Host',
            'company' => 'Test Company'
        ]);
        
        // Visitor 2: 5 minutes from timeout (should get system alert)
        $visitor2 = Visitor::create([
            'name' => 'Test Visitor 2 (5min)',
            'email' => 'test2@example.com',
            'contact' => '1234567891',
            'purpose' => 'Testing 5-minute alert',
            'facility_id' => $facility->id,
            'time_in' => $now->subHours(1),
            'expected_time_out' => $now->addMinutes(5)->format('Y-m-d H:i:s'),
            'expected_date_out' => $now->addMinutes(5)->format('Y-m-d'),
            'status' => 'active',
            'host_employee' => 'Test Host',
            'company' => 'Test Company'
        ]);
        
        // Visitor 3: Already overdue (should be flagged as pending exit)
        $visitor3 = Visitor::create([
            'name' => 'Test Visitor 3 (Overdue)',
            'email' => 'test3@example.com',
            'contact' => '1234567892',
            'purpose' => 'Testing overdue handling',
            'facility_id' => $facility->id,
            'time_in' => $now->subHours(2),
            'expected_time_out' => $now->subMinutes(30)->format('Y-m-d H:i:s'),
            'expected_date_out' => $now->subMinutes(30)->format('Y-m-d'),
            'status' => 'active',
            'host_employee' => 'Test Host',
            'company' => 'Test Company'
        ]);
        
        $this->info("✅ Created test visitors:");
        $this->info("   - {$visitor1->name} (10 minutes from timeout)");
        $this->info("   - {$visitor2->name} (5 minutes from timeout)");
        $this->info("   - {$visitor3->name} (30 minutes overdue)");
        
        // Run the timeout check job
        $this->info("\n🔄 Running timeout check job...");
        dispatch(new CheckVisitorCheckoutTimes());
        
        $this->info("✅ Timeout check job dispatched!");
        
        // Check results
        $this->info("\n📊 Checking results:");
        
        $visitor1->refresh();
        $visitor2->refresh();
        $visitor3->refresh();
        
        $this->info("Visitor 1 pending_exit: " . ($visitor1->pending_exit ? 'Yes' : 'No'));
        $this->info("Visitor 2 pending_exit: " . ($visitor2->pending_exit ? 'Yes' : 'No'));
        $this->info("Visitor 3 pending_exit: " . ($visitor3->pending_exit ? 'Yes' : 'No'));
        
        // Clean up test visitors
        $this->info("\n🧹 Cleaning up test visitors...");
        Visitor::whereIn('id', [$visitor1->id, $visitor2->id, $visitor3->id])->delete();
        $this->info("✅ Test visitors cleaned up!");
        
        $this->info("\n🎉 Visitor timeout system test completed!");
        $this->info("Check the logs and notifications to see the results.");
        
        return 0;
    }
}
