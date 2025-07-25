<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        Reservation::insert([
            [
                'room_id' => 2,
                'user_id' => 1,
                'guest_name' => 'Juan Dela Cruz',
                'check_in' => Carbon::now()->subDays(2),
                'check_out' => Carbon::now()->addDays(1),
                'status' => 'active',
                'total_price' => 5400
            ],
            [
                'room_id' => 5,
                'user_id' => 1,
                'guest_name' => 'Maria Santos',
                'check_in' => Carbon::now()->subDays(5),
                'check_out' => Carbon::now()->subDays(2),
                'status' => 'completed',
                'total_price' => 5400
            ]
        ]);
    }
} 