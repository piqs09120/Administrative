<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\FacilityReservation;
use App\Models\AccessLog;
use App\Models\DisposalHistory;
use App\Services\GeminiService;
use App\Services\DocumentTextExtractorService;
use App\Services\DocumentWorkflowNotificationService;
use App\Jobs\ProcessReservationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    protected $textExtractor;
    protected $geminiService;
    protected $notificationService;

    public function __construct(DocumentTextExtractorService $textExtractor, GeminiService $geminiService, DocumentWorkflowNotificationService $notificationService)
    {
        $this->textExtractor = $textExtractor;
        $this->geminiService = $geminiService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        // Exclude legal documents from general document management
        $documents = Document::with('uploader')
            ->whereNotIn('source', ['legal_management', 'legal_submission', 'ai_builder'])
            ->latest()
            ->get();
        return view('document.index', compact('documents'));
    }

    public function view()
    {
        // Get all documents for the table view
        $documents = Document::with('uploader')
            ->whereNotIn('source', ['legal_management', 'legal_submission', 'ai_builder'])
            ->latest()
            ->paginate(20);

        return view('document.view', compact('documents'));
    }

    /**
     * Basic reports view for DMS
     */
    public function reports()
    {
        $documents = Document::whereNotIn('source', ['legal_management','legal_submission','ai_builder'])->get();
        $byDepartment = $documents->groupBy('department')->map->count();
        $byStatus = $documents->groupBy('status')->map->count();
        return view('document.reports', compact('byDepartment','byStatus'));
    }

    /**
     * Document receiving interface for DMS
     */
    public function receive()
    {
        $documents = Document::whereNotIn('source', ['legal_management','legal_submission','ai_builder'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('document.receive', compact('documents'));
    }

    public function create()
    {
        return view('document.create');
    }

    public function store(Request $request)
    {
        // Enhanced debugging for authentication issues
        \Log::info('Document upload request received', [
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_ip' => request()->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'auth_check' => Auth::check(),
            'has_csrf_token' => $request->hasHeader('X-CSRF-TOKEN'),
            'csrf_token' => $request->header('X-CSRF-TOKEN'),
            'request_headers' => $request->headers->all(),
            'cookies' => $request->cookies->all()
        ]);

        // Enhanced authentication check for all document types
        if (!Auth::check()) {
            \Log::warning('Authentication failed - Auth::check() returned false', [
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
                'request_ip' => request()->ip(),
                'current_guard' => Auth::getDefaultDriver(),
                'all_guards' => array_keys(config('auth.guards'))
            ]);
            
            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to upload documents.',
                    'debug_info' => [
                        'auth_check' => false,
                        'session_id' => session()->getId(),
                        'has_session' => session()->has('_token'),
                        'current_guard' => Auth::getDefaultDriver()
                    ]
                ]);
            }
            
            return redirect()->route('login')->with('error', 'Authentication required to upload documents.');
        }

        // Get authenticated user with enhanced validation
        $user = Auth::user();
        
        \Log::info('User object retrieved', [
            'user_class' => get_class($user),
            'user_id' => $user ? $user->id : 'null',
            'user_properties' => $user ? array_keys($user->toArray()) : 'null',
            'auth_check' => Auth::check(),
            'session_id' => session()->getId(),
            'current_guard' => Auth::getDefaultDriver(),
            'user_model_class' => config('auth.providers.users.model')
        ]);
        
        // Ensure we have a valid user ID - be more flexible about user types
        if (!$user) {
            \Log::error('No user object retrieved from Auth::user()', [
                'auth_check' => Auth::check(),
                'session_id' => session()->getId(),
                'request_ip' => request()->ip(),
                'current_guard' => Auth::getDefaultDriver()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication error: No user found. Please log in again.',
                    'debug_info' => [
                        'auth_check' => Auth::check(),
                        'user_exists' => false,
                        'session_id' => session()->getId(),
                        'current_guard' => Auth::getDefaultDriver()
                    ]
                ]);
            }
            
            return redirect()->route('login')->with('error', 'Authentication error: No user found. Please log in again.');
        }
        
        // Check if user has an ID field (be flexible about the field name)
        $userId = null;
        if (isset($user->id)) {
            $userId = $user->id;
        } elseif (isset($user->Dept_no)) {
            $userId = $user->Dept_no;
        } elseif (isset($user->user_id)) {
            $userId = $user->user_id;
        }
        
        if (empty($userId)) {
            \Log::error('User object has no identifiable ID field', [
                'user_class' => get_class($user),
                'user_properties' => array_keys($user->toArray()),
                'auth_check' => Auth::check(),
                'session_id' => session()->getId(),
                'current_guard' => Auth::getDefaultDriver()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication error: Invalid user ID. Please log in again.',
                    'debug_info' => [
                        'auth_check' => Auth::check(),
                        'user_exists' => true,
                        'user_class' => get_class($user),
                        'user_properties' => array_keys($user->toArray()),
                        'session_id' => session()->getId(),
                        'current_guard' => Auth::getDefaultDriver()
                    ]
                ]);
            }
            
            return redirect()->back()->with('error', 'Authentication error: Invalid user ID. Please log in again.');
        }
        
        // Debug logging
        \Log::info('Document upload attempt', [
            'user_id' => $userId,
            'user_type' => get_class($user),
            'user_properties' => $user->toArray(),
            'request_source' => $request->source,
            'has_file' => $request->hasFile('document_file'),
            'auth_check' => Auth::check(),
            'session_id' => session()->getId(),
            'current_guard' => Auth::getDefaultDriver()
        ]);
        
        // Check user authorization for document upload
        if (!$this->isUserAuthorizedForUpload($user)) {
            AccessLog::create([
                'user_id' => $userId,
                'action' => 'unauthorized_upload_attempt',
                'description' => 'User attempted document upload without proper authorization',
                'ip_address' => request()->ip()
            ]);
            
            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied: You are not authorized to upload documents.'
                ]);
            }
            
            return redirect()->back()->with('error', 'Access Denied: You are not authorized to upload documents.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            // DMS metadata
            'confidentiality' => 'nullable|string|in:public,internal,restricted',
            'retention_policy' => 'nullable|string|in:none,30_days,6_months,1_year,3_years,custom',
            'retention_until' => 'nullable|date',
            'source' => 'nullable|string|in:document_management,legal_management,visitor_management,facility_management',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png|max:10240'
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Ensure uploaded_by is never null
        $uploadedBy = $userId;
        if (empty($uploadedBy)) {
            \Log::error('Critical: User ID is empty during document creation', [
                'user' => $user->toArray(),
                'auth_check' => Auth::check(),
                'session_id' => session()->getId(),
                'current_guard' => Auth::getDefaultDriver()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Critical error: User authentication failed. Please log in again.'
                ]);
            }
            
            return redirect()->back()->with('error', 'Critical error: User authentication failed. Please log in again.');
        }

        // Step 1: Create initial document record for tracking
        try {
            \Log::info('Creating document record', [
                'title' => $request->title,
                'uploaded_by' => $uploadedBy,
                'user_id' => $userId,
                'file_path' => $filePath
            ]);
            
            $document = Document::create([
                'title' => $request->title,
                'description' => $request->description,
                'department' => $request->department,
                'category' => $request->category ?? 'general', // Use AI-determined category if available
                'file_path' => $filePath,
                'uploaded_by' => $uploadedBy, // Use the validated user ID
                'status' => 'active',
                'source' => $request->source ?? 'document_management',
                'workflow_stage' => 'uploaded',
                'workflow_log' => [],
                'lifecycle_log' => [],
                // DMS fields
                'document_uid' => 'DOC-' . strtoupper(uniqid()),
                'confidentiality' => $request->input('confidentiality', 'internal'),
                'retention_until' => $request->input('retention_until'),
                'retention_policy' => $request->input('retention_policy')
            ]);
            
            \Log::info('Document record created successfully', [
                'document_id' => $document->id,
                'uploaded_by' => $document->uploaded_by,
                'title' => $document->title
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Document creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => $userId,
                'uploaded_by' => $uploadedBy,
                'auth_check' => Auth::check(),
                'current_guard' => Auth::getDefaultDriver()
            ]);
            
            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating document: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()->with('error', 'Error creating document: ' . $e->getMessage());
        }

        // Log the initial upload step
        $this->logDocumentLifecycleStep($document, 'uploaded', [
            'user_id' => $userId ?? 'unknown',
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType()
        ]);

        // Step 2: Perform AI Analysis for automatic classification
        try {
            // Extract text content for AI analysis
            $extractedText = $this->textExtractor->extractText($file);
            
            \Log::info('Document text extraction completed', [
                'document_id' => $document->id,
                'text_length' => strlen($extractedText),
                'has_text' => !empty($extractedText),
                'text_preview' => substr($extractedText, 0, 500),
                'text_ends_with_tmp' => str_ends_with($extractedText, 'tmp'),
                'text_contains_unknown' => str_contains($extractedText, 'Unknown document type')
            ]);
            
            // Validate extracted text before sending to AI
            if ($this->isValidExtractedText($extractedText)) {
                $document->update(['extracted_text' => $extractedText]);
                
                // Perform AI analysis using Gemini
                \Log::info('Starting Gemini AI analysis', [
                    'document_id' => $document->id,
                    'text_length' => strlen($extractedText),
                    'text_preview' => substr($extractedText, 0, 200)
                ]);
                
                $aiAnalysis = $this->geminiService->analyzeDocument($extractedText);
                
                \Log::info('Gemini AI analysis response received', [
                    'document_id' => $document->id,
                    'ai_response' => $aiAnalysis,
                    'has_error' => isset($aiAnalysis['error'])
                ]);
                
                if ($aiAnalysis && !isset($aiAnalysis['error'])) {
                    // Extract category from AI analysis
                    $aiCategory = $aiAnalysis['category'] ?? $aiAnalysis['CATEGORY'] ?? 'general';
                    
                    \Log::info('AI analysis successful, updating document', [
                        'document_id' => $document->id,
                        'category' => $aiCategory,
                        'ai_analysis_keys' => array_keys($aiAnalysis)
                    ]);
                    
                    // Update document with AI analysis results
                    $document->update([
                        'ai_analysis' => $aiAnalysis,
                        'category' => $aiCategory, // Update category based on AI analysis
                        'requires_legal_review' => $aiAnalysis['requires_legal_review'] ?? $aiAnalysis['LEGAL_REVIEW_REQUIRED'] === 'YES',
                        'requires_visitor_coordination' => $aiAnalysis['requires_visitor_coordination'] ?? $aiAnalysis['VISITOR_COORDINATION_REQUIRED'] === 'YES',
                        'legal_risk_score' => $aiAnalysis['legal_risk_score'] ?? $aiAnalysis['LEGAL_RISK_SCORE'] ?? 'Low'
                    ]);
                    
                    $this->logDocumentLifecycleStep($document, 'ai_analysis_completed', [
                        'analysis_type' => 'gemini_ai',
                        'category' => $aiCategory,
                        'risk_score' => $aiAnalysis['legal_risk_score'] ?? $aiAnalysis['LEGAL_RISK_SCORE'] ?? 'Low',
                        'requires_legal_review' => $aiAnalysis['requires_legal_review'] ?? $aiAnalysis['LEGAL_REVIEW_REQUIRED'] === 'YES'
                    ]);
                    
                    \Log::info('AI analysis completed successfully', [
                        'document_id' => $document->id,
                        'category' => $aiCategory,
                        'ai_analysis' => $aiAnalysis
                    ]);
                } else {
                    // Fallback if AI analysis fails
                    \Log::warning('AI analysis failed, using fallback', [
                        'document_id' => $document->id,
                        'error' => $aiAnalysis['error'] ?? 'Unknown error',
                        'ai_response' => $aiAnalysis
                    ]);
                    
                    $this->logDocumentLifecycleStep($document, 'ai_analysis_failed', [
                        'error' => $aiAnalysis['error'] ?? 'Unknown error',
                        'fallback_category' => 'general'
                    ]);
                    
                    \Log::warning('AI analysis failed, using fallback category', [
                        'document_id' => $document->id,
                        'error' => $aiAnalysis['error'] ?? 'Unknown error'
                    ]);
                }
            } else {
                // Text extraction failed or returned invalid content
                \Log::error('Text extraction failed or returned invalid content', [
                    'document_id' => $document->id,
                    'extracted_text' => $extractedText,
                    'text_length' => strlen($extractedText),
                    'file_type' => $file->getMimeType()
                ]);
                
                $this->logDocumentLifecycleStep($document, 'text_extraction_failed', [
                    'file_type' => $file->getMimeType(),
                    'extracted_text' => $extractedText,
                    'fallback_category' => 'general'
                ]);
                
                // Set a meaningful fallback category based on filename
                $fallbackCategory = $this->determineFallbackCategory($file->getClientOriginalName());
                $document->update(['category' => $fallbackCategory]);
                
                \Log::warning('Text extraction failed, using filename-based fallback category', [
                    'document_id' => $document->id,
                    'file_type' => $file->getMimeType(),
                    'fallback_category' => $fallbackCategory
                ]);
            }
        } catch (\Exception $e) {
            // Log AI analysis error but continue with document creation
            \Log::error('AI analysis error during document upload', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->logDocumentLifecycleStep($document, 'ai_analysis_error', [
                'error' => $e->getMessage(),
                'fallback_category' => 'general'
            ]);
            
            \Log::error('AI analysis error during document upload', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Create AccessLog entry for legal documents
        if ($request->source === 'legal_management') {
            AccessLog::create([
                'user_id' => $uploadedBy, // Use validated user ID
                'action' => 'legal_document_uploaded',
                'description' => "Legal document '{$request->title}' uploaded successfully",
                'ip_address' => request()->ip()
            ]);
        }

        // Step 4: Route document based on AI analysis
        $this->routeDocument($document, $document->ai_analysis);

        // Log successful upload using DeptAccount Dept_no if available
        try {
            $deptNo = null;
            if (!empty($uploadedBy)) {
                // $uploadedBy may already be Dept_no, try to map if it looks like employee_id
                if (is_string($uploadedBy) && !is_numeric($uploadedBy)) {
                    $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $uploadedBy)->first())->Dept_no;
                } else {
                    $deptNo = $uploadedBy;
                }
            }
            if (!$deptNo && auth()->check()) {
                $email = auth()->user()->email ?? '';
                $empFromEmail = strstr($email, '@', true);
                if ($empFromEmail) {
                    $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empFromEmail)->first())->Dept_no;
                }
            }
            AccessLog::create([
                'user_id' => $deptNo ?? 0,
                'action' => 'document_uploaded',
                'description' => 'Document uploaded: ' . $request->title,
                'ip_address' => request()->ip()
            ]);
        } catch (\Throwable $e) {
            // swallow logging errors
        }

        // Debug logging for successful upload
        \Log::info('Document uploaded successfully', [
            'document_id' => $document->id,
            'title' => $document->title,
            'uploaded_by' => $document->uploaded_by,
            'source' => $request->source,
            'is_ajax' => $request->ajax()
        ]);

        // Return response based on request type
        if ($request->ajax() || $request->wantsJson()) {
            // Refresh the document to get the latest data
            $document->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully!',
                'document' => [
                    'id' => $document->id,
                    'title' => $document->title,
                    'description' => $document->description,
                    'category' => $document->category,
                    'file_path' => $document->file_path,
                    'status' => $document->status,
                    'uploader_name' => $document->uploader_name,
                    'created_at' => $document->created_at
                ]
            ]);
        }
        
        return redirect()->route('document.index')->with('success', 'Document uploaded successfully!');
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
        $this->logDocumentLifecycleStep($document, 'routed_to_fr', [
            'target_module' => 'Facility Reservations',
            'ai_analysis_summary' => $aiAnalysis['summary'] ?? 'No summary available'
        ]);
        
        // Enhanced auto-scheduling as per TO BE diagram
        $autoScheduledReservation = $this->notificationService->autoScheduleFacilityAndNotify($document, $aiAnalysis);
        
        if ($autoScheduledReservation) {
            // Auto-scheduling successful
            $this->logDocumentLifecycleStep($document, 'auto_scheduled_successfully', [
                'reservation_id' => $autoScheduledReservation->id,
                'facility_name' => $autoScheduledReservation->facility->name ?? 'Unknown',
                'scheduled_time' => $autoScheduledReservation->start_time
            ]);
        } else {
            // Fallback to manual reservation creation
            $reservation = FacilityReservation::create([
                'facility_id' => 1, // Default facility - will be updated by user
                'reserved_by' => Auth::id(),
                'start_time' => now()->addDay(), // Default time - will be updated
                'end_time' => now()->addDay()->addHour(), // Default time - will be updated
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
            
            $this->logDocumentLifecycleStep($document, 'manual_reservation_created', [
                'reservation_id' => $reservation->id,
                'reason' => 'Auto-scheduling not applicable or failed'
            ]);
        }

        // Log routing action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'document_routed_to_fr_enhanced',
            'description' => 'Document routed to Facility Reservations with enhanced auto-scheduling. Document ID: ' . $document->id,
            'ip_address' => request()->ip()
        ]);

        $document->logWorkflowStep('routed_to_fr', 'Document routed to Facility Reservations module with auto-scheduling attempt');
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
        $riskScore = $aiAnalysis['legal_risk_score'] ?? 'Low';
        
        $this->logDocumentLifecycleStep($document, 'routed_to_lm', [
            'target_module' => 'Legal Management',
            'risk_score' => $riskScore,
            'requires_review' => $aiAnalysis['requires_legal_review'] ?? false
        ]);
        
        // Enhanced legal processing as per TO BE diagram
        if ($riskScore === 'High' || ($aiAnalysis['requires_legal_review'] ?? false)) {
            // High-risk documents: Create case or legal memo
            $this->createLegalCaseOrMemo($document, $aiAnalysis);
            $this->logDocumentLifecycleStep($document, 'legal_case_created', [
                'risk_level' => 'high',
                'action_taken' => 'case_or_memo_creation'
            ]);
        } else {
            // Lower risk documents: Standard archive with legal review flag
            $document->update(['status' => 'archived_legal_review']);
            $this->logDocumentLifecycleStep($document, 'archived_with_legal_flag', [
                'risk_level' => 'low_to_medium',
                'action_taken' => 'archived_for_review'
            ]);
        }

        // Log comprehensive routing action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'document_routed_to_lm_enhanced',
            'description' => "Document routed to Legal Management with enhanced processing. Risk Score: {$riskScore}, Action: " . ($riskScore === 'High' ? 'Case/Memo Creation' : 'Archive with Review'),
            'ip_address' => request()->ip()
        ]);

        $document->logWorkflowStep('routed_to_lm', 'Document routed to Legal Management module with risk-based processing');
    }

    /**
     * Create legal case or memo for high-risk documents
     */
    private function createLegalCaseOrMemo($document, $aiAnalysis)
    {
        try {
            // Determine document type for case/memo creation
            $category = $aiAnalysis['category'] ?? 'general';
            $riskScore = $aiAnalysis['legal_risk_score'] ?? 'High';
            
            // Create legal case entry
            $legalCase = [
                'case_title' => "Legal Review Required: " . $document->title,
                'case_description' => $this->generateLegalCaseDescription($document, $aiAnalysis),
                'priority' => $this->mapRiskScoreToPriority($riskScore),
                'source_document_id' => $document->id,
                'assigned_to' => $this->getDefaultLegalReviewer(),
                'status' => 'pending_review',
                'created_by' => Auth::id(),
                'legal_implications' => $aiAnalysis['legal_implications'] ?? 'Requires legal analysis',
                'compliance_status' => $aiAnalysis['compliance_status'] ?? 'review_required'
            ];
            
            // Store legal case information in document
            $document->update([
                'legal_case_data' => $legalCase,
                'workflow_stage' => 'legal_case_created'
            ]);
            
            $this->logDocumentLifecycleStep($document, 'legal_case_data_created', [
                'case_title' => $legalCase['case_title'],
                'priority' => $legalCase['priority'],
                'assigned_to' => $legalCase['assigned_to']
            ]);
            
            Log::info('Legal case created for high-risk document', [
                'document_id' => $document->id,
                'risk_score' => $riskScore,
                'case_title' => $legalCase['case_title']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create legal case for document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            
            $this->logDocumentLifecycleStep($document, 'legal_case_creation_failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate legal case description
     */
    private function generateLegalCaseDescription($document, $aiAnalysis)
    {
        $description = "Legal review required for document: " . $document->title . "\n\n";
        $description .= "AI Analysis Summary: " . ($aiAnalysis['summary'] ?? 'No summary available') . "\n\n";
        $description .= "Key Information: " . ($aiAnalysis['key_info'] ?? 'No key information extracted') . "\n\n";
        $description .= "Legal Risk Score: " . ($aiAnalysis['legal_risk_score'] ?? 'Unknown') . "\n";
        $description .= "Requires Legal Review: " . (($aiAnalysis['requires_legal_review'] ?? false) ? 'Yes' : 'No') . "\n";
        $description .= "Document Category: " . ($aiAnalysis['category'] ?? 'general') . "\n\n";
        $description .= "Please review this document for legal compliance and potential risks.";
        
        return $description;
    }

    /**
     * Map risk score to priority
     */
    private function mapRiskScoreToPriority($riskScore)
    {
        switch ($riskScore) {
            case 'High':
                return 'urgent';
            case 'Medium':
                return 'normal';
            default:
                return 'low';
        }
    }

    /**
     * Get default legal reviewer (can be enhanced to use role-based assignment)
     */
    private function getDefaultLegalReviewer()
    {
        // For now, return the current user or system
        return Auth::id() ?? 1;
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

    public function show($id)
    {
        $document = Document::with(['uploader', 'documentRequests.requester', 'documentRequests.approver'])
            ->where('source', 'document_management')
            ->findOrFail($id);
            
        // Check access permissions
        if (!$this->canAccessDocument(Auth::user(), $document)) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied: You do not have permission to view this document.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Access Denied: You do not have permission to view this document.');
        }
        
        // Log access
        $this->logDocumentAccess($document, Auth::user(), 'view');
        
        // Check if user is administrator for action buttons
        $isAdmin = $this->isAdministrator(Auth::user());
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'document' => [
                    'id' => $document->id,
                    'title' => $document->title,
                    'description' => $document->description,
                    'category' => $document->category,
                    'department' => $document->department,
                    'confidentiality' => $document->confidentiality,
                    'retention_policy' => $document->retention_policy,
                    'retention_until' => $document->retention_until,
                    'status' => $document->status,
                    'file_path' => $document->file_path,
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at,
                    'uploader' => $document->uploader,
                    'ai_analysis' => $document->ai_analysis,
                    'is_admin' => $isAdmin
                ]
            ]);
        }
        
        return view('document.show', compact('document', 'isAdmin'));
    }

    public function edit($id)
    {
        // Check if user is administrator
        if (!$this->isAdministrator(Auth::user())) {
            return redirect()->back()->with('error', 'Access Denied: Only administrators can edit documents.');
        }
        
        $document = Document::where('source', 'document_management')->findOrFail($id);
        return view('document.edit', compact('document'));
    }

    public function update(Request $request, $id)
    {
        // Check if user is administrator
        if (!$this->isAdministrator(Auth::user())) {
            return redirect()->back()->with('error', 'Access Denied: Only administrators can update documents.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'confidentiality' => 'nullable|string|in:public,internal,restricted',
            'retention_policy' => 'nullable|string|in:none,30_days,6_months,1_year,3_years,custom',
            'retention_until' => 'nullable|date'
        ]);

        $document = Document::where('source', 'document_management')->findOrFail($id);
        $document->update($request->only(['title', 'description', 'department', 'category', 'confidentiality', 'retention_policy', 'retention_until']));

        // Log the update
        $document->logWorkflowStep('document_updated', 'Document updated by administrator', [
            'updated_by' => Auth::user()->name ?? Auth::user()->id,
            'updated_fields' => array_keys($request->only(['title', 'description', 'department', 'category', 'confidentiality', 'retention_policy', 'retention_until']))
        ]);

        return redirect()->route('document.show', $id)->with('success', 'Document updated successfully!');
    }

    public function destroy($id)
    {
        // Check if user is administrator
        if (!$this->isAdministrator(Auth::user())) {
            return redirect()->back()->with('error', 'Access Denied: Only administrators can delete documents.');
        }
        
        try {
            $document = Document::where('source', 'document_management')->findOrFail($id);

            // Delete file from storage
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            // If the request is AJAX/JSON, return a JSON response for the frontend fetch()
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document deleted successfully!'
                ]);
            }

            return redirect()->route('document.index')->with('success', 'Document deleted successfully!');
        } catch (\Throwable $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting document: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error deleting document: ' . $e->getMessage());
        }
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
        
        // Check access permissions
        if (!$this->canAccessDocument(Auth::user(), $document)) {
            return redirect()->back()->with('error', 'Access Denied: You do not have permission to download this document.');
        }
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Log download access
        $this->logDocumentAccess($document, Auth::user(), 'download');

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
            $aiAnalysis = $this->geminiService->analyzeDocument($documentText);
            
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
    
    /**
     * Analyze an existing stored legal document via AJAX and return JSON.
     * This mirrors analyze() but never redirects; it always returns a JSON
     * payload and gracefully falls back when OCR/AI fails so the UI never sees 500s.
     */
    public function analyzeAjax($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            // Validate file exists; if missing, we will still attempt analysis using stored data
            $filePath = storage_path('app/public/' . $document->file_path);
            $fileExists = file_exists($filePath);
            
            // Extract text using the extractor service with safe fallback
            $documentText = '';
            if ($fileExists) {
                try {
                    $documentText = $this->textExtractor->extractText($filePath);
                } catch (\Throwable $e) {
                    \Log::warning('OCR extraction failed, will try stored text / metadata', [
                        'document_id' => $document->id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                \Log::warning('AnalyzeAjax: File missing, using stored text/metadata fallback', [
                    'document_id' => $document->id,
                    'file_path' => $document->file_path
                ]);
            }
            
            if (empty($documentText)) {
                // If we have previously stored extracted_text use it first
                if (!empty($document->extracted_text)) {
                    $documentText = $document->extracted_text;
                } else {
                    // Ensure we always have text to classify – use metadata
                    $meta = trim(($document->title ?? '') . ' ' . ($document->description ?? ''));
                    $documentText = ($meta !== '') ? $meta : 'general document content - filename: ' . basename($document->file_path ?? 'document');
                }
            }
            
            // Run AI with graceful fallback
            try {
                $aiAnalysis = $this->geminiService->analyzeDocument($documentText);
            } catch (\Throwable $e) {
                \Log::error('Gemini analysis threw unexpectedly, using fallback', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
                $aiAnalysis = app(\App\Services\GeminiService::class)->fallbackAnalysis($documentText);
            }
                
            if (isset($aiAnalysis['error']) && $aiAnalysis['error']) {
                // Convert to non-error by using fallback
                $aiAnalysis = app(\App\Services\GeminiService::class)->fallbackAnalysis($documentText);
            }
            
            // Optionally persist latest analysis to the document record
            try {
                $document->update([
                    'ai_analysis' => $aiAnalysis,
                    'category' => $aiAnalysis['category'] ?? ($document->category ?? 'general'),
                    'requires_legal_review' => $aiAnalysis['requires_legal_review'] ?? false,
                    'legal_risk_score' => $aiAnalysis['legal_risk_score'] ?? ($document->legal_risk_score ?? 'Low')
                ]);
            } catch (\Throwable $e) {
                // Non-fatal if persisting fails
                \Log::warning('Failed to persist AI analysis on document', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'analysis' => $aiAnalysis
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.'
            ], 404);
        } catch (\Throwable $e) {
            \Log::error('Unexpected error in analyzeAjax', [
                'document_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function analyzeUpload(Request $request)
    {
        try {
            // Log the incoming request for debugging
            \Log::info('AI Analysis request received', [
                'has_file' => $request->hasFile('document_file'),
                'file_name' => $request->file('document_file') ? $request->file('document_file')->getClientOriginalName() : 'no file',
                'file_size' => $request->file('document_file') ? $request->file('document_file')->getSize() : 'no file',
                'file_type' => $request->file('document_file') ? $request->file('document_file')->getMimeType() : 'no file',
                'all_data' => $request->all()
            ]);

            // Validate the request
            $request->validate([
                'document_file' => 'required|file|max:10240'
            ]);

            $file = $request->file('document_file');
            
            if (!$file || !$file->isValid()) {
                \Log::error('File validation failed', [
                    'file' => $file ? $file->getClientOriginalName() : 'null',
                    'is_valid' => $file ? $file->isValid() : 'null',
                    'error' => $file ? $file->getError() : 'null'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file uploaded'
                ], 422);
            }

            // Extract text with better error handling
            $documentText = '';
            try {
                $documentText = $this->textExtractor->extractText($file->getRealPath());
                \Log::info('Text extraction completed', [
                    'file_path' => $file->getRealPath(),
                    'text_length' => strlen($documentText),
                    'text_preview' => substr($documentText, 0, 200)
                ]);
            } catch (\Exception $e) {
                \Log::warning('Text extraction failed, using fallback', [
                    'error' => $e->getMessage(),
                    'file_path' => $file->getRealPath()
                ]);
                $documentText = 'general document content - filename: ' . $file->getClientOriginalName();
            }

            // Ensure we always have some text to classify
            if (empty($documentText)) {
                $documentText = 'general document content - filename: ' . $file->getClientOriginalName();
            }

            // Perform AI analysis
            try {
                \Log::info('Starting Gemini AI analysis', [
                    'text_length' => strlen($documentText),
                    'text_preview' => substr($documentText, 0, 100)
                ]);
                
                $aiAnalysis = $this->geminiService->analyzeDocument($documentText);
                
                \Log::info('Gemini AI analysis completed', [
                    'has_error' => isset($aiAnalysis['error']),
                    'analysis_keys' => array_keys($aiAnalysis)
                ]);
                
                // Guarantee a result: if remote analysis fails, use local fallback
                if (isset($aiAnalysis['error']) && $aiAnalysis['error']) {
                    \Log::warning('Gemini AI failed, using fallback analysis');
                    $aiAnalysis = app(\App\Services\GeminiService::class)->fallbackAnalysis($documentText);
                }

                return response()->json([
                    'success' => true,
                    'analysis' => $aiAnalysis
                ]);
            } catch (\Throwable $e) {
                \Log::error('AI analysis failed, using fallback', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $aiAnalysis = app(\App\Services\GeminiService::class)->fallbackAnalysis($documentText);
                return response()->json([
                    'success' => true,
                    'analysis' => $aiAnalysis,
                    'fallback' => true,
                    'warning' => 'AI analysis failed, using fallback classification'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in analyzeUpload', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in analyzeUpload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk upload multiple documents
     */
    public function bulkUpload(Request $request)
    {
        if (!Auth::check()) {
            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to upload documents.'
                ]);
            }
            
            return redirect()->route('login')->with('error', 'Authentication required to upload documents.');
        }

        $request->validate([
            'category' => 'nullable|string|max:255',
            'description_template' => 'nullable|string',
            'document_files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png|max:10240'
        ]);

        $user = Auth::user();
        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('document_files') as $file) {
            try {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('documents', $fileName, 'public');

                // Create document record
                $document = Document::create([
                    'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'description' => $request->description_template,
                    'category' => $request->category,
                    'file_path' => $filePath,
                    'uploaded_by' => $user->Dept_no ?? $user->id,
                    'status' => 'active',
                    'source' => 'legal_management',
                    'workflow_stage' => 'uploaded',
                    'workflow_log' => [],
                    'lifecycle_log' => []
                ]);

                // Log the upload
                $this->logDocumentLifecycleStep($document, 'bulk_uploaded', [
                    'user_id' => $user->Dept_no ?? $user->id,
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType()
                ]);

                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to upload {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        // Log bulk upload action
        AccessLog::create([
            'user_id' => $user->Dept_no ?? $user->id,
            'action' => 'bulk_document_upload',
            'description' => "Bulk uploaded {$uploadedCount} legal documents",
            'ip_address' => request()->ip()
        ]);

        if (count($errors) > 0) {
            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully uploaded {$uploadedCount} documents. Some documents failed to upload: " . implode(', ', $errors)
                ]);
            }
            
            return redirect()->route('legal.legal_documents')
                ->with('success', "Successfully uploaded {$uploadedCount} documents")
                ->with('error', "Some documents failed to upload: " . implode(', ', $errors));
        }

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully uploaded {$uploadedCount} legal documents!"
            ]);
        }

        return redirect()->route('legal.legal_documents')
            ->with('success', "Successfully uploaded {$uploadedCount} legal documents!");
    }

    /**
     * Check if user is authorized for document upload
     */
    private function isUserAuthorizedForUpload($user)
    {
        // Allow all authenticated users for now, but log the access
        // In production, this could check specific roles or permissions
        
        // Ensure we have a valid user_id for logging
        $userId = $user->Dept_no ?? $user->id ?? 'unknown';
        
        AccessLog::create([
            'user_id' => $userId,
            'action' => 'authorization_check_passed',
            'description' => 'User passed authorization check for document upload',
            'ip_address' => request()->ip()
        ]);
        return true;
    }

    /**
     * Enhanced document lifecycle tracking with OCR quality assessment
     */
    private function logDocumentLifecycleStep($document, $step, $details = [])
    {
        $lifecycleLog = $document->lifecycle_log ?? [];
        // Prefer DeptAccount Dept_no over Laravel user id for audit trail
        $userId = null;
        try {
            $empId = session('emp_id');
            if ($empId) {
                $userId = optional(\App\Models\DeptAccount::where('employee_id', $empId)->first())->Dept_no;
            }
            if (!$userId && Auth::check()) {
                $email = Auth::user()->email ?? '';
                $empFromEmail = strstr($email, '@', true);
                if ($empFromEmail) {
                    $userId = optional(\App\Models\DeptAccount::where('employee_id', $empFromEmail)->first())->Dept_no;
                }
            }
        } catch (\Throwable $e) {
            $userId = null;
        }
        $userId = $userId ?? 'unknown';
        
        // Add OCR quality assessment if this is a text extraction step
        if ($step === 'text_extraction_completed' && isset($details['extracted_text'])) {
            $details['ocr_quality'] = $this->assessExtractionQuality($details['extracted_text']);
            $details['text_validation_passed'] = $this->isValidExtractedText($details['extracted_text']);
        }
        
        $lifecycleLog[] = [
            'step' => $step,
            'timestamp' => now()->toISOString(),
            'user_id' => $userId,
            'details' => $details,
            'ip_address' => request()->ip()
        ];
        
        $document->update(['lifecycle_log' => $lifecycleLog]);
        
        // Also log to AccessLog for audit trail
        try {
            AccessLog::create([
                'user_id' => $userId,
                'action' => 'document_lifecycle_' . $step,
                'description' => "Document lifecycle: {$step} for document ID {$document->id}",
                'ip_address' => request()->ip()
            ]);
        } catch (\Throwable $e) {
            // swallow logging errors
        }
    }

    /**
     * Update document status with comprehensive tracking
     */
    private function updateDocumentStatus($document, $status, $reason = '')
    {
        $oldStatus = $document->status;
        $document->update(['status' => $status]);
        
        $this->logDocumentLifecycleStep($document, 'status_update', [
            'old_status' => $oldStatus,
            'new_status' => $status,
            'reason' => $reason
        ]);
    }

    /**
     * Determine routing decision based on TO BE diagram logic
     */
    private function determineRoutingDecision($document, $aiAnalysis)
    {
        if (!$aiAnalysis || $aiAnalysis['error']) {
            return [
                'route' => 'non_actionable',
                'target_module' => null,
                'reason' => 'AI analysis failed - document archived'
            ];
        }

        $category = $aiAnalysis['category'] ?? 'general';
        $requiresLegalReview = $aiAnalysis['requires_legal_review'] ?? false;
        $requiresVisitorCoordination = $aiAnalysis['requires_visitor_coordination'] ?? false;
        $riskScore = $aiAnalysis['legal_risk_score'] ?? 'Low';

        // Priority routing logic as per TO BE diagram:
        
        // 1. High-risk legal documents go to LM
        if ($riskScore === 'High' || $requiresLegalReview) {
            return [
                'route' => 'actionable',
                'target_module' => 'LM',
                'reason' => "High legal risk ({$riskScore}) or requires legal review"
            ];
        }

        // 2. Facility reservation documents go to FR
        if ($this->isFacilityReservationDocument($category, $aiAnalysis)) {
            return [
                'route' => 'actionable',
                'target_module' => 'FR',
                'reason' => 'Contains facility reservation content'
            ];
        }

        // 3. Visitor coordination documents go to VM
        if ($requiresVisitorCoordination) {
            return [
                'route' => 'actionable',
                'target_module' => 'VM',
                'reason' => 'Requires visitor coordination'
            ];
        }

        // 4. Medium-risk legal documents to LM
        if ($riskScore === 'Medium') {
            return [
                'route' => 'actionable',
                'target_module' => 'LM',
                'reason' => "Medium legal risk requires review"
            ];
        }

        // 5. All other documents are archived as non-actionable
        return [
            'route' => 'non_actionable',
            'target_module' => null,
            'reason' => "Low risk general document - no specific action required"
        ];
    }

    /**
     * Edit legal document
     */
    public function editLegalDocument($id)
    {
        $document = Document::where('source', 'legal_management')->findOrFail($id);
        return view('legal.edit_document', compact('document'));
    }

    /**
     * Update legal document
     */
    public function updateLegalDocument(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255'
        ]);

        $document = Document::where('source', 'legal_management')->findOrFail($id);
        $document->update($request->all());

        // Log the update (use DeptAccount Dept_no)
        try {
            $deptNo = null;
            $empId = session('emp_id');
            if ($empId) {
                $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empId)->first())->Dept_no;
            }
            if (!$deptNo && Auth::check()) {
                $email = Auth::user()->email ?? '';
                $empFromEmail = strstr($email, '@', true);
                if ($empFromEmail) {
                    $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empFromEmail)->first())->Dept_no;
                }
            }
            AccessLog::create([
                'user_id' => $deptNo ?? 0,
                'action' => 'legal_document_updated',
                'description' => "Updated legal document: {$document->title}",
                'ip_address' => request()->ip()
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors
        }

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Legal document updated successfully!'
            ]);
        }

        return redirect()->route('legal.legal_documents')->with('success', 'Legal document updated successfully!');
    }

    /**
     * Delete legal document
     */
    public function deleteLegalDocument($id)
    {
        try {
            $document = Document::where('source', 'legal_management')->findOrFail($id);

            // Delete file from storage if present
            if (!empty($document->file_path) && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Safe deletion log (non-fatal if logging fails) - use DeptAccount Dept_no
            try {
                $deptNo = null;
                $empId = session('emp_id');
                if ($empId) {
                    $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empId)->first())->Dept_no;
                }
                if (!$deptNo && Auth::check()) {
                    $email = Auth::user()->email ?? '';
                    $empFromEmail = strstr($email, '@', true);
                    if ($empFromEmail) {
                        $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empFromEmail)->first())->Dept_no;
                    }
                }
                AccessLog::create([
                    'user_id' => $deptNo ?? 0,
                    'action' => 'legal_document_deleted',
                    'description' => "Deleted legal document: {$document->title}",
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to log deletion for legal document', [
                    'id' => $document->id,
                    'error' => $e->getMessage()
                ]);
            }

            $document->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Legal document deleted successfully!'
                ]);
            }

            return redirect()->route('legal.legal_documents')->with('success', 'Legal document deleted successfully!');
        } catch (\Throwable $e) {
            \Log::error('Error deleting legal document', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting document: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }

    /**
     * Show legal document
     */
    public function showLegalDocument($id)
    {
        $document = Document::where('source', 'legal_management')->with('uploader')->findOrFail($id);
        return view('legal.show_document', compact('document'));
    }
    
   /**
    * Download legal document
    */
   public function downloadLegalDocument($id)
   {
       $document = Document::findOrFail($id);
       
       $filePath = storage_path('app/public/' . $document->file_path);
       if (file_exists($filePath)) {
           return response()->download($filePath);
       }
       
       return back()->with('error', 'File not found.');
   }

   /**
    * Approve legal document
    */
   public function approveLegalDocument(Request $request, $id)
   {
       try {
           $document = Document::where('source', 'legal_management')->findOrFail($id);
           
           $notes = $request->input('notes', '');
           
           $document->update([
               'status' => 'approved',
               'approved_by' => Auth::id(),
               'approval_notes' => $notes,
               'approved_at' => now()
           ]);

           // Log the approval
           AccessLog::create([
               'user_id' => Auth::id() ?? 'unknown',
               'action' => 'legal_document_approved',
               'description' => "Approved legal document: {$document->title}",
               'ip_address' => request()->ip()
           ]);

           if ($request->ajax()) {
               return response()->json([
                   'success' => true,
                   'message' => 'Document approved successfully!'
               ]);
           }

           return redirect()->route('legal.legal_documents')->with('success', 'Document approved successfully!');
       } catch (\Exception $e) {
           if ($request->ajax()) {
               return response()->json([
                   'success' => false,
                   'message' => 'Error approving document: ' . $e->getMessage()
               ], 500);
           }
           
           return back()->with('error', 'Error approving document: ' . $e->getMessage());
       }
   }

   /**
    * Decline legal document
    */
   public function declineLegalDocument(Request $request, $id)
   {
       try {
           $document = Document::where('source', 'legal_management')->findOrFail($id);
           
           $reason = $request->input('reason', '');
           
           if (empty($reason)) {
               if ($request->ajax()) {
                   return response()->json([
                       'success' => false,
                       'message' => 'Decline reason is required.'
                   ], 422);
               }
               return back()->with('error', 'Decline reason is required.');
           }
           
           $document->update([
               'status' => 'declined',
               'declined_by' => Auth::id(),
               'decline_reason' => $reason,
               'declined_at' => now()
           ]);

           // Log the decline (use DeptAccount Dept_no)
           try {
               $deptNo = null;
               $empId = session('emp_id');
               if ($empId) {
                   $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empId)->first())->Dept_no;
               }
               if (!$deptNo && Auth::check()) {
                   $email = Auth::user()->email ?? '';
                   $empFromEmail = strstr($email, '@', true);
                   if ($empFromEmail) {
                       $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empFromEmail)->first())->Dept_no;
                   }
               }
               AccessLog::create([
                   'user_id' => $deptNo ?? 0,
                   'action' => 'legal_document_declined',
                   'description' => "Declined legal document: {$document->title} - Reason: {$reason}",
                   'ip_address' => request()->ip()
               ]);
           } catch (\Throwable $e) {
               // ignore logging errors
           }

           if ($request->ajax()) {
               return response()->json([
                   'success' => true,
                   'message' => 'Document declined successfully!'
               ]);
           }

           return redirect()->route('legal.legal_documents')->with('success', 'Document declined successfully!');
       } catch (\Exception $e) {
           if ($request->ajax()) {
               return response()->json([
                   'success' => false,
                   'message' => 'Error declining document: ' . $e->getMessage()
               ], 500);
           }
           
           return back()->with('error', 'Error declining document: ' . $e->getMessage());
       }
   }

    /**
     * Archive a document
     */
    public function archive($id)
    {
        // Check if user is administrator
        if (!$this->isAdministrator(Auth::user())) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied: Only administrators can archive documents.'
                ], 403);
            }
            return back()->with('error', 'Access Denied: Only administrators can archive documents.');
        }
        
        try {
            $document = Document::findOrFail($id);
            $document->update(['status' => 'archived']);
            
            // Log the archive action
            $document->logWorkflowStep('document_archived', 'Document archived by administrator', [
                'archived_by' => Auth::user()->name ?? Auth::user()->id,
                'archived_at' => now()->toISOString()
            ]);
            
            AccessLog::create([
                'user_id' => Auth::id() ?? 'unknown',
                'action' => 'document_archived',
                'description' => "Document '{$document->title}' archived by administrator",
                'ip_address' => request()->ip()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document archived successfully'
                ]);
            }
            
            return back()->with('success', 'Document archived successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error archiving document: ' . $e->getMessage()
                ]);
            }
            
            return back()->with('error', 'Error archiving document: ' . $e->getMessage());
        }
    }

    /**
     * Unarchive a document
     */
    public function unarchive($id)
    {
        try {
            $document = Document::findOrFail($id);
            $document->update(['status' => 'active']);
            
            // Log the unarchive action
            AccessLog::create([
                'user_id' => Auth::id() ?? 'unknown',
                'action' => 'document_unarchived',
                'description' => "Document '{$document->title}' unarchived",
                'ip_address' => request()->ip()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document unarchived successfully'
                ]);
            }
            
            return back()->with('success', 'Document unarchived successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error unarchiving document: ' . $e->getMessage()
                ]);
            }
            
            return back()->with('error', 'Error unarchiving document: ' . $e->getMessage());
        }
    }

    /**
     * Get archived documents
     */
    public function archived()
    {
        // Show both archived documents and expired documents (ready for disposal)
        $documents = Document::where(function($query) {
                $query->where('status', 'archived')
                      ->orWhere('status', 'expired')
                      ->orWhere(function($q) {
                          $q->whereNotNull('retention_until')
                            ->where('retention_until', '<=', now()->addDays(30));
                      });
            })
            ->with(['uploader'])
            ->latest()
            ->paginate(20);

        return view('document.archived', compact('documents'));
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Validate extracted text to ensure it's not empty and doesn't contain common OCR errors.
     */
    private function isValidExtractedText($text)
    {
        // Check if text is empty
        if (empty(trim($text))) {
            \Log::warning('DocumentController: Text validation failed - empty text', [
                'text_length' => strlen($text)
            ]);
            return false;
        }

        // Check for common OCR errors and fallback messages
        $lowercaseText = strtolower($text);
        $fallbackIndicators = [
            'unknown document type',
            'document not found',
            'pdf text extraction failed',
            'likely scanned',
            'image file',
            'pdf file',
            'manual review recommended',
            'tmp',
            'file not found'
        ];
        
        foreach ($fallbackIndicators as $indicator) {
            if (str_contains($lowercaseText, $indicator)) {
                \Log::warning('DocumentController: Text validation failed - contains fallback indicator', [
                    'indicator' => $indicator,
                    'text_preview' => substr($text, 0, 100)
                ]);
                return false;
            }
        }

        // Check if text is too short (likely not meaningful content)
        if (strlen($text) < 50) {
            \Log::warning('DocumentController: Text validation failed - text too short', [
                'text_length' => strlen($text),
                'text_preview' => $text
            ]);
            return false;
        }

        // Check if text contains mostly special characters or numbers
        $alphaContent = preg_replace('/[^a-zA-Z\s]/', '', $text);
        $alphaRatio = strlen($alphaContent) / strlen($text);
        
        if ($alphaRatio < 0.3) { // Less than 30% alphabetic content
            \Log::warning('DocumentController: Text validation failed - insufficient alphabetic content', [
                'alpha_ratio' => $alphaRatio,
                'text_preview' => substr($text, 0, 100)
            ]);
            return false;
        }

        \Log::info('DocumentController: Text validation passed', [
            'text_length' => strlen($text),
            'alpha_ratio' => $alphaRatio,
            'text_preview' => substr($text, 0, 200)
        ]);

        return true;
    }

    /**
     * Determine a fallback category based on the filename.
     */
    private function determineFallbackCategory($filename)
    {
        $lowercaseFilename = strtolower($filename);
        
        \Log::info('DocumentController: Determining fallback category from filename', [
            'filename' => $filename,
            'lowercase_filename' => $lowercaseFilename
        ]);
        
        // Enhanced document type mapping
        $documentTypeMap = [
            // Policy documents
            'privacy' => 'policy',
            'policy' => 'policy',
            'terms' => 'policy',
            'data protection' => 'policy',
            'acceptable use' => 'policy',
            'data privacy' => 'policy',
            
            // Contract documents
            'contract' => 'contract',
            'agreement' => 'contract',
            'lease' => 'contract',
            'employment' => 'contract',
            'nda' => 'contract',
            'mou' => 'contract',
            
            // Financial documents
            'invoice' => 'financial',
            'receipt' => 'financial',
            'budget' => 'financial',
            'financial' => 'financial',
            'expense' => 'financial',
            'payment' => 'financial',
            
            // Legal documents
            'legal' => 'legal',
            'affidavit' => 'legal',
            'subpoena' => 'legal',
            'court' => 'legal',
            'law' => 'legal',
            'litigation' => 'legal',
            
            // Memorandum documents
            'memo' => 'memorandum',
            'memorandum' => 'memorandum',
            'moa' => 'memorandum',
            'internal' => 'memorandum',
            'staff' => 'memorandum',
            
            // Report documents
            'report' => 'report',
            'analysis' => 'report',
            'assessment' => 'report',
            'evaluation' => 'report',
            'findings' => 'report',
            'study' => 'report',
            
            // Compliance documents
            'compliance' => 'compliance',
            'regulation' => 'compliance',
            'regulatory' => 'compliance',
            'audit' => 'compliance',
            'standards' => 'compliance',
            
            // Communication documents
            'email' => 'communication',
            'letter' => 'communication',
            'correspondence' => 'communication',
            'communication' => 'communication',
            
            // Presentation documents
            'presentation' => 'presentation',
            'slide' => 'presentation',
            'deck' => 'presentation',
            
            // Spreadsheet documents
            'spreadsheet' => 'spreadsheet',
            'excel' => 'spreadsheet',
            'sheet' => 'spreadsheet',
            
            // General documents
            'document' => 'general',
            'doc' => 'general',
            'file' => 'general'
        ];
        
        // Check for document type indicators
        foreach ($documentTypeMap as $indicator => $category) {
            if (strpos($lowercaseFilename, $indicator) !== false) {
                \Log::info('DocumentController: Fallback category determined from filename', [
                    'filename' => $filename,
                    'indicator' => $indicator,
                    'category' => $category
                ]);
                return $category;
            }
        }
        
        // Default fallback
        \Log::info('DocumentController: Using default fallback category', [
            'filename' => $filename,
            'default_category' => 'general'
        ]);
        return 'general';
    }

    /**
     * Test OCR extraction for debugging purposes
     */
    public function testOcrExtraction(Request $request)
    {
        try {
            $request->validate([
                'document_file' => 'required|file|max:10240'
            ]);

            $file = $request->file('document_file');
            
            \Log::info('OCR Test: Starting text extraction test', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'file_extension' => $file->getClientOriginalExtension()
            ]);

            // Extract text with detailed logging
            $extractedText = $this->textExtractor->extractText($file->getRealPath());
            
            \Log::info('OCR Test: Text extraction completed', [
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $file->getRealPath(),
                'text_length' => strlen($extractedText),
                'text_preview' => substr($extractedText, 0, 500),
                'is_valid_text' => $this->isValidExtractedText($extractedText),
                'extraction_quality' => $this->assessExtractionQuality($extractedText)
            ]);

            return response()->json([
                'success' => true,
                'file_info' => [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension()
                ],
                'extraction_result' => [
                    'text_length' => strlen($extractedText),
                    'text_preview' => substr($extractedText, 0, 500),
                    'is_valid' => $this->isValidExtractedText($extractedText),
                    'quality_score' => $this->assessExtractionQuality($extractedText),
                    'full_text' => $extractedText
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('OCR Test: Error during text extraction test', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during OCR test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assess the quality of extracted text
     */
    private function assessExtractionQuality($text)
    {
        if (empty($text)) {
            return 'none';
        }

        $length = strlen($text);
        $alphaContent = preg_replace('/[^a-zA-Z\s]/', '', $text);
        $alphaRatio = strlen($alphaContent) / $length;
        
        // Check for fallback indicators
        $lowercaseText = strtolower($text);
        $fallbackIndicators = [
            'unknown document type',
            'document not found',
            'pdf text extraction failed',
            'likely scanned',
            'image file',
            'pdf file',
            'manual review recommended'
        ];
        
        foreach ($fallbackIndicators as $indicator) {
            if (str_contains($lowercaseText, $indicator)) {
                return 'fallback';
            }
        }

        // Quality scoring
        if ($length < 50) {
            return 'very_low';
        } elseif ($length < 200) {
            return 'low';
        } elseif ($alphaRatio < 0.3) {
            return 'low';
        } elseif ($length < 1000) {
            return 'medium';
        } elseif ($alphaRatio < 0.5) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    /**
     * Show disposal queue for expired documents
     */
    public function disposal()
    {
        // Get expired documents
        $documents = Document::where('status', 'expired')
            ->whereNotNull('retention_until')
            ->latest()
            ->paginate(20);

        // Get disposal stats
        $stats = [
            'expired' => Document::where('status', 'expired')->count(),
            'pending_disposal' => Document::where('status', 'expired')
                ->whereNotNull('retention_until')
                ->where('retention_until', '<=', now())
                ->count(),
            'disposed' => Document::where('status', 'disposed')->count()
        ];

        return view('document.disposal', compact('documents', 'stats'));
    }

    /**
     * Dispose of a document (permanent deletion)
     */
    public function dispose($id)
    {
        // Check if user is administrator
        if (!$this->isAdministrator(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied: Only administrators can dispose documents.'
            ], 403);
        }
        
        try {
            $document = Document::findOrFail($id);

            // Only allow disposal of expired documents
            if ($document->status !== 'expired') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only expired documents can be disposed.'
                ], 422);
            }

            // Delete file from storage if present
            if (!empty($document->file_path) && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Log disposal action (use DeptAccount Dept_no)
            try {
                $deptNo = null;
                $empId = session('emp_id');
                if ($empId) {
                    $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empId)->first())->Dept_no;
                }
                if (!$deptNo && Auth::check()) {
                    $email = Auth::user()->email ?? '';
                    $empFromEmail = strstr($email, '@', true);
                    if ($empFromEmail) {
                        $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empFromEmail)->first())->Dept_no;
                    }
                }
                AccessLog::create([
                    'user_id' => $deptNo ?? 0,
                    'action' => 'document_disposed',
                    'description' => "Document '{$document->title}' permanently disposed",
                    'ip_address' => request()->ip()
                ]);
            } catch (\Throwable $e) {
                // ignore logging errors
            }

            // Log disposal in lifecycle
            $log = $document->lifecycle_log ?? [];
            $log[] = [
                'step' => 'manually_disposed',
                'timestamp' => now()->toISOString(),
                'user_id' => Auth::id(),
                'details' => [
                    'previous_status' => $document->status,
                    'retention_until' => optional($document->retention_until)->toDateTimeString(),
                    'disposed_by' => Auth::id()
                ],
                'ip_address' => request()->ip()
            ];

            // Update document before deletion to log the action
            $document->update(['lifecycle_log' => $log]);

            // Create disposal history record before deleting
            DisposalHistory::create([
                'document_title' => $document->title,
                'document_description' => $document->description,
                'document_category' => $document->category,
                'document_department' => $document->department,
                'document_author' => $document->author,
                'file_path' => $document->file_path,
                'file_name' => basename($document->file_path ?? ''),
                'file_type' => pathinfo($document->file_path ?? '', PATHINFO_EXTENSION),
                'file_size' => $document->file_path ? Storage::disk('public')->size($document->file_path) : null,
                'confidentiality_level' => $document->confidentiality,
                'retention_until' => $document->retention_until,
                'retention_policy' => $document->retention_policy,
                'previous_status' => $document->status,
                'disposal_reason' => 'manually_disposed',
                'disposed_at' => now(),
                'disposed_by' => Auth::id(),
                'lifecycle_log' => $log,
                'ai_analysis' => $document->ai_analysis,
                'metadata' => $document->metadata,
                'ip_address' => request()->ip()
            ]);

            // Permanently delete the document record
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document disposed successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error disposing document', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error disposing document: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Check if user can access document based on confidentiality level
     */
    private function canAccessDocument($user, Document $document)
    {
        $confidentiality = $document->confidentiality ?? 'internal';
        $userRole = $user->role ?? 'user';
        
        switch ($confidentiality) {
            case 'public':
                return true;
            case 'internal':
                return true;
            case 'restricted':
                return in_array($userRole, ['admin', 'super_admin', 'legal_admin', 'hr_admin']);
            default:
                return true;
        }
    }

    /**
     * Log document access
     */
    private function logDocumentAccess(Document $document, $user, $action)
    {
        // Log to general AccessLog
        AccessLog::create([
            'user_id' => $user->id,
            'action' => 'document_' . $action,
            'description' => "Document {$action}: {$document->title} (ID: {$document->id})",
            'ip_address' => request()->ip(),
            'metadata' => [
                'document_id' => $document->id,
                'document_title' => $document->title,
                'confidentiality' => $document->confidentiality,
                'user_role' => $user->role ?? 'unknown'
            ]
        ]);
    }

    /**
     * Check if user is administrator
     */
    private function isAdministrator($user)
    {
        if (!$user) {
            return false;
        }
        
        $userRole = $user->role ?? 'user';
        return in_array($userRole, ['admin', 'Administrator', 'super_admin', 'legal_admin', 'hr_admin']);
    }
} 