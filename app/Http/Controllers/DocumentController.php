<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\FacilityReservation;
use App\Models\AccessLog;
use App\Services\GeminiService;
use App\Services\DocumentTextExtractorService;
use App\Jobs\ProcessReservationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    protected $textExtractor;
    protected $geminiService;

    public function __construct(DocumentTextExtractorService $textExtractor, GeminiService $geminiService)
    {
        $this->textExtractor = $textExtractor;
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $documents = Document::with('uploader')
            ->latest()
            ->get();
        return view('document.index', compact('documents'));
    }

    public function create()
    {
        return view('document.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png|max:10240'
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Step 1: Auto-sanitize and format file for NLP input using DocumentTextExtractorService
        $documentText = $this->textExtractor->extractText($file->getRealPath());
        
        if (empty($documentText) || $documentText === "Unknown document type: " . $file->getClientOriginalExtension()) {
            return redirect()->back()->with('error', 'Could not extract text from document. Please ensure the file is readable.');
        }

        // Step 2: Classify document using Gemini AI legal text analysis
        $category = $request->ai_category ?: $request->category;
        $aiAnalysis = null;
        
        if ($documentText) {
            try {
                // Include original filename to boost classification when text is sparse
                $textForAi = trim($documentText . "\n\nFILENAME: " . $file->getClientOriginalName());
                $aiAnalysis = $this->geminiService->analyzeDocument($textForAi);
                
                if (!$aiAnalysis['error']) {
                    $category = $aiAnalysis['category'];
                }
            } catch (\Exception $e) {
                Log::error('Gemini AI analysis failed: ' . $e->getMessage());
                $aiAnalysis = null;
            }
        }

        // Step 3: Create document with AI analysis
        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'department' => $request->department,
            'category' => $category,
            'author' => $request->author,
            'file_path' => $filePath,
            'uploaded_by' => Auth::id(),
            'status' => 'archived', // enum: archived|pending_release|released
            'source' => 'document_management',
            'requires_legal_review' => $aiAnalysis['requires_legal_review'] ?? false,
            'requires_visitor_coordination' => $aiAnalysis['requires_visitor_coordination'] ?? false,
            'legal_risk_score' => $aiAnalysis['legal_risk_score'] ?? 'Low',
            'workflow_stage' => 'processing'
        ]);

        // Store AI analysis data
        if ($aiAnalysis && !$aiAnalysis['error']) {
            $document->update([
                'ai_analysis' => $aiAnalysis
            ]);
        }

        // Step 4: Route document based on AI classification
        $this->routeDocument($document, $aiAnalysis);

        // Log document upload and routing
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'document_uploaded_and_routed',
            'description' => 'Document uploaded and routed based on AI analysis. Category: ' . $category . ', Legal Review: ' . ($aiAnalysis['requires_legal_review'] ? 'Yes' : 'No') . ', Visitor Coordination: ' . ($aiAnalysis['requires_visitor_coordination'] ? 'Yes' : 'No'),
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('document.show', $document->id)
            ->with('success', 'Document uploaded, analyzed, and routed successfully!');
    }

    private function routeDocument(Document $document, $aiAnalysis)
    {
        if (!$aiAnalysis || $aiAnalysis['error']) {
            // If AI analysis failed, keep archived status and mark stage
            $document->update(['workflow_stage' => 'ai_failed']);
            $document->logWorkflowStep('ai_analysis_failed', 'AI analysis failed, document archived');
            return;
        }

        $category = $aiAnalysis['category'] ?? 'general';
        $requiresLegalReview = $aiAnalysis['requires_legal_review'] ?? false;
        $requiresVisitorCoordination = $aiAnalysis['requires_visitor_coordination'] ?? false;

        // Route to Facility Reservations (FR) module
        if ($this->isFacilityReservationDocument($category, $aiAnalysis)) {
            $this->routeToFacilityReservations($document, $aiAnalysis);
        }
        // Route to Visitor Management (VM) module
        elseif ($requiresVisitorCoordination) {
            $this->routeToVisitorManagement($document, $aiAnalysis);
        }
        // Route to Legal Management (LM) module
        elseif ($requiresLegalReview) {
            $this->routeToLegalManagement($document, $aiAnalysis);
        }
        // Archive non-actionable documents
        else {
            $this->archiveDocument($document, $aiAnalysis);
        }

        // Mark DM lifecycle complete after routing decision
        $this->markLifecycleCompleted($document);
    }

    private function isFacilityReservationDocument($category, $aiAnalysis)
    {
        // Check if document contains facility reservation keywords
        $text = strtolower($aiAnalysis['summary'] ?? '') . ' ' . strtolower($aiAnalysis['key_info'] ?? '');
        $facilityKeywords = ['facility', 'room', 'conference', 'meeting', 'reservation', 'booking', 'schedule', 'venue'];
        
        foreach ($facilityKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    private function routeToFacilityReservations(Document $document, $aiAnalysis)
    {
        $document->update(['workflow_stage' => 'routed_fr']);
        
        // Try to parse a date/time window from AI text
        $textForDates = trim(($aiAnalysis['summary'] ?? '') . ' ' . ($aiAnalysis['key_info'] ?? ''));
        [$startAt, $endAt] = $this->parseDateRangeFromText($textForDates);
        if (!$startAt) {
            $startAt = now()->addDay();
        }
        if (!$endAt) {
            $endAt = (clone $startAt)->addHour();
        }

        // Create a facility reservation record
        $reservation = FacilityReservation::create([
            'facility_id' => 1, // Default facility - will be updated by user
            'reserved_by' => Auth::id(),
            'start_time' => $startAt,
            'end_time' => $endAt,
            'purpose' => $aiAnalysis['summary'] ?? 'Document-based reservation',
            'status' => 'pending',
            'requester_name' => Auth::user()->name,
            'requester_contact' => Auth::user()->email,
            'workflow_stage' => 'document_processing',
            'document_id' => $document->id
        ]);

        // Link document to reservation
        $document->update(['workflow_stage' => 'linked_reservation']);

        // Dispatch job to process the reservation document
        ProcessReservationDocument::dispatch($reservation->id);

        // Log routing action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'document_routed_to_fr',
            'description' => 'Document routed to Facility Reservations module. Reservation ID: ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);

        $document->logWorkflowStep('routed_to_fr', 'Document routed to Facility Reservations module');
    }

    private function routeToVisitorManagement(Document $document, $aiAnalysis)
    {
        $document->update(['workflow_stage' => 'routed_vm']);
        
        // Log routing action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'document_routed_to_vm',
            'description' => 'Document routed to Visitor Management module for visitor coordination',
            'ip_address' => request()->ip()
        ]);

        $document->logWorkflowStep('routed_to_vm', 'Document routed to Visitor Management module');
    }

    private function routeToLegalManagement(Document $document, $aiAnalysis)
    {
        $document->update(['workflow_stage' => 'routed_lm']);
        
        // Log routing action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'document_routed_to_lm',
            'description' => 'Document routed to Legal Management module. Risk Score: ' . ($aiAnalysis['legal_risk_score'] ?? 'Unknown'),
            'ip_address' => request()->ip()
        ]);

        $document->logWorkflowStep('routed_to_lm', 'Document routed to Legal Management module');

        // Simulate creation of a legal memo/case record by logging into workflow
        $document->logWorkflowStep('lm_case_opened', 'Legal case/memo created for document', [
            'risk' => $aiAnalysis['legal_risk_score'] ?? 'Low',
            'requires_legal_review' => $aiAnalysis['requires_legal_review'] ?? false,
        ]);

        // Persist a simple LegalCase record
        try {
            \App\Models\LegalCase::firstOrCreate(
                ['document_id' => $document->id],
                [
                    'title' => 'Case for: ' . ($document->title ?? ('Document #' . $document->id)),
                    'status' => 'open',
                    'risk_score' => $aiAnalysis['legal_risk_score'] ?? 'Low',
                    'requires_legal_review' => $aiAnalysis['requires_legal_review'] ?? false,
                    'memo' => $aiAnalysis['summary'] ?? null,
                    'created_by' => auth()->id(),
                ]
            );
        } catch (\Throwable $e) {
            // Log but do not interrupt routing
            \Log::warning('Failed creating LegalCase record', ['document_id' => $document->id, 'error' => $e->getMessage()]);
        }
    }

    private function archiveDocument(Document $document, $aiAnalysis)
    {
        $document->update(['workflow_stage' => 'archived']);
        
        // Log archiving action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'document_archived',
            'description' => 'Document archived as non-actionable. Category: ' . ($aiAnalysis['category'] ?? 'Unknown'),
            'ip_address' => request()->ip()
        ]);

        $document->logWorkflowStep('archived', 'Document archived as non-actionable');
    }

    private function markLifecycleCompleted(Document $document): void
    {
        $document->update(['workflow_stage' => 'lifecycle_completed']);
        $document->logWorkflowStep('dm_lifecycle_completed', 'Document Management lifecycle completed');
    }

    private function parseDateRangeFromText(string $text): array
    {
        // Attempt to parse common date formats using Carbon's parsing
        // Returns [startAt, endAt] (Carbon instances or null)
        $start = null;
        $end = null;

        // Pattern: 2025-08-21 14:00 to 16:00
        if (preg_match('/(\d{4}-\d{2}-\d{2})\s+(\d{1,2}:\d{2})\s*(?:to|-|–|until)\s*(\d{1,2}:\d{2})/i', $text, $m)) {
            try {
                $start = \Carbon\Carbon::parse($m[1] . ' ' . $m[2]);
                $end = \Carbon\Carbon::parse($m[1] . ' ' . $m[3]);
                return [$start, $end];
            } catch (\Throwable $e) {}
        }

        // Pattern: Jan 12, 2025 2:00 PM - 4:00 PM
        if (preg_match('/([A-Za-z]{3,9}\s+\d{1,2},\s*\d{4})\s+(\d{1,2}:\d{2}\s*(?:AM|PM))\s*(?:to|-|–|until)\s*(\d{1,2}:\d{2}\s*(?:AM|PM))/i', $text, $m)) {
            try {
                $start = \Carbon\Carbon::parse($m[1] . ' ' . $m[2]);
                $end = \Carbon\Carbon::parse($m[1] . ' ' . $m[3]);
                return [$start, $end];
            } catch (\Throwable $e) {}
        }

        // Fallback: find any datetime mention
        if (preg_match('/(\d{4}-\d{2}-\d{2}[ T]\d{1,2}:\d{2}(?::\d{2})?)/', $text, $m)) {
            try { $start = \Carbon\Carbon::parse($m[1]); } catch (\Throwable $e) {}
        } elseif (preg_match('/([A-Za-z]{3,9}\s+\d{1,2},\s*\d{4}\s+\d{1,2}:\d{2}\s*(?:AM|PM))/i', $text, $m)) {
            try { $start = \Carbon\Carbon::parse($m[1]); } catch (\Throwable $e) {}
        }

        return [$start, $end];
    }

    public function show($id)
    {
        $document = Document::with(['uploader', 'documentRequests.requester', 'documentRequests.approver'])
            ->where('source', 'document_management')
            ->findOrFail($id);
        return view('document.show', compact('document'));
    }

    public function edit($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        return view('document.edit', compact('document'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $document = Document::where('source', 'document_management')->findOrFail($id);
        $document->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return redirect()->route('document.show', $id)->with('success', 'Document updated successfully!');
    }

    public function destroy($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        return redirect()->route('document.index')->with('success', 'Document deleted successfully!');
    }

    public function requestRelease($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        
        // Check if document is archived
        if ($document->status !== 'archived') {
            return redirect()->back()->with('error', 'Document is not available for release request.');
        }

        // Check if there's already a pending request
        $existingRequest = DocumentRequest::where('document_id', $id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'A release request is already pending for this document.');
        }

        DocumentRequest::create([
            'document_id' => $id,
            'requested_by' => Auth::id(),
            'status' => 'pending'
        ]);

        $document->update(['status' => 'pending_release']);

        return redirect()->route('document.show', $id)->with('success', 'Release request submitted successfully!');
    }

    public function download($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download(storage_path('app/public/' . $document->file_path), $document->title . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
    }

    public function analyze($id)
    {
        $document = Document::where('source', 'document_management')->findOrFail($id);
        
        // Extract text from document using DocumentTextExtractorService
        $filePath = storage_path('app/public/' . $document->file_path);
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $documentText = $this->textExtractor->extractText($filePath);

        if (!$documentText) {
            return redirect()->back()->with('error', 'Could not extract text from document.');
        }

        try {
            // Append filename to improve classification for image/scanned PDFs
            $textForAi = trim($documentText . "\n\nFILENAME: " . basename($filePath));
            $aiAnalysis = $this->geminiService->analyzeDocument($textForAi);
            
            if ($aiAnalysis['error']) {
                return redirect()->back()->with('error', 'AI analysis failed: ' . $aiAnalysis['message']);
            }

            // Update document with AI analysis
            $document->update([
                'ai_analysis' => $aiAnalysis,
                'category' => $aiAnalysis['category'],
                'requires_legal_review' => $aiAnalysis['requires_legal_review'] ?? false,
                'requires_visitor_coordination' => $aiAnalysis['requires_visitor_coordination'] ?? false,
                'legal_risk_score' => $aiAnalysis['legal_risk_score'] ?? 'Low'
            ]);

            // Re-route document based on new analysis
            $this->routeDocument($document, $aiAnalysis);

            return redirect()->route('document.show', $id)->with('success', 'Document analyzed and re-routed successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'AI analysis failed: ' . $e->getMessage());
        }
    }

    public function analyzeUpload(Request $request)
    {
        // Accept any file up to 10MB; we'll always attempt a best-effort analysis
        $request->validate([
            'document_file' => 'required|file|max:10240'
        ]);

        $file = $request->file('document_file');
        
        // Extract text (returns placeholders for unsupported types too)
        $documentText = $this->textExtractor->extractText($file->getRealPath());

        // Ensure we always have some text to classify
        if (empty($documentText)) {
            $documentText = 'general document content';
        }

        try {
            // Always include original filename to help the classifier (e.g., "Affidavit", "Contract")
            $textForAi = trim($documentText . "\n\nFILENAME: " . $file->getClientOriginalName());
            $aiAnalysis = $this->geminiService->analyzeDocument($textForAi);
            
            // Guarantee a result: if remote analysis fails, use local fallback
            if (isset($aiAnalysis['error']) && $aiAnalysis['error']) {
                $aiAnalysis = app(\App\Services\GeminiService::class)->fallbackAnalysis($documentText);
            }

            return response()->json([
                'success' => true,
                'analysis' => $aiAnalysis
            ]);
        } catch (\Throwable $e) {
            $aiAnalysis = app(\App\Services\GeminiService::class)->fallbackAnalysis($documentText);
            return response()->json([
                'success' => true,
                'analysis' => $aiAnalysis,
                'fallback' => true
            ]);
        }
    }
} 