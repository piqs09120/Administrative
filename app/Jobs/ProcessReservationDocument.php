<?php

namespace App\Jobs;

use App\Models\FacilityReservation;
use App\Jobs\CheckAndAutoApproveReservation;
use App\Services\DocumentTextExtractorService;
use App\Services\GeminiService;
use App\Services\SecureDocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessReservationDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $reservationId;

    public function __construct(int $reservationId)
    {
        $this->reservationId = $reservationId;
    }

    public function handle(DocumentTextExtractorService $textExtractor, GeminiService $gemini, SecureDocumentRepository $repository): void
    {
        $reservation = FacilityReservation::find($this->reservationId);
        if (!$reservation || empty($reservation->document_path)) {
            return;
        }

        try {
            $documentPath = storage_path('app/public/' . $reservation->document_path);
            if (!file_exists($documentPath)) {
                throw new \RuntimeException('Document file not found');
            }

            // Step 1: Extract text from document
            $content = $textExtractor->extractText($documentPath);
            $reservation->logWorkflowStep('document_text_extracted', 'Text successfully extracted from document');
            
            // Step 2: Send document to Gemini AI for classification
            $aiResult = $gemini->analyzeDocument($content);
            
            if (!($aiResult['error'] ?? false)) {
                // Step 3: Store and index uploaded document in secure repository + Log classification metadata
                $repository->logClassificationMetadata($this->reservationId, $aiResult);
                
                // Step 4: Update reservation with AI classification results
                $requiresLegal = $this->requiresLegalReview($aiResult);
                $requiresVisitor = $this->requiresVisitorCoordination($aiResult);
                
                $reservation->update([
                    'ai_classification' => $aiResult,
                    'requires_legal_review' => $requiresLegal,
                    'requires_visitor_coordination' => $requiresVisitor,
                ]);
                
                // Log the decision point from TO BE diagram
                $decision = $requiresLegal || $requiresVisitor ? 'YES' : 'NO';
                $reservation->logWorkflowStep('document_requires_validation_check', 
                    "Does document require legal validation etc? Answer: {$decision}", [
                    'requires_legal_review' => $requiresLegal,
                    'requires_visitor_coordination' => $requiresVisitor,
                    'category' => $aiResult['category'] ?? 'unknown'
                ]);
                
            } else {
                $reservation->update(['ai_error' => $aiResult['message'] ?? 'AI error']);
                $reservation->logWorkflowStep('document_classification_error', 'AI classification failed', [
                    'error' => $aiResult['message'] ?? 'Unknown AI error'
                ]);
            }

        } catch (\Throwable $e) {
            $reservation->update(['ai_error' => 'Document processing failed: ' . $e->getMessage()]);
            $reservation->logWorkflowStep('document_processing_error', 'Error processing document', [
                'error' => $e->getMessage()
            ]);
            Log::error('ProcessReservationDocument failed', [
                'reservation_id' => $this->reservationId,
                'error' => $e->getMessage(),
            ]);
        }

        // After AI processing, extract and store visitor data if found
        if (!($aiResult['error'] ?? false)) {
            $this->extractVisitorData($reservation, $aiResult);
        }

        // Update workflow stage
        $reservation->updateWorkflowStage('document_processed', 'Document analyzed by AI');

        // Decision point from TO BE diagram: Does document require legal validation etc?
        if ($reservation->requires_legal_review || $reservation->requires_visitor_coordination) {
            // YES path: Goes to legal workflow (will be handled by legal review process)
            $reservation->logWorkflowStep('legal_workflow_required', 'Document requires legal review workflow');
            // Note: Legal workflow will be triggered by admin/legal team manually
        } else {
            // NO path: Proceed to approval → Auto check facility availability → Auto approve
            $reservation->logWorkflowStep('proceed_to_approval', 'Proceeding to auto-approval workflow');
            CheckAndAutoApproveReservation::dispatch($this->reservationId);
        }
    }

    private function requiresLegalReview(array $aiResult): bool
    {
        $legalCategories = ['contract', 'subpoena', 'affidavit', 'cease_desist', 'legal_notice'];
        $category = $aiResult['category'] ?? 'general';
        return in_array($category, $legalCategories, true);
    }

    private function requiresVisitorCoordination(array $aiResult): bool
    {
        $visitorKeywords = ['visitor', 'guest', 'attendee', 'participant', 'delegate'];
        $summary = strtolower($aiResult['summary'] ?? '');
        $keyInfo = strtolower($aiResult['key_info'] ?? '');
        foreach ($visitorKeywords as $keyword) {
            if (strpos($summary, $keyword) !== false || strpos($keyInfo, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    private function extractVisitorData($reservation, $aiResult)
    {
        // Extract visitor information from AI analysis
        $visitorData = [];
        
        // Look for visitor information in AI results
        $summary = strtolower($aiResult['summary'] ?? '');
        $keyInfo = strtolower($aiResult['key_info'] ?? '');
        $entities = $aiResult['entities'] ?? [];
        
        // Basic visitor detection and extraction
        if ($this->requiresVisitorCoordination($aiResult)) {
            // Try to extract visitor names from entities
            foreach ($entities as $entity) {
                if (in_array($entity['type'] ?? '', ['PERSON', 'person', 'visitor'])) {
                    $visitorData[] = [
                        'name' => $entity['value'],
                        'company' => $this->extractCompanyForVisitor($entity['value'], $entities),
                        'access_level' => 'visitor',
                        'status' => 'pending_approval',
                        'extracted_from' => 'ai_analysis'
                    ];
                }
            }
            
            // If no specific visitors found but coordination required, create placeholder
            if (empty($visitorData)) {
                $visitorData[] = [
                    'name' => 'Visitor (Name to be confirmed)',
                    'company' => 'Company to be confirmed',
                    'access_level' => 'visitor',
                    'status' => 'pending_approval',
                    'extracted_from' => 'ai_detection'
                ];
            }
        }

        if (!empty($visitorData)) {
            $reservation->update(['visitor_data' => $visitorData]);
            $reservation->logWorkflowStep('visitor_data_extracted', 'Extracted visitor information from document', [
                'visitor_count' => count($visitorData)
            ]);
        }
    }

    private function extractCompanyForVisitor($visitorName, $entities)
    {
        // Try to find organization entities near the visitor name
        foreach ($entities as $entity) {
            if (in_array($entity['type'] ?? '', ['ORGANIZATION', 'organization', 'company'])) {
                return $entity['value'];
            }
        }
        return '';
    }
}


