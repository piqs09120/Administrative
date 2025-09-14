<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentExpirationNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class CheckDocumentExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:check-expiration {--days=7 : Number of days before expiration to notify}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for documents approaching expiration and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $expirationDate = Carbon::now()->addDays($days);
        
        $this->info("Checking for documents expiring within {$days} days...");
        
        // Find documents expiring soon
        $expiringDocuments = Document::whereNotNull('retention_until')
            ->where('retention_until', '<=', $expirationDate)
            ->where('retention_until', '>', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->get();
            
        $this->info("Found {$expiringDocuments->count()} documents expiring soon.");
        
        if ($expiringDocuments->count() > 0) {
            // Get administrators and document managers
            $notifyUsers = User::whereIn('role', ['admin', 'super_admin', 'legal_admin', 'hr_admin'])
                ->orWhere('department', 'legal')
                ->orWhere('department', 'hr')
                ->get();
                
            foreach ($expiringDocuments as $document) {
                $this->info("Processing document: {$document->title} (Expires: {$document->retention_until})");
                
                // Send notification to relevant users
                foreach ($notifyUsers as $user) {
                    $user->notify(new DocumentExpirationNotification($document, $days));
                }
                
                // Log the notification
                $document->logWorkflowStep('expiration_notification_sent', 
                    "Expiration notification sent to administrators. Document expires in {$days} days.");
            }
            
            $this->info("Notifications sent for {$expiringDocuments->count()} documents.");
        } else {
            $this->info("No documents expiring soon.");
        }
        
        // Check for expired documents that need disposal
        $expiredDocuments = Document::whereNotNull('retention_until')
            ->where('retention_until', '<=', Carbon::now())
            ->where('status', '!=', 'disposed')
            ->get();
            
        if ($expiredDocuments->count() > 0) {
            $this->info("Found {$expiredDocuments->count()} expired documents that need disposal.");
            
            foreach ($expiredDocuments as $document) {
                $this->info("Marking document for disposal: {$document->title}");
                
                // Update document status to expired
                $document->update(['status' => 'expired']);
                
                // Log the expiration
                $document->logWorkflowStep('document_expired', 
                    "Document has expired and is ready for disposal.");
            }
        }
        
        return Command::SUCCESS;
    }
}
