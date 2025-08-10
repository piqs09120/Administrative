<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixUserEmail extends Command
{
    protected $signature = 'app:fix-user-email';
    protected $description = 'Fix user email to match the correct email address';

    public function handle()
    {
        $this->info('🔧 Fixing User Email Address');
        $this->newLine();

        // Find the user with gigi@gmail.com
        $user = User::where('email', 'gigi@gmail.com')->first();
        
        if ($user) {
            $this->line("Found user: {$user->name} ({$user->email})");
            
            // Update to the correct email
            $user->update([
                'email' => 'gigipiquero@gmail.com',
                'name' => 'Gigi Piquero'
            ]);
            
            $this->info("✅ Updated email to: gigipiquero@gmail.com");
            $this->line("✅ Updated name to: Gigi Piquero");
        } else {
            // Create new user if not exists
            $this->line("User not found, creating new user...");
            
            $user = User::create([
                'name' => 'Gigi Piquero',
                'email' => 'gigipiquero@gmail.com',
                'password' => bcrypt('password123'),
                'role' => 'user'
            ]);
            
            $this->info("✅ Created new user: {$user->name} ({$user->email})");
        }

        $this->newLine();
        $this->line("📧 Now facility reservation emails will go to: gigipiquero@gmail.com");
    }
}