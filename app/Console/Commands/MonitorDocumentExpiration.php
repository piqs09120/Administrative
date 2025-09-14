<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentExpirationNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class MonitorDocumentExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:monitor-expiration {--days=30 : Number of days to monitor ahead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor document expiration deadlines and send alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $this->info("Monitoring document expiration for the next {$days} days...");

        $this->checkExpiringDocuments($days);
        $this->checkOverdueDocuments();
        $this->checkRetentionCompliance();
        $this->generateExpirationReport($days);

        $this->info('Document expiration monitoring completed!');
        return Command::SUCCESS;
    }

    /**
     * Check for documents expiring within specified days
     */
    private function checkExpiringDocuments($days)
    {
        $this->info("Checking documents expiring within {$days} days...");

        $expirationDate = Carbon::now()->addDays($days);
        
        $expiringDocuments = Document::whereNotNull('retention_until')
            ->where('retention_until', '<=', $expirationDate)
            ->where('retention_until', '>', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->where('status', '!=', 'archived')
            ->orderBy('retention_until')
            ->get();

        $this->line("Found {$expiringDocuments->count()} documents expiring soon:");

        foreach ($expiringDocuments as $document) {
            $daysUntilExpiration = Carbon::now()->diffInDays($document->retention_until, false);
            
            $this->line("  - {$document->title}");
            $this->line("    Department: {$document->department}");
            $this->line("    Confidentiality: {$document->confidentiality}");
            $this->line("    Expires: {$document->retention_until->format('Y-m-d H:i:s')} ({$daysUntilExpiration} days)");
            $this->line("    Status: {$document->status}");
            $this->line("    Document UID: {$document->document_uid}");
            $this->line('');
        }

        // Send notifications for critical documents (expiring within 7 days)
        $criticalDocuments = $expiringDocuments->where('retention_until', '<=', Carbon::now()->addDays(7));
        
        if ($criticalDocuments->count() > 0) {
            $this->info("Sending notifications for {$criticalDocuments->count()} critical documents...");
            $this->sendExpirationNotifications($criticalDocuments);
        }
    }

    /**
     * Check for overdue documents
     */
    private function checkOverdueDocuments()
    {
        $this->info('Checking for overdue documents...');

        $overdueDocuments = Document::whereNotNull('retention_until')
            ->where('retention_until', '<', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->where('status', '!=', 'archived')
            ->orderBy('retention_until')
            ->get();

        $this->line("Found {$overdueDocuments->count()} overdue documents:");

        foreach ($overdueDocuments as $document) {
            $daysOverdue = Carbon::now()->diffInDays($document->retention_until, false);
            
            $this->line("  - {$document->title}");
            $this->line("    Department: {$document->department}");
            $this->line("    Was due: {$document->retention_until->format('Y-m-d H:i:s')} ({$daysOverdue} days ago)");
            $this->line("    Status: {$document->status}");
            $this->line('');
        }

        // Mark overdue documents as expired
        if ($overdueDocuments->count() > 0) {
            $this->info("Marking {$overdueDocuments->count()} overdue documents as expired...");
            
            foreach ($overdueDocuments as $document) {
                $document->update(['status' => 'expired']);
                $document->logWorkflowStep('document_expired', 'Document expired and marked for disposal', [
                    'expired_at' => now()->toISOString(),
                    'days_overdue' => Carbon::now()->diffInDays($document->retention_until, false)
                ]);
            }
        }
    }

    /**
     * Check retention compliance
     */
    private function checkRetentionCompliance()
    {
        $this->info('Checking retention compliance...');

        $totalDocuments = Document::whereNotNull('retention_until')->count();
        $compliantDocuments = Document::whereNotNull('retention_until')
            ->where(function($query) {
                $query->where('status', 'disposed')
                      ->orWhere('retention_until', '>', Carbon::now());
            })
            ->count();

        $complianceRate = $totalDocuments > 0 ? round(($compliantDocuments / $totalDocuments) * 100, 2) : 100;

        $this->line("Total documents with retention policies: {$totalDocuments}");
        $this->line("Compliant documents: {$compliantDocuments}");
        $this->line("Compliance rate: {$complianceRate}%");

        if ($complianceRate < 95) {
            $this->warn("⚠️  Retention compliance is below 95%!");
        } else {
            $this->info("✓ Retention compliance is good");
        }
    }

    /**
     * Generate expiration report
     */
    private function generateExpirationReport($days)
    {
        $this->info('Generating expiration report...');

        $report = [
            'total_documents' => Document::count(),
            'documents_with_retention' => Document::whereNotNull('retention_until')->count(),
            'expiring_within_days' => Document::whereNotNull('retention_until')
                ->where('retention_until', '<=', Carbon::now()->addDays($days))
                ->where('retention_until', '>', Carbon::now())
                ->where('status', '!=', 'disposed')
                ->count(),
            'overdue_documents' => Document::whereNotNull('retention_until')
                ->where('retention_until', '<', Carbon::now())
                ->where('status', '!=', 'disposed')
                ->where('status', '!=', 'archived')
                ->count(),
            'archived_documents' => Document::where('status', 'archived')->count(),
            'disposed_documents' => Document::where('status', 'disposed')->count(),
            'expired_documents' => Document::where('status', 'expired')->count(),
        ];

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Documents', $report['total_documents']],
                ['Documents with Retention Policy', $report['documents_with_retention']],
                ["Expiring within {$days} days", $report['expiring_within_days']],
                ['Overdue Documents', $report['overdue_documents']],
                ['Archived Documents', $report['archived_documents']],
                ['Disposed Documents', $report['disposed_documents']],
                ['Expired Documents', $report['expired_documents']],
            ]
        );
    }

    /**
     * Send expiration notifications
     */
    private function sendExpirationNotifications($documents)
    {
        // Get administrators and document managers
        $notifyUsers = User::whereIn('role', ['admin', 'super_admin', 'legal_admin', 'hr_admin'])
            ->orWhere('department', 'legal')
            ->orWhere('department', 'hr')
            ->get();

        foreach ($documents as $document) {
            $daysUntilExpiration = Carbon::now()->diffInDays($document->retention_until, false);
            
            foreach ($notifyUsers as $user) {
                $user->notify(new DocumentExpirationNotification($document, $daysUntilExpiration));
            }
            
            // Log the notification
            $document->logWorkflowStep('expiration_notification_sent', 
                "Expiration notification sent to administrators. Document expires in {$daysUntilExpiration} days.");
        }

        $this->info("✓ Sent notifications for {$documents->count()} documents to " . $notifyUsers->count() . " users");
    }
}
