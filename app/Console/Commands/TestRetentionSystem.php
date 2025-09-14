<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;

class TestRetentionSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:test-retention {--create-test-docs : Create test documents with different retention periods}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the document retention and archival system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Document Retention and Archival System');
        $this->info('==============================================');

        if ($this->option('create-test-docs')) {
            $this->createTestDocuments();
        }

        $this->testRetentionPolicies();
        $this->testExpirationNotifications();
        $this->testArchivalProcess();
        $this->testDisposalProcess();

        $this->info('Retention system test completed!');
        return Command::SUCCESS;
    }

    /**
     * Create test documents with different retention periods
     */
    private function createTestDocuments()
    {
        $this->info('Creating test documents...');

        // Document expiring in 1 day
        Document::create([
            'title' => 'Test Document - Expires Tomorrow',
            'description' => 'Test document for retention system',
            'department' => 'IT',
            'category' => 'test',
            'file_path' => 'test/test1.pdf',
            'uploaded_by' => 1,
            'status' => 'active',
            'source' => 'document_management',
            'confidentiality' => 'internal',
            'retention_policy' => '1_year',
            'retention_until' => Carbon::now()->addDay(),
            'document_uid' => 'TEST-' . uniqid(),
            'workflow_stage' => 'uploaded'
        ]);

        // Document expiring in 7 days
        Document::create([
            'title' => 'Test Document - Expires in 7 Days',
            'description' => 'Test document for retention system',
            'department' => 'HR',
            'category' => 'test',
            'file_path' => 'test/test2.pdf',
            'uploaded_by' => 1,
            'status' => 'active',
            'source' => 'document_management',
            'confidentiality' => 'restricted',
            'retention_policy' => '6_months',
            'retention_until' => Carbon::now()->addDays(7),
            'document_uid' => 'TEST-' . uniqid(),
            'workflow_stage' => 'uploaded'
        ]);

        // Document already expired
        Document::create([
            'title' => 'Test Document - Already Expired',
            'description' => 'Test document for retention system',
            'department' => 'Legal',
            'category' => 'test',
            'file_path' => 'test/test3.pdf',
            'uploaded_by' => 1,
            'status' => 'active',
            'source' => 'document_management',
            'confidentiality' => 'public',
            'retention_policy' => '30_days',
            'retention_until' => Carbon::now()->subDays(5),
            'document_uid' => 'TEST-' . uniqid(),
            'workflow_stage' => 'uploaded'
        ]);

        // Document expiring in 30 days
        Document::create([
            'title' => 'Test Document - Expires in 30 Days',
            'description' => 'Test document for retention system',
            'department' => 'Finance',
            'category' => 'test',
            'file_path' => 'test/test4.pdf',
            'uploaded_by' => 1,
            'status' => 'active',
            'source' => 'document_management',
            'confidentiality' => 'internal',
            'retention_policy' => '3_years',
            'retention_until' => Carbon::now()->addDays(30),
            'document_uid' => 'TEST-' . uniqid(),
            'workflow_stage' => 'uploaded'
        ]);

        $this->info('✓ Created 4 test documents with different retention periods');
    }

    /**
     * Test retention policies
     */
    private function testRetentionPolicies()
    {
        $this->info('Testing Retention Policies...');

        $documents = Document::where('title', 'LIKE', 'Test Document%')->get();
        
        foreach ($documents as $document) {
            $daysUntilExpiration = Carbon::now()->diffInDays($document->retention_until, false);
            
            $this->line("Document: {$document->title}");
            $this->line("  - Retention Policy: {$document->retention_policy}");
            $this->line("  - Expires: {$document->retention_until->format('Y-m-d H:i:s')}");
            $this->line("  - Days until expiration: {$daysUntilExpiration}");
            $this->line("  - Status: {$document->status}");
            $this->line("  - Confidentiality: {$document->confidentiality}");
            $this->line('');
        }

        $this->info('✓ Retention policies analyzed');
    }

    /**
     * Test expiration notifications
     */
    private function testExpirationNotifications()
    {
        $this->info('Testing Expiration Notifications...');

        // Test 7-day notification
        $expiringIn7Days = Document::whereNotNull('retention_until')
            ->where('retention_until', '<=', Carbon::now()->addDays(7))
            ->where('retention_until', '>', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->count();

        $this->line("Documents expiring within 7 days: {$expiringIn7Days}");

        // Test 1-day notification
        $expiringIn1Day = Document::whereNotNull('retention_until')
            ->where('retention_until', '<=', Carbon::now()->addDay())
            ->where('retention_until', '>', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->count();

        $this->line("Documents expiring within 1 day: {$expiringIn1Day}");

        // Test expired documents
        $expiredDocuments = Document::whereNotNull('retention_until')
            ->where('retention_until', '<=', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->count();

        $this->line("Expired documents: {$expiredDocuments}");

        $this->info('✓ Expiration notifications tested');
    }

    /**
     * Test archival process
     */
    private function testArchivalProcess()
    {
        $this->info('Testing Archival Process...');

        // Find documents that should be archived
        $documentsToArchive = Document::whereNotNull('retention_until')
            ->where('retention_until', '<=', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->where('status', '!=', 'archived')
            ->get();

        $this->line("Documents ready for archival: {$documentsToArchive->count()}");

        foreach ($documentsToArchive as $document) {
            $this->line("  - {$document->title} (Expired: {$document->retention_until->format('Y-m-d')})");
        }

        $this->info('✓ Archival process tested');
    }

    /**
     * Test disposal process
     */
    private function testDisposalProcess()
    {
        $this->info('Testing Disposal Process...');

        // Find documents that should be disposed
        $documentsToDispose = Document::where('status', 'expired')->get();

        $this->line("Documents ready for disposal: {$documentsToDispose->count()}");

        foreach ($documentsToDispose as $document) {
            $this->line("  - {$document->title} (Status: {$document->status})");
        }

        $this->info('✓ Disposal process tested');
    }
}
