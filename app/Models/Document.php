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
        'document_uid', 'confidentiality', 'retention_until', 'retention_policy'
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'workflow_log' => 'array',
        'lifecycle_log' => 'array',
        'legal_case_data' => 'array',
        'metadata' => 'array',
        'retention_until' => 'datetime'
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

}
