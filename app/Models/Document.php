<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'department', 'author', 'file_path', 'status', 'uploaded_by', 
        'ai_analysis', 'category', 'source', 'extracted_text', 'metadata',
        'requires_legal_review', 'requires_visitor_coordination', 'legal_risk_score',
        'workflow_stage', 'workflow_log', 'lifecycle_log', 'legal_case_data', 'linked_reservation_id',
        'linked_case_id',
        // DMS-only metadata
        'document_uid', 'confidentiality', 'retention_until', 'retention_policy',
        // Legal document retention policy fields
        'archived_at', 'disposal_date', 'retention_years', 'can_dispose', 'disposal_reason',
        // Collaboration and history fields
        'editing_history', 'collaborators', 'last_edited_by', 'last_edited_at', 'version',
        'access_log', 'download_count', 'view_count'
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'workflow_log' => 'array',
        'lifecycle_log' => 'array',
        'legal_case_data' => 'array',
        'metadata' => 'array',
        'retention_until' => 'datetime',
        'archived_at' => 'datetime',
        'disposal_date' => 'datetime',
        'can_dispose' => 'boolean',
        'editing_history' => 'array',
        'collaborators' => 'array',
        'access_log' => 'array',
        'last_edited_at' => 'datetime',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'version' => 'integer'
    ];

    public function uploader() {
        // Try to find DeptAccount first (for department users)
        if (is_numeric($this->uploaded_by)) {
            // If uploaded_by is numeric, it's likely a User ID
            return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
        } else {
            // If uploaded_by is a string, it's likely a Dept_no
            return $this->belongsTo(DeptAccount::class, 'uploaded_by', 'Dept_no');
        }
    }

    /**
     * Get uploader name with fallback
     */
    public function getUploaderNameAttribute() {
        try {
            if ($this->uploader) {
                if ($this->uploader instanceof \App\Models\User) {
                    return $this->uploader->name ?? 'User ' . $this->uploader->id;
                } else {
                    return $this->uploader->name ?? $this->uploader->employee_name ?? 'Unknown';
                }
            }
        } catch (\Exception $e) {
            // If relationship fails, return a fallback
        }
        return 'Unknown';
    }

    public function documentRequests() {
        return $this->hasMany(DocumentRequest::class);
    }

    public function facilityReservations() {
        return $this->hasMany(FacilityReservation::class);
    }

    // Workflow helper methods
    public function logWorkflowStep($step, $message, $data = [])
    {
        $log = $this->workflow_log ?? [];
        $log[] = [
            'step' => $step,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id()
        ];
        
        $this->update(['workflow_log' => $log]);
    }

    public function getWorkflowStage()
    {
        return $this->workflow_stage ?? 'uploaded';
    }

    /**
     * Archive document with retention policy (No Deletion, Archive Only)
     */
    public function archiveWithRetention($retentionYears = null, $reason = 'Administrative archive')
    {
        $retentionYears = $retentionYears ?? $this->getDefaultRetentionYears();
        $disposalDate = now()->addYears($retentionYears);
        
        $this->update([
            'status' => 'archived',
            'archived_at' => now(),
            'retention_years' => $retentionYears,
            'disposal_date' => $disposalDate,
            'can_dispose' => false,
            'disposal_reason' => $reason
        ]);

        $this->logWorkflowStep('archived_with_retention', 'Document archived with retention policy', [
            'retention_years' => $retentionYears,
            'disposal_date' => $disposalDate->toISOString(),
            'reason' => $reason
        ]);
    }

    /**
     * Get default retention years based on document category
     */
    public function getDefaultRetentionYears()
    {
        return match($this->category) {
            'contract' => 7,
            'policy' => 5,
            'legal_case' => 10,
            'compliance' => 6,
            'financial' => 7,
            default => 5
        };
    }

    /**
     * Check if document can be disposed (past retention period)
     */
    public function canBeDisposed()
    {
        return $this->status === 'archived' && 
               $this->disposal_date && 
               $this->disposal_date <= now() &&
               !$this->can_dispose;
    }

    /**
     * Mark document as ready for disposal
     */
    public function markForDisposal($reason = 'Retention period expired')
    {
        if (!$this->canBeDisposed()) {
            return false;
        }

        $this->update([
            'can_dispose' => true,
            'disposal_reason' => $reason
        ]);

        $this->logWorkflowStep('marked_for_disposal', 'Document marked for disposal', [
            'disposal_date' => $this->disposal_date->toISOString(),
            'reason' => $reason
        ]);

        return true;
    }

    /**
     * Scope for archived documents
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope for documents ready for disposal
     */
    public function scopeReadyForDisposal($query)
    {
        return $query->where('status', 'archived')
                    ->where('can_dispose', true);
    }

    /**
     * Scope for documents expiring soon (within 30 days)
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('status', 'archived')
                    ->where('disposal_date', '<=', now()->addDays(30))
                    ->where('can_dispose', false);
    }

    /**
     * Collaboration and History Methods
     */
    
    /**
     * Add editing history entry
     */
    public function addEditingHistory($action, $description, $userId = null, $data = [])
    {
        $history = $this->editing_history ?? [];
        $history[] = [
            'action' => $action,
            'description' => $description,
            'user_id' => $userId ?? auth()->id(),
            'user_name' => auth()->user()->name ?? 'System',
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];
        
        $this->update([
            'editing_history' => $history,
            'last_edited_by' => $userId ?? auth()->id(),
            'last_edited_at' => now(),
            'version' => ($this->version ?? 0) + 1
        ]);
    }

    /**
     * Add collaborator
     */
    public function addCollaborator($userId, $role = 'viewer')
    {
        $collaborators = $this->collaborators ?? [];
        $collaborators[] = [
            'user_id' => $userId,
            'role' => $role, // viewer, editor, reviewer
            'added_at' => now()->toISOString(),
            'added_by' => auth()->id()
        ];
        
        $this->update(['collaborators' => $collaborators]);
    }

    /**
     * Log document access
     */
    public function logAccess($action = 'view', $userId = null)
    {
        $accessLog = $this->access_log ?? [];
        $accessLog[] = [
            'action' => $action, // view, download, edit
            'user_id' => $userId ?? auth()->id(),
            'user_name' => auth()->user()->name ?? 'System',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ];
        
        // Update counters
        $updates = ['access_log' => $accessLog];
        if ($action === 'view') {
            $updates['view_count'] = ($this->view_count ?? 0) + 1;
        } elseif ($action === 'download') {
            $updates['download_count'] = ($this->download_count ?? 0) + 1;
        }
        
        $this->update($updates);
    }

    /**
     * Get document collaborators
     */
    public function getCollaborators()
    {
        return $this->collaborators ?? [];
    }

    /**
     * Get editing history
     */
    public function getEditingHistory()
    {
        return $this->editing_history ?? [];
    }

    /**
     * Get access log
     */
    public function getAccessLog()
    {
        return $this->access_log ?? [];
    }

    /**
     * Check if user can edit document
     */
    public function canUserEdit($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        // Document owner can always edit
        if ($this->uploaded_by == $userId) {
            return true;
        }
        
        // Check if user is a collaborator with edit role
        $collaborators = $this->getCollaborators();
        foreach ($collaborators as $collaborator) {
            if ($collaborator['user_id'] == $userId && in_array($collaborator['role'], ['editor', 'reviewer'])) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get document statistics
     */
    public function getDocumentStats()
    {
        return [
            'view_count' => $this->view_count ?? 0,
            'download_count' => $this->download_count ?? 0,
            'version' => $this->version ?? 1,
            'collaborators_count' => count($this->getCollaborators()),
            'editing_entries' => count($this->getEditingHistory()),
            'access_entries' => count($this->getAccessLog())
        ];
    }

    /**
     * Scopes for organized sorting
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByAuthor($query, $author)
    {
        return $query->where('author', 'like', '%' . $author . '%');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByConfidentiality($query, $confidentiality)
    {
        return $query->where('confidentiality', $confidentiality);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeMostViewed($query, $limit = 10)
    {
        return $query->orderBy('view_count', 'desc')->limit($limit);
    }

    public function scopeMostDownloaded($query, $limit = 10)
    {
        return $query->orderBy('download_count', 'desc')->limit($limit);
    }

    public function scopeRecentlyEdited($query, $limit = 10)
    {
        return $query->orderBy('last_edited_at', 'desc')->limit($limit);
    }

}
