<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LegalAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_type',
        'timestamp',
        'user_id',
        'user_name',
        'user_role',
        'module',
        'entity_type',
        'entity_id',
        'ai_result',
        'next_steps',
        'description',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'ai_result' => 'array',
        'next_steps' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Scope for logs by action type
     */
    public function scopeByActionType(Builder $query, string $actionType): Builder
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope for logs by user
     */
    public function scopeByUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for logs by entity
     */
    public function scopeByEntity(Builder $query, string $entityType, string $entityId): Builder
    {
        return $query->where('entity_type', $entityType)
                    ->where('entity_id', $entityId);
    }

    /**
     * Scope for logs by date range
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('timestamp', [$startDate, $endDate]);
    }

    /**
     * Scope for AI-related actions
     */
    public function scopeAiActions(Builder $query): Builder
    {
        return $query->whereIn('action_type', [
            'ai_analysis',
            'ai_classification',
            'ai_violation_detection',
            'ai_compliance_check'
        ]);
    }

    /**
     * Create audit log entry
     */
    public static function createLog(
        string $actionType,
        string $userId,
        string $userName,
        string $userRole,
        string $description = null,
        string $entityType = null,
        string $entityId = null,
        array $aiResult = null,
        array $nextSteps = null,
        array $metadata = null
    ): self {
        return self::create([
            'action_type' => $actionType,
            'timestamp' => now(),
            'user_id' => $userId,
            'user_name' => $userName,
            'user_role' => $userRole,
            'module' => 'Legal Management',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ai_result' => $aiResult,
            'next_steps' => $nextSteps,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata
        ]);
    }

    /**
     * Log document upload
     */
    public static function logDocumentUpload(
        string $userId,
        string $userName,
        string $userRole,
        string $documentId,
        string $documentTitle,
        array $aiResult = null
    ): self {
        return self::createLog(
            'document_upload',
            $userId,
            $userName,
            $userRole,
            "Document '{$documentTitle}' uploaded and analyzed",
            'document',
            $documentId,
            $aiResult,
            ['Review AI analysis results', 'Check compliance status', 'Assign to legal officer if needed'],
            ['document_title' => $documentTitle]
        );
    }

    /**
     * Log complaint filing
     */
    public static function logComplaintFiling(
        string $userId,
        string $userName,
        string $userRole,
        string $complaintId,
        string $complaintType,
        array $aiResult = null
    ): self {
        return self::createLog(
            'complaint_filed',
            $userId,
            $userName,
            $userRole,
            "Complaint of type '{$complaintType}' filed",
            'complaint',
            $complaintId,
            $aiResult,
            ['Assign to legal officer', 'Begin investigation', 'Notify complainant'],
            ['complaint_type' => $complaintType]
        );
    }

    /**
     * Log violation report
     */
    public static function logViolationReport(
        string $userId,
        string $userName,
        string $userRole,
        string $reportId,
        string $violationType,
        array $aiResult = null
    ): self {
        return self::createLog(
            'violation_reported',
            $userId,
            $userName,
            $userRole,
            "Violation report of type '{$violationType}' submitted",
            'violation_report',
            $reportId,
            $aiResult,
            ['Assign to legal officer', 'Begin investigation', 'Gather evidence'],
            ['violation_type' => $violationType]
        );
    }

    /**
     * Log AI analysis
     */
    public static function logAiAnalysis(
        string $userId,
        string $userName,
        string $userRole,
        string $entityType,
        string $entityId,
        string $analysisType,
        array $aiResult,
        array $nextSteps = null
    ): self {
        return self::createLog(
            'ai_analysis',
            $userId,
            $userName,
            $userRole,
            "AI analysis completed for {$analysisType}",
            $entityType,
            $entityId,
            $aiResult,
            $nextSteps,
            ['analysis_type' => $analysisType]
        );
    }

    /**
     * Get action type color for display
     */
    public function getActionTypeColorAttribute(): string
    {
        return match($this->action_type) {
            'document_upload' => 'text-blue-600 bg-blue-100',
            'complaint_filed' => 'text-orange-600 bg-orange-100',
            'violation_reported' => 'text-red-600 bg-red-100',
            'ai_analysis' => 'text-purple-600 bg-purple-100',
            'ai_classification' => 'text-indigo-600 bg-indigo-100',
            'ai_violation_detection' => 'text-red-600 bg-red-100',
            'ai_compliance_check' => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimestampAttribute(): string
    {
        return $this->timestamp->format('M d, Y H:i:s');
    }
}