<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacilityRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\FacilityRequest::create([
            'request_type' => 'reservation',
            'department' => 'Finance',
            'priority' => 'low',
            'location' => 'qc',
            'facility_id' => 30,
            'requested_datetime' => now()->addDays(1),
            'description' => 'Need to reserve facility for meeting.',
            'contact_name' => 'ern',
            'contact_email' => 'piqs09120@gmail.com',
            'status' => 'pending'
        ]);
    }
}
