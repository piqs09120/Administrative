<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::insert([
            [
                'user_id' => 1,
                'table_number' => 'A1',
                'total_amount' => 1500,
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 1,
                'table_number' => 'B2',
                'total_amount' => 2200,
                'status' => 'paid',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay()
            ]
        ]);
    }
} 