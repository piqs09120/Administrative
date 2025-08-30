<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            AlertSeeder::class,
            DepartmentAccountSeeder::class,
            EnergyUsageSeeder::class,
            OrderSeeder::class,
            ReservationSeeder::class,
            RoomSeeder::class,
        ]);

        // Seed sample legal cases for testing
        $this->seedLegalCases();
    }

    private function seedLegalCases()
    {
        $statuses = ['pending', 'ongoing', 'completed', 'rejected'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $caseTypes = ['civil', 'criminal', 'administrative', 'contract', 'employment', 'property'];

        for ($i = 1; $i <= 10; $i++) {
            \App\Models\LegalCase::create([
                'case_title' => 'Sample Legal Case ' . $i,
                'case_description' => 'This is a sample legal case description for testing purposes. Case number ' . $i . ' involves various legal matters.',
                'case_type' => $caseTypes[array_rand($caseTypes)],
                'priority' => $priorities[array_rand($priorities)],
                'status' => $statuses[array_rand($statuses)],
                'case_number' => 'CASE-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'filing_date' => now()->subDays(rand(1, 30)),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
} 