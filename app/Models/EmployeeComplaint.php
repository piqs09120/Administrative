<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EmployeeComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'complainant_id',
        'complainant_name',
        'complainant_department',
        'complainant_email',
        'complainant_contact',
        'complaint_description',
        'complaint_type',
        'priority',
        'status',
        'assigned_to',
        'incident_details',
        'incident_date',
        'incident_location',
        'witnesses',
        'supporting_documents',
        'ai_analysis',
        'applicable_laws',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
        'workflow_log',
        'metadata'
    ];

    protected $casts = [
        'incident_date' => 'date',
        'resolved_at' => 'datetime',
        'witnesses' => 'array',
        'supporting_documents' => 'array',
        'ai_analysis' => 'array',
        'applicable_laws' => 'array',
        'workflow_log' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Generate unique Case ID in format CASE-{YEAR}-{5-digit number}
     */
    public static function generateCaseId(): string
    {
        $year = date('Y');
        $prefix = "CASE-{$year}-";
        
        return DB::transaction(function () use ($year, $prefix) {
            // Lock the table to prevent concurrent access
            $lastCase = self::whereYear('created_at', $year)
                ->where('case_id', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('case_id', 'desc')
                ->first();
            
            if ($lastCase && $lastCase->case_id) {
                $lastNumber = (int) substr($lastCase->case_id, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $caseId = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            
            // Double-check that the case ID doesn't already exist
            while (self::where('case_id', $caseId)->exists()) {
                $newNumber++;
                $caseId = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
            
            return $caseId;
        });
    }

    /**
     * Boot method to auto-generate case ID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($complaint) {
            if (empty($complaint->case_id)) {
                $complaint->case_id = self::generateCaseId();
            }
        });
    }

    /**
     * Scope for complaints by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for complaints by priority
     */
    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for complaints by department
     */
    public function scopeByDepartment(Builder $query, string $department): Builder
    {
        return $query->where('complainant_department', $department);
    }

    /**
     * Scope for assigned complaints
     */
    public function scopeAssignedTo(Builder $query, string $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Get priority color for display
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'text-red-600 bg-red-100',
            'high' => 'text-orange-600 bg-orange-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'low' => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'submitted' => 'text-blue-600 bg-blue-100',
            'under_review' => 'text-yellow-600 bg-yellow-100',
            'investigation' => 'text-orange-600 bg-orange-100',
            'resolved' => 'text-green-600 bg-green-100',
            'dismissed' => 'text-gray-600 bg-gray-100',
            'escalated' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Log workflow step
     */
    public function logWorkflowStep(string $step, string $message, array $data = []): void
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

    /**
     * Update status and log workflow
     */
    public function updateStatus(string $status, string $message = null): void
    {
        $oldStatus = $this->status;
        $this->update(['status' => $status]);
        
        $this->logWorkflowStep('status_change', $message ?? "Status changed from {$oldStatus} to {$status}", [
            'old_status' => $oldStatus,
            'new_status' => $status
        ]);
    }

    /**
     * Assign to legal officer
     */
    public function assignTo(string $userId, string $officerName): void
    {
        $this->update(['assigned_to' => $userId]);
        
        $this->logWorkflowStep('assigned', "Complaint assigned to {$officerName}", [
            'assigned_to' => $userId,
            'assigned_to_name' => $officerName
        ]);
    }

    /**
     * Add AI analysis results
     */
    public function addAiAnalysis(array $analysis): void
    {
        $this->update(['ai_analysis' => $analysis]);
        
        $this->logWorkflowStep('ai_analysis', 'AI analysis completed', [
            'analysis' => $analysis
        ]);
    }

    /**
     * Add applicable laws
     */
    public function addApplicableLaws(array $laws): void
    {
        $this->update(['applicable_laws' => $laws]);
        
        $this->logWorkflowStep('laws_identified', 'Applicable laws identified', [
            'laws' => $laws
        ]);
    }

    /**
     * Resolve complaint
     */
    public function resolve(string $resolvedBy, string $notes): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
            'resolution_notes' => $notes
        ]);
        
        $this->logWorkflowStep('resolved', 'Complaint resolved', [
            'resolved_by' => $resolvedBy,
            'resolution_notes' => $notes
        ]);
    }

    /**
     * Get related documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'linked_case_id', 'case_id');
    }
}