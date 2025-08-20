<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReservationTask;

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
        'document_id',
        'ai_error',
        'visitor_data',
        'auto_approved_at',
        // New workflow fields
        'requester_name',
        'requester_contact',
        'workflow_stage',
        'workflow_log',
        'current_workflow_status'
    ];

    protected $casts = [
        // 'ai_classification' => 'array',
        'visitor_data' => 'array',
        'auto_approved_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        // New workflow casts
        'workflow_log' => 'array',
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
        // This relationship is no longer directly used for task-based legal review
        return $this->belongsTo(User::class, 'legal_reviewed_by');
    }

    public function tasks()
    {
        return $this->hasMany(ReservationTask::class);
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'facility_reservation_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
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
            // Delete associated tasks when a reservation is deleted
            $reservation->tasks()->delete();
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
        $documentTask = $this->tasks()->where('task_type', 'document_classification')->first();
        if (!$documentTask || !isset($documentTask->details['ai_classification'])) {
            return null;
        }

        $aiClassification = $documentTask->details['ai_classification'];

        if ($key) {
            return $aiClassification[$key] ?? null;
        }

        return $aiClassification;
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
               $this->areAllRequiredTasksComplete() &&
               empty($this->availability_conflicts);
    }

    public function hasAvailabilityConflicts()
    {
        return !empty($this->availability_conflicts);
    }

    public function areAllRequiredTasksComplete(): bool
    {
        // Check if the document classification task exists and is completed
        $documentTask = $this->tasks()->where('task_type', 'document_classification')->first();
        if (!$documentTask || $documentTask->status !== 'completed') {
            return false; // Document classification is a prerequisite
        }

        // Get AI classification from the document task details, not directly from reservation
        $aiResult = $documentTask->details['ai_classification'] ?? [];

        // Define which tasks are "required" based on AI analysis
        $requiredTaskTypes = [];
        if ($aiResult['requires_legal_review'] ?? false) {
            $requiredTaskTypes[] = 'legal_review';
        }
        if ($aiResult['requires_visitor_coordination'] ?? false) {
            $requiredTaskTypes[] = 'visitor_coordination';
        }

        // Check if all these dynamically required tasks are completed
        foreach ($requiredTaskTypes as $taskType) {
            $task = $this->tasks()->where('task_type', $taskType)->first();
            if (!$task || $task->status !== 'completed') {
                return false; // A required task is pending or missing
            }
        }

        return true; // All required tasks are present and completed
    }
}
