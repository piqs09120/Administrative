<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EnergyUsage;
use Carbon\Carbon;

class EnergyUsageSeeder extends Seeder
{
    public function run(): void
    {
        EnergyUsage::insert([
            [
                'area' => 'Guest Rooms',
                'usage' => 1200,
                'cost' => 144,
                'recorded_at' => Carbon::now()->startOfMonth()
            ],
            [
                'area' => 'Kitchen & Restaurant',
                'usage' => 650,
                'cost' => 78,
                'recorded_at' => Carbon::now()->startOfMonth()
            ],
            [
                'area' => 'Common Areas',
                'usage' => 350,
                'cost' => 42,
                'recorded_at' => Carbon::now()->startOfMonth()
            ],
            [
                'area' => 'HVAC System',
                'usage' => 250,
                'cost' => 30,
                'recorded_at' => Carbon::now()->startOfMonth()
            ]
        ]);
    }
} 