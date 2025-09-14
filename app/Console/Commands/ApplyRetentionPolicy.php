<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\DisposalHistory;

class ApplyRetentionPolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example: php artisan documents:apply-retention
     */
    protected $signature = 'documents:apply-retention {--dry-run : Show what would change without saving}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically delete documents when past retention_until date. Optionally dry-run.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();
        $dryRun = (bool) $this->option('dry-run');

        $candidates = Document::query()
            ->whereNotNull('retention_until')
            ->where('retention_until', '<=', $now)
            ->whereNotIn('status', ['deleted'])
            ->get();

        $deleted = 0;
        $errors = 0;
        
        foreach ($candidates as $doc) {
            $oldStatus = $doc->status ?? 'unknown';
            if ($oldStatus !== 'deleted') {
                if (!$dryRun) {
                    try {
                        // Delete file from storage if present
                        if (!empty($doc->file_path) && \Storage::disk('public')->exists($doc->file_path)) {
                            \Storage::disk('public')->delete($doc->file_path);
                        }

                        // Log the deletion action
                        $log = $doc->lifecycle_log ?? [];
                        $log[] = [
                            'step' => 'auto_deleted_expired',
                            'timestamp' => now()->toISOString(),
                            'user_id' => null,
                            'details' => [
                                'previous_status' => $oldStatus,
                                'retention_until' => optional($doc->retention_until)->toDateTimeString(),
                                'reason' => 'Document expired and automatically deleted'
                            ],
                            'ip_address' => null,
                        ];

                        // Update document before deletion to log the action
                        $doc->update(['lifecycle_log' => $log]);

                        // Create disposal history record before deleting
                        DisposalHistory::create([
                            'document_title' => $doc->title,
                            'document_description' => $doc->description,
                            'document_category' => $doc->category,
                            'document_department' => $doc->department,
                            'document_author' => $doc->author,
                            'file_path' => $doc->file_path,
                            'file_name' => basename($doc->file_path ?? ''),
                            'file_type' => pathinfo($doc->file_path ?? '', PATHINFO_EXTENSION),
                            'file_size' => $doc->file_path ? \Storage::disk('public')->size($doc->file_path) : null,
                            'confidentiality_level' => $doc->confidentiality,
                            'retention_until' => $doc->retention_until,
                            'retention_policy' => $doc->retention_policy,
                            'previous_status' => $oldStatus,
                            'disposal_reason' => 'auto_expired',
                            'disposed_at' => now(),
                            'disposed_by' => null, // System disposal
                            'lifecycle_log' => $log,
                            'ai_analysis' => $doc->ai_analysis,
                            'metadata' => $doc->metadata,
                            'ip_address' => null
                        ]);

                        // Permanently delete the document record
                        $doc->delete();
                        
                        $deleted++;
                        $this->line("[{$doc->id}] {$doc->title} -> automatically deleted (was {$oldStatus})");
                        
                    } catch (\Exception $e) {
                        $errors++;
                        $this->error("[{$doc->id}] {$doc->title} -> ERROR: " . $e->getMessage());
                    }
                } else {
                    $deleted++;
                    $this->line("[{$doc->id}] {$doc->title} -> would be automatically deleted (was {$oldStatus})");
                }
            }
        }

        if ($dryRun) {
            $this->info("DRY RUN: Would automatically delete {$deleted} document(s).");
        } else {
            $this->info("Automatically deleted {$deleted} document(s).");
            if ($errors > 0) {
                $this->warn("Encountered {$errors} error(s) during deletion.");
            }
        }
        return Command::SUCCESS;
    }
}


