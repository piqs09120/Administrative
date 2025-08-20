<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'department', 'author', 'file_path', 'status', 'uploaded_by', 
        'ai_analysis', 'category', 'source', 
        'requires_legal_review', 'requires_visitor_coordination', 'legal_risk_score',
        'workflow_stage', 'workflow_log'
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'workflow_log' => 'array'
    ];

    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function documentRequests() {
        return $this->hasMany(DocumentRequest::class);
    }

    public function facilityReservations() {
        return $this->hasMany(FacilityReservation::class);
    }

    public function legalCase()
    {
        return $this->hasOne(LegalCase::class);
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
