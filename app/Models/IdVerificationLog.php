<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdVerificationLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'visitor_id',
        'form_name',
        'form_email',
        'form_id_number',
        'form_id_type',
        'form_date_of_birth',
        'extracted_name',
        'extracted_id_number',
        'extracted_date_of_birth',
        'extracted_nationality',
        'extracted_raw_data',
        'parse_method',
        'extraction_confidence',
        'match_score',
        'overall_confidence',
        'verification_status',
        'quality_passed',
        'quality_metrics',
        'quality_issues',
        'component_scores',
        'mismatch_reasons',
        'philid_verified',
        'philid_verification_data',
        'id_document_path',
        'id_document_hash',
        'ip_address',
        'user_agent',
        'verified_at',
        'reviewed_by',
        'reviewed_at',
        'reviewer_notes',
        'consent_given',
        'consent_timestamp',
        'data_retention_until',
        'data_encrypted',
    ];

    protected $casts = [
        'form_date_of_birth' => 'date',
        'extracted_date_of_birth' => 'date',
        'extraction_confidence' => 'decimal:2',
        'match_score' => 'decimal:2',
        'overall_confidence' => 'decimal:2',
        'quality_passed' => 'boolean',
        'quality_metrics' => 'array',
        'quality_issues' => 'array',
        'component_scores' => 'array',
        'mismatch_reasons' => 'array',
        'philid_verified' => 'boolean',
        'philid_verification_data' => 'array',
        'verified_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'consent_given' => 'boolean',
        'consent_timestamp' => 'datetime',
        'data_retention_until' => 'datetime',
        'data_encrypted' => 'boolean',
    ];

    // Relationships
    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function qrPass()
    {
        return $this->hasOne(VisitorQrPass::class, 'verification_log_id');
    }

    // Scopes
    public function scopePendingReview($query)
    {
        return $query->where('verification_status', 'review')
                    ->whereNull('reviewed_at')
                    ->orderBy('created_at', 'asc');
    }

    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    // Accessors
    public function getStatusBadgeColorAttribute()
    {
        return match($this->verification_status) {
            'approved' => 'success',
            'review' => 'warning',
            'rejected' => 'error',
            default => 'neutral'
        };
    }

    public function getConfidenceLevelAttribute()
    {
        $confidence = $this->overall_confidence;
        if ($confidence >= 85) return 'High';
        if ($confidence >= 60) return 'Medium';
        return 'Low';
    }
}



