<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacilityReservation;
use App\Notifications\DocumentRequestStatusNotification;
use App\Models\AccessLog;

class LegalController extends Controller
{
    public function index()
    {
        $pendingRequests = DocumentRequest::with(['document.uploader', 'requester'])
            ->where('status', 'pending')
            ->whereHas('document')
            ->latest()
            ->get();
            
        $approvedRequests = DocumentRequest::with(['document.uploader', 'requester', 'approver'])
            ->where('status', 'approved')
            ->whereHas('document')
            ->latest()
            ->take(10)
            ->get();
            
        $deniedRequests = DocumentRequest::with(['document.uploader', 'requester', 'approver'])
            ->where('status', 'denied')
            ->whereHas('document')
            ->latest()
            ->take(10)
            ->get();

        // Facility reservations - NOW HANDLED BY RESERVATION TASKS
        $pendingLegalReviewTasks = \App\Models\ReservationTask::with(['facilityReservation.facility', 'facilityReservation.reserver'])
            ->where('task_type', 'legal_review')
            ->where('status', 'pending')
            ->where('assigned_to_module', 'LM')
            ->latest()
            ->get();
        
        $approvedLegalReviewTasks = \App\Models\ReservationTask::with(['facilityReservation.facility', 'facilityReservation.reserver'])
            ->where('task_type', 'legal_review')
            ->where('status', 'completed')
            ->where('assigned_to_module', 'LM')
            ->latest()
            ->take(10) // Limit for dashboard display
            ->get();
        
        $flaggedLegalReviewTasks = \App\Models\ReservationTask::with(['facilityReservation.facility', 'facilityReservation.reserver'])
            ->where('task_type', 'legal_review')
            ->where('status', 'flagged')
            ->where('assigned_to_module', 'LM')
            ->latest()
            ->take(10) // Limit for dashboard display
            ->get();

        return view('legal.index', compact(
            'pendingRequests', 'approvedRequests', 'deniedRequests',
            'pendingLegalReviewTasks', 'approvedLegalReviewTasks', 'flaggedLegalReviewTasks'
        ));
    }

    public function create()
    {
        return view('legal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'case_title' => 'required|string|max:255',
            'case_description' => 'nullable|string',
            'legal_document' => 'required|file|mimes:pdf,doc,docx,txt|max:10240'
        ]);

        // Handle file upload
        $file = $request->file('legal_document');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('legal_documents', $fileName, 'public');

        // Extract text from document for AI analysis
        $documentText = '';
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'txt') {
            $documentText = file_get_contents($file->getRealPath());
        } elseif ($extension === 'pdf') {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $documentText = $pdf->getText();
        } elseif ($extension === 'docx') {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getRealPath());
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                $elements = $section->getElements();
                foreach ($elements as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    }
                }
            }
            $documentText = $text;
        }

        // Use AI to determine category
        $category = 'general';
        $aiAnalysis = null;
        
        if ($documentText) {
            try {
                $geminiService = new \App\Services\GeminiService();
                $aiAnalysis = $geminiService->analyzeDocument($documentText);
                
                // Check if analysis was successful (including fallback)
                if (!$aiAnalysis['error']) {
                    $category = $aiAnalysis['category'];
                    // Log the AI analysis for debugging
                    \Log::info('AI Analysis Result:', [
                        'category' => $category,
                        'full_analysis' => $aiAnalysis,
                        'is_fallback' => $aiAnalysis['fallback'] ?? false
                    ]);
                } else {
                    \Log::error('AI Analysis Error:', $aiAnalysis);
                    // Even if AI fails, try to use fallback
                    $category = 'general';
                }
            } catch (\Exception $e) {
                \Log::error('AI analysis failed: ' . $e->getMessage());
                $category = 'general';
            }
        }
        
        // Log the final category being saved
        \Log::info('Document being saved with category:', [
            'title' => $request->case_title,
            'category' => $category,
            'ai_analysis' => $aiAnalysis
        ]);

        // Create document record with AI-determined category
        $document = Document::create([
            'title' => $request->case_title,
            'description' => $request->case_description,
            'category' => $category,
            'file_path' => $filePath,
            'uploaded_by' => Auth::id(),
            'status' => 'archived',
            'source' => 'legal_management', // Mark as created through Legal Management
        ]);

        // Store AI analysis data if available
        if ($aiAnalysis && !$aiAnalysis['error']) {
            $document->update([
                'ai_analysis' => json_encode($aiAnalysis)
            ]);
        }

        // Get proper display name for success message
        $categoryDisplayNames = [
            'memorandum' => 'Memorandum',
            'contract' => 'Contract',
            'subpoena' => 'Subpoena',
            'affidavit' => 'Affidavit',
            'cease_desist' => 'Cease & Desist',
            'legal_notice' => 'Legal Notice',
            'policy' => 'Policy',
            'legal_brief' => 'Legal Brief',
            'financial' => 'Financial Document',
            'compliance' => 'Compliance Document',
            'report' => 'Report',
            'general' => 'Legal General'
        ];

        $displayCategory = $categoryDisplayNames[$category] ?? ucfirst($category);
        
        return redirect()->route('legal.index')->with('success', 'Legal case added successfully and classified as ' . $displayCategory . '!');
    }

    public function show($id)
    {
        $request = DocumentRequest::with(['document.uploader', 'requester', 'approver'])->findOrFail($id);
        $entities = [];
        $document = $request->document;
        if ($document) {
            $filePath = storage_path('app/public/' . $document->file_path);
            if (file_exists($filePath)) {
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $documentText = '';
                if ($extension === 'txt') {
                    $documentText = file_get_contents($filePath);
                } elseif ($extension === 'pdf') {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($filePath);
                    $documentText = $pdf->getText();
                } elseif ($extension === 'docx') {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                    $text = '';
                    foreach ($phpWord->getSections() as $section) {
                        $elements = $section->getElements();
                        foreach ($elements as $element) {
                            if (method_exists($element, 'getText')) {
                                $text .= $element->getText() . ' ';
                            }
                        }
                    }
                    $documentText = $text;
                }
                if ($documentText) {
                    // spaCy microservice removed; entity extraction disabled
                    $entities = [];
                }
            }
        }
        return view('legal.show', compact('request', 'entities'));
    }

    public function edit($id)
    {
        $request = DocumentRequest::with(['document.uploader', 'requester'])->findOrFail($id);
        return view('legal.edit', compact('request'));
    }

    public function update(Request $request, $id)
    {
        // This would be for editing legal notes or remarks
        return redirect()->route('legal.index');
    }

    public function destroy($id)
    {
        // Legal doesn't delete requests, only approves/denies
        return redirect()->route('legal.index');
    }

    public function approveRequest($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);
        
        if ($documentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        // Check if document exists
        if (!$documentRequest->document) {
            return redirect()->back()->with('error', 'Document not found for this request.');
        }

        // Extract text and classify document
        $document = $documentRequest->document;
        $filePath = storage_path('app/public/' . $document->file_path);
        $category = null;
        if (file_exists($filePath)) {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $documentText = '';
            if ($extension === 'txt') {
                $documentText = file_get_contents($filePath);
            } elseif ($extension === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $documentText = $pdf->getText();
            } elseif ($extension === 'docx') {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    $elements = $section->getElements();
                    foreach ($elements as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . ' ';
                        }
                    }
                }
                $documentText = $text;
            }
            if ($documentText) {
                // Use GeminiService if available; otherwise leave category unchanged
                try {
                    $geminiService = new \App\Services\GeminiService();
                    $analysis = $geminiService->analyzeDocument($documentText);
                    if (is_array($analysis) && isset($analysis['error']) && $analysis['error'] === false && isset($analysis['category'])) {
                        $category = $analysis['category'];
                    }
                } catch (\Throwable $e) {
                    // Gracefully skip if service or API key is unavailable
                }
            }
        }
        if ($category) {
            $document->update(['category' => $category]);
        }

        $documentRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks')
        ]);

        // Update document status to released
        $documentRequest->document->update(['status' => 'released']);

        // Notify requester
        $documentRequest->requester->notify(new DocumentRequestStatusNotification($documentRequest));

        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve_document_request',
            'description' => 'Approved document request ID ' . $documentRequest->id,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('legal.index')->with('success', 'Document release request approved successfully!');
    }

    public function denyRequest($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);
        
        if ($documentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $documentRequest->update([
            'status' => 'denied',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks')
        ]);

        // Notify requester
        $documentRequest->requester->notify(new DocumentRequestStatusNotification($documentRequest));

        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'deny_document_request',
            'description' => 'Denied document request ID ' . $documentRequest->id,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('legal.index')->with('success', 'Document release request denied.');
    }

    public function pendingRequests()
    {
        $pendingRequests = DocumentRequest::with(['document.uploader', 'requester'])
            ->where('status', 'pending')
            ->whereHas('document')
            ->latest()
            ->get();
            
        return view('legal.pending', compact('pendingRequests'));
    }

    public function approvedRequests()
    {
        $approvedRequests = DocumentRequest::with(['document.uploader', 'requester', 'approver'])
            ->where('status', 'approved')
            ->whereHas('document')
            ->latest()
            ->paginate(20);
            
        return view('legal.approved', compact('approvedRequests'));
    }

        public function deniedRequests()
    {
        $deniedRequests = DocumentRequest::with(['document.uploader', 'requester', 'approver'])
            ->where('status', 'denied')
            ->whereHas('document')
            ->latest()
            ->paginate(20);

        return view('legal.denied', compact('deniedRequests'));
    }

    public function categoryDocuments($category)
    {
        // Map URL categories to database categories
        $categoryMapping = [
            'memorandums' => 'memorandum',
            'contracts' => 'contract',
            'subpoenas' => 'subpoena',
            'affidavits' => 'affidavit',
            'cease-desist' => 'cease_desist',
            'legal-notices' => 'legal_notice',
            'policies' => 'policy',
            'legal-briefs' => 'legal_brief',
            'financial' => 'financial',
            'compliance' => 'compliance',
            'reports' => 'report'
        ];

        $dbCategory = $categoryMapping[$category] ?? 'general';
        
        // Get documents for this category (only from Legal Management)
        $documents = Document::with('uploader')
            ->where('category', $dbCategory)
            ->where('source', 'legal_management')
            ->latest()
            ->paginate(20);

        // Get category display name
        $categoryDisplayNames = [
            'memorandum' => 'Memorandums',
            'contract' => 'Contracts',
            'subpoena' => 'Subpoenas',
            'affidavit' => 'Affidavits',
            'cease_desist' => 'Cease & Desist',
            'legal_notice' => 'Legal Notices',
            'policy' => 'Policies',
            'legal_brief' => 'Legal Briefs',
            'financial' => 'Financial Documents',
            'compliance' => 'Compliance Documents',
            'report' => 'Reports',
            'general' => 'General Legal Documents'
        ];

        $categoryName = $categoryDisplayNames[$dbCategory] ?? 'Legal Documents';

        return view('legal.category', compact('documents', 'categoryName', 'category'));
    }
}