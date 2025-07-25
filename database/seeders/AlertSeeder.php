<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alert;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        Alert::insert([
            [
                'type' => 'critical',
                'title' => 'Kitchen Freezer Temperature Alert',
                'message' => 'Main kitchen freezer temperature has risen above safe levels (5°F)',
                'status' => 'active',
                'location' => 'Main Kitchen',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'warning',
                'title' => 'High Energy Usage Detected',
                'message' => 'Energy consumption in guest rooms is 15% above normal levels',
                'status' => 'acknowledged',
                'location' => 'Guest Rooms - Floor 3',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay()
            ]
        ]);
    }
} 