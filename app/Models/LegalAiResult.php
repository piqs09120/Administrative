<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LegalAiResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'case_id',
        'report_id',
        'analysis_type',
        'ai_result',
        'document_type',
        'confidence',
        'detected_violations',
        'applicable_laws',
        'compliance_status',
        'risk_level',
        'summary',
        'policy_links',
        'recommendations',
        'ai_model',
        'processing_time',
        'metadata'
    ];

    protected $casts = [
        'ai_result' => 'array',
        'detected_violations' => 'array',
        'applicable_laws' => 'array',
        'policy_links' => 'array',
        'recommendations' => 'array',
        'metadata' => 'array',
        'confidence' => 'decimal:2',
        'processing_time' => 'decimal:3'
    ];

    /**
     * Get the document this result belongs to
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the complaint this result belongs to
     */
    public function complaint()
    {
        return $this->belongsTo(EmployeeComplaint::class, 'case_id', 'case_id');
    }

    /**
     * Get the violation report this result belongs to
     */
    public function violationReport()
    {
        return $this->belongsTo(ViolationReport::class, 'report_id', 'report_id');
    }

    /**
     * Scope for results by analysis type
     */
    public function scopeByAnalysisType(Builder $query, string $type): Builder
    {
        return $query->where('analysis_type', $type);
    }

    /**
     * Scope for results by risk level
     */
    public function scopeByRiskLevel(Builder $query, string $level): Builder
    {
        return $query->where('risk_level', $level);
    }

    /**
     * Scope for results by compliance status
     */
    public function scopeByComplianceStatus(Builder $query, string $status): Builder
    {
        return $query->where('compliance_status', $status);
    }

    /**
     * Get risk level color for display
     */
    public function getRiskLevelColorAttribute(): string
    {
        return match($this->risk_level) {
            'critical' => 'text-red-600 bg-red-100',
            'high' => 'text-orange-600 bg-orange-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'low' => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Get compliance status color for display
     */
    public function getComplianceStatusColorAttribute(): string
    {
        return match($this->compliance_status) {
            'compliant' => 'text-green-600 bg-green-100',
            'non_compliant' => 'text-red-600 bg-red-100',
            'needs_review' => 'text-yellow-600 bg-yellow-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Get confidence level description
     */
    public function getConfidenceLevelAttribute(): string
    {
        if ($this->confidence >= 90) return 'Very High';
        if ($this->confidence >= 80) return 'High';
        if ($this->confidence >= 70) return 'Medium';
        if ($this->confidence >= 60) return 'Low';
        return 'Very Low';
    }

    /**
     * Check if result requires immediate attention
     */
    public function requiresImmediateAttention(): bool
    {
        return in_array($this->risk_level, ['critical', 'high']) || 
               $this->compliance_status === 'non_compliant';
    }

    /**
     * Get formatted AI result for display
     */
    public function getFormattedResultAttribute(): array
    {
        return [
            'DocumentType' => $this->document_type,
            'Confidence' => $this->confidence . '%',
            'DetectedViolations' => $this->detected_violations ?? [],
            'ApplicableLaws' => $this->applicable_laws ?? [],
            'ComplianceStatus' => $this->compliance_status,
            'RiskLevel' => $this->risk_level,
            'Summary' => $this->summary,
            'PolicyLinks' => $this->policy_links ?? [],
            'Recommendations' => $this->recommendations ?? []
        ];
    }
}