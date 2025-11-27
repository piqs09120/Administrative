<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeptAccount;
use Illuminate\Support\Facades\DB;

class LegalCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_title',
        'case_description',
        'case_type',
        'priority',
        'status',
        'workflow_stage',
        'assigned_to',
        'investigator_id',
        'created_by',
        'case_number',
        'filing_date',
        'court_date',
        'outcome',
        'amount',
        'notes',
        'investigation_notes',
        'investigation_findings',
        'resolution_decision',
        'resolution_notes',
        'disciplinary_actions',
        'preventive_measures',
        'linked_case_id',
        'employee_involved',
        'incident_date',
        'incident_location',
        'metadata',
        'investigation_started_at',
        'investigation_completed_at',
        'resolved_at',
        'stage_changed_at',
        'days_in_current_stage',
        'source',
        'visitor_id',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'court_date' => 'date',
        'incident_date' => 'datetime',
        'investigation_started_at' => 'datetime',
        'investigation_completed_at' => 'datetime',
        'resolved_at' => 'datetime',
        'stage_changed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user assigned to this case
     */
    public function assignedTo()
    {
        return $this->belongsTo(DeptAccount::class, 'assigned_to', 'Dept_no');
    }

    /**
     * Get the user who created this case
     */
    public function createdBy()
    {
        return $this->belongsTo(DeptAccount::class, 'created_by', 'Dept_no');
    }

    /**
     * Get documents associated with this case
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'linked_case_id');
    }

    /**
     * Get activities for this case
     */
    public function activities()
    {
        return $this->hasMany(CaseActivity::class, 'legal_case_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get evidence for this case
     */
    public function evidence()
    {
        return $this->hasMany(CaseEvidence::class, 'legal_case_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get witnesses for this case
     */
    public function witnesses()
    {
        return $this->hasMany(CaseWitness::class, 'legal_case_id');
    }

    /**
     * Get the investigator assigned to this case
     */
    public function investigator()
    {
        return $this->belongsTo(DeptAccount::class, 'investigator_id', 'Dept_no');
    }

    /**
     * Get the visitor associated with this case (if created from visitor violation)
     */
    public function visitor()
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }

    /**
     * Get the priority color for display
     */
    public function getPriorityColorAttribute()
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
     * Get the status color for display
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'text-yellow-600 bg-yellow-100',
            'ongoing' => 'text-blue-600 bg-blue-100',
            'completed' => 'text-green-600 bg-green-100',
            'rejected' => 'text-red-600 bg-red-100',
            'active' => 'text-blue-600 bg-blue-100',
            'on_hold' => 'text-orange-600 bg-orange-100',
            'closed' => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Get workflow stage color for display
     */
    public function getWorkflowStageColorAttribute()
    {
        return match($this->workflow_stage) {
            'filing' => 'bg-blue-100 text-blue-800',
            'investigation' => 'bg-orange-100 text-orange-800',
            'review' => 'bg-purple-100 text-purple-800',
            'resolution' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get workflow stage icon
     */
    public function getWorkflowStageIconAttribute()
    {
        return match($this->workflow_stage) {
            'filing' => 'file-text',
            'investigation' => 'search',
            'review' => 'clipboard-check',
            'resolution' => 'check-circle',
            'closed' => 'archive',
            default => 'help-circle'
        };
    }

    /**
     * Check if case can transition to next stage
     */
    public function canTransitionTo($newStage)
    {
        $allowedTransitions = [
            'filing' => ['investigation'],
            'investigation' => ['review'],
            'review' => ['resolution'],
            'resolution' => ['closed'],
            'closed' => [],
        ];

        return in_array($newStage, $allowedTransitions[$this->workflow_stage] ?? []);
    }

    /**
     * Transition to new workflow stage
     */
    public function transitionTo($newStage, $notes = null)
    {
        if (!$this->canTransitionTo($newStage)) {
            return false;
        }

        $oldStage = $this->workflow_stage;
        $this->workflow_stage = $newStage;
        $this->stage_changed_at = now();
        $this->days_in_current_stage = 0;

        // Stage-specific updates
        if ($newStage === 'investigation' && !$this->investigation_started_at) {
            $this->investigation_started_at = now();
        } elseif ($newStage === 'resolution' && !$this->investigation_completed_at) {
            $this->investigation_completed_at = now();
        } elseif ($newStage === 'closed' && !$this->resolved_at) {
            $this->resolved_at = now();
        }

        $this->save();

        // Log activity
        CaseActivity::log(
            $this->id,
            'stage_changed',
            "Case moved from {$oldStage} to {$newStage}",
            ['old_stage' => $oldStage, 'new_stage' => $newStage, 'notes' => $notes]
        );

        return true;
    }

    /**
     * Calculate days in current stage
     */
    public function updateDaysInStage()
    {
        if ($this->stage_changed_at) {
            $this->days_in_current_stage = $this->stage_changed_at->diffInDays(now());
            $this->save();
        }
    }

    /**
     * Generate case number with proper locking to prevent duplicates
     */
    public static function generateCaseNumber()
    {
        $year = date('Y');
        $prefix = "LC-{$year}-";
        
        // Use database transaction with locking to prevent race conditions
        return DB::transaction(function () use ($year, $prefix) {
            // Lock the table to prevent concurrent access
            $lastCase = self::whereYear('created_at', $year)
                ->where('case_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('case_number', 'desc')
                ->first();
            
            if ($lastCase && $lastCase->case_number) {
                $lastNumber = (int) substr($lastCase->case_number, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $caseNumber = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            
            // Double-check that the case number doesn't already exist
            while (self::where('case_number', $caseNumber)->exists()) {
                $newNumber++;
                $caseNumber = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
            
            return $caseNumber;
        });
    }

    /**
     * Boot method to auto-generate case number and set initial workflow stage
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($legalCase) {
            if (empty($legalCase->case_number)) {
                $legalCase->case_number = self::generateCaseNumber();
            }
            
            // Set initial workflow stage if not set
            if (empty($legalCase->workflow_stage)) {
                $legalCase->workflow_stage = 'filing';
            }
            
            // Set stage_changed_at to current time
            if (empty($legalCase->stage_changed_at)) {
                $legalCase->stage_changed_at = now();
            }
        });

        // Log initial creation
        static::created(function ($legalCase) {
            CaseActivity::log(
                $legalCase->id,
                'case_created',
                "Case created: {$legalCase->case_title}",
                ['case_number' => $legalCase->case_number]
            );
        });
    }
}
