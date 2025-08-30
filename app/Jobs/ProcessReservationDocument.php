<?php

namespace App\Jobs;

use App\Models\FacilityReservation;
use App\Jobs\CheckAndAutoApproveReservation;
use App\Services\DocumentTextExtractorService;
use App\Services\GeminiService;
use App\Services\SecureDocumentRepository;
use App\Services\VisitorService;
use App\Services\ReservationWorkflowService;
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

    public function handle(DocumentTextExtractorService $textExtractor, GeminiService $gemini, SecureDocumentRepository $repository, VisitorService $visitorService, ReservationWorkflowService $workflowService): void
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
                // Use the new AI analysis fields for routing decisions
                
                // Create document classification task and subsequent tasks
                $workflowService->createDocumentClassificationTask($reservation, $aiResult);

                // Log the routing decision based on AI analysis
                $this->logRoutingDecision($reservation, $aiResult);
                
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

        // After AI processing, extract and store visitor data if found (now handled by workflow service implicitly when creating visitor task)
        
        // Update workflow stage (now handled by workflow service implicitly)

        // Decision point from TO BE diagram: Does document require legal validation etc?
        // This logic is now handled by ReservationWorkflowService::updateReservationOverallStatus
    }

    private function logRoutingDecision(FacilityReservation $reservation, array $aiResult): void
    {
        $requiresLegalReview = $aiResult['requires_legal_review'] ?? false;
        $requiresVisitorCoordination = $aiResult['requires_visitor_coordination'] ?? false;
        $legalRiskScore = $aiResult['legal_risk_score'] ?? 'Low';
        
        $routingInfo = [
            'category' => $aiResult['category'] ?? 'general',
            'legal_review_required' => $requiresLegalReview,
            'visitor_coordination_required' => $requiresVisitorCoordination,
            'legal_risk_score' => $legalRiskScore
        ];
        
        $reservation->logWorkflowStep('ai_routing_decision', 'AI analysis routing decision made', $routingInfo);
        
        Log::info('Document routing decision', [
            'reservation_id' => $reservation->id,
            'routing_info' => $routingInfo
        ]);
    }
}


