<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::insert([
            ['number' => '101', 'type' => 'Single', 'status' => 'available', 'price' => 1200],
            ['number' => '102', 'type' => 'Double', 'status' => 'occupied', 'price' => 1800],
            ['number' => '103', 'type' => 'Suite', 'status' => 'maintenance', 'price' => 3500],
            ['number' => '104', 'type' => 'Single', 'status' => 'available', 'price' => 1200],
            ['number' => '105', 'type' => 'Double', 'status' => 'occupied', 'price' => 1800],
        ]);
    }
} 