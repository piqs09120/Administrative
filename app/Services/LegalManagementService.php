<?php

namespace App\Services;

use App\Models\Document;
use App\Models\CompanyPolicy;
use App\Models\EmployeeComplaint;
use App\Models\ViolationReport;
use App\Models\LegalAiResult;
use App\Models\LegalAuditLog;
use App\Services\GeminiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LegalManagementService
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Process document with AI analysis and policy linking
     */
    public function processDocument(Document $document)
    {
        try {
            // Get document text
            $text = $document->extracted_text ?? $document->description ?? '';
            
            if (empty($text)) {
                Log::warning('No text content available for AI analysis', [
                    'document_id' => $document->id,
                    'title' => $document->title
                ]);
                return false;
            }

            // Perform AI analysis
            $aiResult = $this->geminiService->analyzeDocumentEnhanced($text);
            
            // Store AI result
            $legalAiResult = LegalAiResult::create([
                'document_id' => $document->id,
                'analysis_type' => 'document_classification',
                'ai_result' => $aiResult,
                'document_type' => $aiResult['ai_classification'] ?? 'Unknown',
                'confidence' => $aiResult['ai_confidence'] ?? 0,
                'detected_violations' => $aiResult['detected_violations'] ?? [],
                'applicable_laws' => $aiResult['applicable_laws'] ?? [],
                'compliance_status' => $aiResult['compliance_status'] ?? 'needs_review',
                'risk_level' => $this->determineRiskLevel($aiResult),
                'summary' => $aiResult['ai_insights'] ?? '',
                'policy_links' => $this->findRelatedPolicies($text),
                'recommendations' => $aiResult['recommendations'] ?? [],
                'ai_model' => 'gemini-pro',
                'processing_time' => microtime(true) - LARAVEL_START,
                'metadata' => [
                    'analysis_date' => now()->toISOString(),
                    'document_title' => $document->title,
                    'document_category' => $document->category
                ]
            ]);

            // Update document with AI results
            $document->update([
                'ai_classification' => $aiResult['ai_classification'] ?? 'Unknown',
                'ai_confidence' => $aiResult['ai_confidence'] ?? 0,
                'violation_score' => $aiResult['violation_score'] ?? 0,
                'violation_details' => $aiResult['violation_details'] ?? '',
                'flagged_issues' => $aiResult['flagged_issues'] ?? '',
                'compliance_status' => $aiResult['compliance_status'] ?? 'needs_review',
                'compliance_details' => $aiResult['compliance_details'] ?? '',
                'regulatory_standards' => $aiResult['regulatory_standards'] ?? '',
                'ai_tags' => $aiResult['ai_tags'] ?? '',
                'ai_insights' => $aiResult['ai_insights'] ?? '',
                'requires_immediate_review' => $aiResult['requires_immediate_review'] ?? false,
                'alert_reasons' => $aiResult['alert_reasons'] ?? '',
                'ai_analysis_completed' => true,
                'ai_analysis_date' => now()->toISOString()
            ]);

            // Log audit trail
            LegalAuditLog::logDocumentUpload(
                auth()->id(),
                auth()->user()->name ?? 'System',
                auth()->user()->role ?? 'User',
                $document->id,
                $document->title,
                $aiResult
            );

            return $legalAiResult;

        } catch (\Exception $e) {
            Log::error('Error processing document with AI', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process employee complaint
     */
    public function processComplaint(array $complaintData)
    {
        try {
            DB::beginTransaction();

            // Create complaint
            $complaint = EmployeeComplaint::create([
                'complainant_id' => $complaintData['complainant_id'],
                'complainant_name' => $complaintData['complainant_name'],
                'complainant_department' => $complaintData['complainant_department'],
                'complainant_email' => $complaintData['complainant_email'],
                'complainant_contact' => $complaintData['complainant_contact'],
                'complaint_description' => $complaintData['complaint_description'],
                'complaint_type' => $complaintData['complaint_type'],
                'priority' => $complaintData['priority'] ?? 'medium',
                'incident_details' => $complaintData['incident_details'] ?? '',
                'incident_date' => $complaintData['incident_date'] ?? now()->toDateString(),
                'incident_location' => $complaintData['incident_location'] ?? '',
                'witnesses' => $complaintData['witnesses'] ?? [],
                'supporting_documents' => $complaintData['supporting_documents'] ?? []
            ]);

            // Perform AI analysis
            $aiAnalysis = $this->geminiService->analyzeComplaint(
                $complaint->complaint_description,
                $complaint->complaint_type
            );

            // Update complaint with AI analysis
            $complaint->addAiAnalysis($aiAnalysis);
            $complaint->addApplicableLaws($aiAnalysis['ApplicableLaws'] ?? []);

            // Store AI result
            LegalAiResult::create([
                'case_id' => $complaint->case_id,
                'analysis_type' => 'complaint_analysis',
                'ai_result' => $aiAnalysis,
                'document_type' => 'Complaint',
                'confidence' => 85, // Default confidence for complaint analysis
                'detected_violations' => $aiAnalysis['DetectedViolations'] ?? [],
                'applicable_laws' => $aiAnalysis['ApplicableLaws'] ?? [],
                'compliance_status' => 'needs_review',
                'risk_level' => $aiAnalysis['Severity'] ?? 'medium',
                'summary' => $aiAnalysis['Summary'] ?? '',
                'recommendations' => $aiAnalysis['RecommendedActions'] ?? [],
                'ai_model' => 'gemini-pro',
                'metadata' => [
                    'complaint_type' => $complaint->complaint_type,
                    'complainant_department' => $complaint->complainant_department
                ]
            ]);

            // Log audit trail
            LegalAuditLog::logComplaintFiling(
                auth()->id(),
                auth()->user()->name ?? 'System',
                auth()->user()->role ?? 'User',
                $complaint->case_id,
                $complaint->complaint_type,
                $aiAnalysis
            );

            DB::commit();
            return $complaint;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing complaint', [
                'complaint_data' => $complaintData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process violation report
     */
    public function processViolationReport(array $reportData)
    {
        try {
            DB::beginTransaction();

            // Create violation report
            $report = ViolationReport::create([
                'case_id' => $reportData['case_id'] ?? null,
                'reporter_id' => $reportData['reporter_id'],
                'reporter_name' => $reportData['reporter_name'],
                'reporter_department' => $reportData['reporter_department'],
                'violator_id' => $reportData['violator_id'] ?? null,
                'violator_name' => $reportData['violator_name'] ?? null,
                'violator_department' => $reportData['violator_department'] ?? null,
                'violation_description' => $reportData['violation_description'],
                'violation_type' => $reportData['violation_type'],
                'severity' => $reportData['severity'] ?? 'medium',
                'incident_details' => $reportData['incident_details'],
                'incident_date' => $reportData['incident_date'],
                'incident_location' => $reportData['incident_location'],
                'witnesses' => $reportData['witnesses'] ?? [],
                'evidence_documents' => $reportData['evidence_documents'] ?? []
            ]);

            // Perform AI analysis
            $aiAnalysis = $this->geminiService->analyzeComplaint(
                $report->violation_description,
                $report->violation_type
            );

            // Update report with AI analysis
            $report->addAiAnalysis($aiAnalysis);
            $report->addDetectedViolations($aiAnalysis['DetectedViolations'] ?? []);
            $report->addApplicableLaws($aiAnalysis['ApplicableLaws'] ?? []);

            // Store AI result
            LegalAiResult::create([
                'report_id' => $report->report_id,
                'analysis_type' => 'violation_analysis',
                'ai_result' => $aiAnalysis,
                'document_type' => 'Violation Report',
                'confidence' => 90, // High confidence for violation analysis
                'detected_violations' => $aiAnalysis['DetectedViolations'] ?? [],
                'applicable_laws' => $aiAnalysis['ApplicableLaws'] ?? [],
                'compliance_status' => 'non_compliant',
                'risk_level' => $aiAnalysis['Severity'] ?? 'high',
                'summary' => $aiAnalysis['Summary'] ?? '',
                'recommendations' => $aiAnalysis['RecommendedActions'] ?? [],
                'ai_model' => 'gemini-pro',
                'metadata' => [
                    'violation_type' => $report->violation_type,
                    'severity' => $report->severity,
                    'reporter_department' => $report->reporter_department
                ]
            ]);

            // Log audit trail
            LegalAuditLog::logViolationReport(
                auth()->id(),
                auth()->user()->name ?? 'System',
                auth()->user()->role ?? 'User',
                $report->report_id,
                $report->violation_type,
                $aiAnalysis
            );

            DB::commit();
            return $report;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing violation report', [
                'report_data' => $reportData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Find related company policies
     */
    public function findRelatedPolicies(string $text)
    {
        $policies = CompanyPolicy::active()
            ->get()
            ->filter(function ($policy) use ($text) {
                // Check if any keywords match
                foreach ($policy->keywords ?? [] as $keyword) {
                    if (stripos($text, $keyword) !== false) {
                        return true;
                    }
                }
                
                // Check title and description
                if (stripos($text, $policy->title) !== false || 
                    stripos($text, $policy->description) !== false) {
                    return true;
                }
                
                return false;
            })
            ->map(function ($policy) {
                return [
                    'id' => $policy->id,
                    'policy_code' => $policy->policy_code,
                    'title' => $policy->title,
                    'category' => $policy->category
                ];
            })
            ->toArray();

        return $policies;
    }

    /**
     * Determine risk level from AI result
     */
    private function determineRiskLevel(array $aiResult)
    {
        $violationScore = $aiResult['violation_score'] ?? 0;
        $complianceStatus = $aiResult['compliance_status'] ?? 'needs_review';
        $requiresReview = $aiResult['requires_immediate_review'] ?? false;

        if ($requiresReview || $violationScore >= 80 || $complianceStatus === 'non_compliant') {
            return 'critical';
        } elseif ($violationScore >= 60 || $complianceStatus === 'needs_review') {
            return 'high';
        } elseif ($violationScore >= 30) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        return [
            'total_documents' => Document::where('source', 'legal_management')->count(),
            'high_risk_documents' => Document::where('source', 'legal_management')
                ->where('requires_immediate_review', true)
                ->count(),
            'violations' => ViolationReport::where('status', '!=', 'dismissed')->count(),
            'complaints' => EmployeeComplaint::where('status', '!=', 'dismissed')->count(),
            'non_compliant' => Document::where('source', 'legal_management')
                ->where('compliance_status', 'non_compliant')
                ->count(),
            'pending_reviews' => Document::where('source', 'legal_management')
                ->where('compliance_status', 'needs_review')
                ->count(),
            'ai_analyses' => LegalAiResult::count(),
            'policies' => CompanyPolicy::active()->count()
        ];
    }

    /**
     * Archive document (no deletion)
     */
    public function archiveDocument(Document $document, string $reason = 'Administrative archive')
    {
        try {
            $document->archiveWithRetention(null, $reason);
            
            // Log audit trail
            LegalAuditLog::createLog(
                'document_archived',
                auth()->id(),
                auth()->user()->name ?? 'System',
                auth()->user()->role ?? 'User',
                "Document '{$document->title}' archived: {$reason}",
                'document',
                $document->id
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Error archiving document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get high-risk documents requiring immediate attention
     */
    public function getHighRiskDocuments()
    {
        return Document::where('source', 'legal_management')
            ->where('requires_immediate_review', true)
            ->with(['uploader'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent AI analyses
     */
    public function getRecentAiAnalyses($limit = 10)
    {
        return LegalAiResult::with(['document', 'complaint', 'violationReport'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
