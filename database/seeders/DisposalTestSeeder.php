<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;

class DisposalTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user to assign as uploader
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No users found. Please run user seeder first.');
            return;
        }

        // Create test documents with different retention scenarios
        $testDocuments = [
            [
                'title' => 'Expired Contract - ABC Corp',
                'category' => 'contract',
                'department' => 'Legal',
                'status' => 'active',
                'retention_until' => Carbon::now()->subDays(30), // Expired 30 days ago
                'confidentiality_level' => 'restricted',
                'file_path' => 'test/expired_contract.pdf',
                'uploaded_by' => $user->id,
                'created_at' => Carbon::now()->subYear(),
            ],
            [
                'title' => 'Expiring Policy Document',
                'category' => 'policy',
                'department' => 'HR',
                'status' => 'active',
                'retention_until' => Carbon::now()->addDays(5), // Expires in 5 days
                'confidentiality_level' => 'internal',
                'file_path' => 'test/expiring_policy.pdf',
                'uploaded_by' => $user->id,
                'created_at' => Carbon::now()->subMonths(6),
            ],
            [
                'title' => 'Old Legal Memorandum',
                'category' => 'legal',
                'department' => 'Legal',
                'status' => 'expired',
                'retention_until' => Carbon::now()->subDays(15), // Expired 15 days ago
                'confidentiality_level' => 'confidential',
                'file_path' => 'test/old_memo.pdf',
                'uploaded_by' => $user->id,
                'created_at' => Carbon::now()->subYear(),
            ],
            [
                'title' => 'Monthly Report - Q1 2024',
                'category' => 'report',
                'department' => 'Finance',
                'status' => 'active',
                'retention_until' => Carbon::now()->subDays(7), // Expired 7 days ago
                'confidentiality_level' => 'internal',
                'file_path' => 'test/q1_report.pdf',
                'uploaded_by' => $user->id,
                'created_at' => Carbon::now()->subMonths(9),
            ],
            [
                'title' => 'Service Agreement - XYZ Ltd',
                'category' => 'contract',
                'department' => 'Operations',
                'status' => 'active',
                'retention_until' => Carbon::now()->addDays(20), // Expires in 20 days
                'confidentiality_level' => 'restricted',
                'file_path' => 'test/service_agreement.pdf',
                'uploaded_by' => $user->id,
                'created_at' => Carbon::now()->subMonths(3),
            ],
        ];

        foreach ($testDocuments as $docData) {
            Document::create($docData);
        }

        $this->command->info('Created ' . count($testDocuments) . ' test documents for disposal testing.');
    }
}
