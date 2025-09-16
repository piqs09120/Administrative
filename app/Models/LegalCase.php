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
        'assigned_to',
        'created_by',
        'case_number',
        'filing_date',
        'court_date',
        'outcome',
        'notes',
        'linked_case_id',
        'employee_involved',
        'incident_date',
        'incident_location',
        'metadata',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'court_date' => 'date',
        'incident_date' => 'datetime',
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
     * Boot method to auto-generate case number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($legalCase) {
            if (empty($legalCase->case_number)) {
                $legalCase->case_number = self::generateCaseNumber();
            }
        });
    }
}
