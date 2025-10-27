<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ViolationReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'case_id',
        'reporter_id',
        'reporter_name',
        'reporter_department',
        'violator_id',
        'violator_name',
        'violator_department',
        'violation_description',
        'violation_type',
        'severity',
        'status',
        'assigned_to',
        'incident_details',
        'incident_date',
        'incident_location',
        'witnesses',
        'evidence_documents',
        'ai_analysis',
        'detected_violations',
        'applicable_laws',
        'investigation_notes',
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
        'evidence_documents' => 'array',
        'ai_analysis' => 'array',
        'detected_violations' => 'array',
        'applicable_laws' => 'array',
        'workflow_log' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Generate unique Report ID in format VIO-{YEAR}-{5-digit number}
     */
    public static function generateReportId(): string
    {
        $year = date('Y');
        $prefix = "VIO-{$year}-";
        
        return DB::transaction(function () use ($year, $prefix) {
            $lastReport = self::whereYear('created_at', $year)
                ->where('report_id', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('report_id', 'desc')
                ->first();
            
            if ($lastReport && $lastReport->report_id) {
                $lastNumber = (int) substr($lastReport->report_id, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $reportId = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            
            while (self::where('report_id', $reportId)->exists()) {
                $newNumber++;
                $reportId = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
            
            return $reportId;
        });
    }

    /**
     * Boot method to auto-generate report ID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($report) {
            if (empty($report->report_id)) {
                $report->report_id = self::generateReportId();
            }
        });
    }

    /**
     * Scope for reports by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for reports by severity
     */
    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for reports by department
     */
    public function scopeByDepartment(Builder $query, string $department): Builder
    {
        return $query->where('reporter_department', $department);
    }

    /**
     * Get severity color for display
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'text-red-600 bg-red-100',
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
            'reported' => 'text-blue-600 bg-blue-100',
            'under_investigation' => 'text-yellow-600 bg-yellow-100',
            'confirmed' => 'text-orange-600 bg-orange-100',
            'resolved' => 'text-green-600 bg-green-100',
            'dismissed' => 'text-gray-600 bg-gray-100',
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
        
        $this->logWorkflowStep('assigned', "Violation report assigned to {$officerName}", [
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
     * Add detected violations
     */
    public function addDetectedViolations(array $violations): void
    {
        $this->update(['detected_violations' => $violations]);
        
        $this->logWorkflowStep('violations_detected', 'Violations detected and recorded', [
            'violations' => $violations
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
     * Resolve violation report
     */
    public function resolve(string $resolvedBy, string $notes): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
            'resolution_notes' => $notes
        ]);
        
        $this->logWorkflowStep('resolved', 'Violation report resolved', [
            'resolved_by' => $resolvedBy,
            'resolution_notes' => $notes
        ]);
    }

    /**
     * Get related documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'linked_case_id', 'report_id');
    }

    /**
     * Get related complaint if linked
     */
    public function complaint()
    {
        return $this->belongsTo(EmployeeComplaint::class, 'case_id', 'case_id');
    }
}