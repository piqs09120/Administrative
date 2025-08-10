<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 
        'reserved_by', 
        'start_time', 
        'end_time', 
        'purpose', 
        'status', 
        'approved_by', 
        'remarks',
        'document_path',
        'ai_classification',
        'ai_error',
        'requires_legal_review',
        'requires_visitor_coordination',
        'visitor_data',
        'auto_approved_at',
        'legal_reviewed_by',
        'legal_reviewed_at',
        'legal_comment',
        // New workflow fields
        'requester_name',
        'requester_department',
        'requester_contact',
        'availability_checked',
        'availability_checked_at',
        'availability_conflicts',
        'workflow_stage',
        'workflow_log',
        'digital_passes_generated',
        'digital_pass_data',
        'security_notified',
        'security_notified_at'
    ];

    protected $casts = [
        'ai_classification' => 'array',
        'visitor_data' => 'array',
        'requires_legal_review' => 'boolean',
        'requires_visitor_coordination' => 'boolean',
        'auto_approved_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'legal_reviewed_at' => 'datetime',
        // New workflow casts
        'availability_checked' => 'boolean',
        'availability_checked_at' => 'datetime',
        'workflow_log' => 'array',
        'digital_passes_generated' => 'boolean',
        'digital_pass_data' => 'array',
        'security_notified' => 'boolean',
        'security_notified_at' => 'datetime'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function reserver()
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function legalReviewer()
    {
        return $this->belongsTo(User::class, 'legal_reviewed_by');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function (FacilityReservation $reservation) {
            if (!empty($reservation->document_path)) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($reservation->document_path);
                } catch (\Throwable $e) {
                    \Log::warning('Failed to delete reservation file on delete', [
                        'reservation_id' => $reservation->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });
    }

    // Helper methods for status checking
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isDenied()
    {
        return $this->status === 'denied';
    }

    public function isAutoApproved()
    {
        return !is_null($this->auto_approved_at);
    }

    // Helper method to get AI classification data
    public function getAiClassification($key = null)
    {
        if (!$this->ai_classification) {
            return null;
        }

        if ($key) {
            return $this->ai_classification[$key] ?? null;
        }

        return $this->ai_classification;
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

    public function updateWorkflowStage($stage, $message = null)
    {
        $this->update(['workflow_stage' => $stage]);
        if ($message) {
            $this->logWorkflowStep($stage, $message);
        }
    }

    public function getWorkflowStage()
    {
        return $this->workflow_stage ?? 'submitted';
    }

    public function canAutoApprove()
    {
        return $this->availability_checked && 
               !$this->requires_legal_review && 
               !$this->requires_visitor_coordination &&
               empty($this->availability_conflicts);
    }

    public function hasAvailabilityConflicts()
    {
        return !empty($this->availability_conflicts);
    }
}
