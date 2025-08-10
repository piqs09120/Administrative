<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUsers extends Command
{
    protected $signature = 'app:check-users';
    protected $description = 'Check what users exist in the database';

    public function handle()
    {
        $this->info('👥 Checking Users in Database');
        $this->newLine();

        $users = User::all(['id', 'name', 'email', 'role']);
        
        if ($users->isEmpty()) {
            $this->error('❌ No users found in database');
            return;
        }

        $this->line('📋 Found ' . $users->count() . ' users:');
        $this->newLine();

        foreach ($users as $user) {
            $role = $user->role ?? 'no role';
            $this->line("   ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$role}");
        }

        $this->newLine();
        $this->line('🔍 Which user should receive facility reservation emails?');
        $this->line('   • gigipiquero@gmail.com should get reservation confirmations');
        $this->line('   • piqs09120@gmail.com should get admin notifications');
    }
}