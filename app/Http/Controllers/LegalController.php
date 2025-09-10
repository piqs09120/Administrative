<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacilityReservation;
use App\Notifications\DocumentRequestStatusNotification;
use App\Models\AccessLog;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class LegalController extends Controller
{
    /**
     * Legal Management Dashboard (Case Deck)
     */
    public function index()
    {
        return redirect()->route('legal.case_deck');
    }

    /**
     * Case Deck - Main dashboard with overview of all legal cases
     */
    public function caseDeck()
    {
        // Dashboard statistics - Updated to match requested card display
        $stats = [
            'total_cases' => \App\Models\LegalCase::count() ?? 0,
            'approved_cases' => \App\Models\LegalCase::where('status', 'completed')->count() ?? 0,
            'pending_cases' => \App\Models\LegalCase::where('status', 'pending')->count() ?? 0,
            'declined_cases' => \App\Models\LegalCase::where('status', 'rejected')->count() ?? 0,
            'ongoing_cases' => \App\Models\LegalCase::where('status', 'ongoing')->count() ?? 0,
            'active_cases' => \App\Models\LegalCase::where('status', 'active')->count() ?? 0,
            'total_documents' => Document::where('source', 'legal_management')->count(),
            'pending_reviews' => DocumentRequest::where('status', 'pending')->count(),
            'facility_legal_tasks' => \App\Models\ReservationTask::where('task_type', 'legal_review')
                ->where('status', 'pending')
                ->where('assigned_to_module', 'LM')
                ->count()
        ];

        // Recent legal cases
        $recentCases = \App\Models\LegalCase::latest()->take(5)->get() ?? collect([]);
        
        // Recent legal documents
        $recentDocuments = Document::where('source', 'legal_management')
            ->latest()
            ->take(5)
            ->get();
            
        // Pending legal review tasks
        $pendingLegalReviewTasks = \App\Models\ReservationTask::with(['facilityReservation.facility', 'facilityReservation.reserver'])
            ->where('task_type', 'legal_review')
            ->where('status', 'pending')
            ->where('assigned_to_module', 'LM')
            ->latest()
            ->take(5)
            ->get();

        // All cases for the main grid
        $cases = \App\Models\LegalCase::with(['assignedTo', 'createdBy'])
            ->latest()
            ->paginate(20);

        return view('legal.case_deck', compact('stats', 'recentCases', 'recentDocuments', 'pendingLegalReviewTasks', 'cases'));
    }

    /**
     * Legal Documents - Document management
     */
    public function legalDocuments(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status');
        
        // Build the query for Documents tab
        // Strictly exclude all internally-created (legal_management) items from this tab
        $query = Document::where('source', '!=', 'legal_management')
            ->with(['uploader' => function($q) {
                $q->select('Dept_no', 'employee_name', 'dept_name');
            }]);
            
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Apply category filter
        if ($category) {
            $query->where('category', $category);
        }
        
        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }
        
        // Get paginated documents with all necessary fields
        $documents = $query->latest()->paginate(20);
        // Created via internal drafting (for Create tab table)
        // Show drafts and submitted-for-review (and keep already active ones visible here too)
        $createdDocuments = Document::where('source', 'legal_management')
            ->whereIn('status', ['draft', 'pending_review', 'active'])
            ->latest()
            ->take(50)
            ->get();
            
        // Build the query for statistics
        // Stats can still include all documents handled in this module
        $statsQuery = Document::query();
        
        // Apply the same filters to stats query
        if ($search) {
            $statsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        if ($category) {
            $statsQuery->where('category', $category);
        }
        
        // Get document statistics with filters applied
        $stats = [
            'total' => $statsQuery->count(),
            'active' => $statsQuery->where('status', 'active')->count(),
            'pending_review' => $statsQuery->where('status', 'pending_review')->count(),
            'archived' => $statsQuery->where('status', 'archived')->count(),
        ];
            
        return view('legal.legal_documents', compact('documents', 'createdDocuments', 'stats', 'search', 'category', 'status'));
    }

    /**
     * Show internal legal document creation form (draft/publish)
     */
    public function createInternalDocument()
    {
        // Simple built-in templates (can be moved to DB later)
        $templates = [
            [
                'key' => 'guest_agreement',
                'name' => 'Guest Accommodation Agreement',
                'title' => 'Guest Accommodation Agreement',
                'content' => "This Guest Accommodation Agreement is made between [[PARTY_A]] and [[PARTY_B]] for the stay commencing on [[EFFECTIVE_DATE]]. Fees: [[AMOUNT]]. Terms & Conditions: [[TERMS]]."
            ],
            [
                'key' => 'vendor_contract',
                'name' => 'Vendor Supply Contract',
                'title' => 'Vendor Supply Contract',
                'content' => "This Vendor Supply Contract is entered into by and between [[PARTY_A]] and [[PARTY_B]] effective [[EFFECTIVE_DATE]]. Consideration: [[AMOUNT]]. Scope of Work: [[TERMS]]."
            ],
            [
                'key' => 'hr_policy',
                'name' => 'HR Policy (General)',
                'title' => 'Human Resources Policy',
                'content' => "This HR Policy sets expectations and guidelines effective [[EFFECTIVE_DATE]]. Applicability: All employees. Summary: [[TERMS]]."
            ],
        ];

        return view('legal.create_document', compact('templates'));
    }

    /**
     * Store internal legal document
     */
    public function storeInternalDocument(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:500',
            'parties' => 'nullable|string|max:500',
            'amount' => 'nullable|numeric|min:0',
            'effective_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:effective_date',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:20480',
            'action' => 'required|in:draft,submit'
        ]);

        // Status handling: submit -> pending_review, draft -> draft
        $status = $request->action === 'submit' ? 'pending_review' : 'draft';

        // Persist as a Document in existing repository tagged as legal_management
        $doc = \App\Models\Document::create([
            'title' => $request->title,
            'description' => $request->purpose,
            'category' => $request->document_type,
            'department' => $request->department,
            'status' => $status,
            'source' => 'legal_management',
            // Ensure non-null for schemas that require a value
            'file_path' => '',
            'uploader_id' => auth()->id(),
            'uploaded_by' => auth()->id(),
            'metadata' => [
                'parties' => $request->parties,
                'terms' => $request->terms,
                'amount' => $request->amount,
                'effective_date' => $request->effective_date,
                'end_date' => $request->end_date,
                'content' => $request->content,
            ],
        ]);

        // Optional file upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('legal_documents', 'public');
            $doc->update(['file_path' => $path]);
        }

        // Optionally kick AI when submitted for review and file/content available
        try {
            if ($status === 'pending_review') {
                // Prefer file text; fallback to title/description
                $text = trim(($doc->metadata['content'] ?? '') . ' ' . ($doc->title ?? '') . ' ' . ($doc->description ?? ''));
                if ($text !== '') {
                    $analysis = app(\App\Services\GeminiService::class)->analyzeDocument($text);
                    if (is_array($analysis) && empty($analysis['error'])) {
                        $doc->update([
                            'ai_analysis' => $analysis,
                            'category' => $analysis['category'] ?? $doc->category,
                            'legal_risk_score' => $analysis['legal_risk_score'] ?? $doc->legal_risk_score
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Auto AI analysis on submit failed', ['doc_id' => $doc->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('legal.legal_documents', ['tab' => 'create'])
            ->with('success', $status === 'pending_review' ? 'Document submitted for review.' : 'Draft saved successfully.');
    }

    /** Review: Approve */
    public function approveDocument($id)
    {
        $doc = Document::findOrFail($id);
        $doc->update(['status' => 'active']);
        return back()->with('success', 'Document approved.');
    }

    /** Review: Reject */
    public function rejectDocument(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        $doc->update(['status' => 'rejected']);
        return back()->with('success', 'Document rejected.');
    }

    /** Review: Request Revision */
    public function requestRevisionDocument(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        $doc->update(['status' => 'returned']);
        return back()->with('success', 'Revision requested.');
    }

    /** Archive and compute retention */
    public function archiveDocument(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        // Compute retention (default 5 years; per type could differ)
        $years = match($doc->category) {
            'contract' => 5,
            'policy' => 3,
            default => 2,
        };
        $retentionUntil = now()->addYears($years);
        $doc->update([
            'status' => 'archived',
            'retention_until' => $retentionUntil,
        ]);
        return back()->with('success', 'Document archived. Retention until ' . $retentionUntil->format('Y-m-d'));
    }

    /** Department submission form */
    public function submitForm()
    {
        return view('legal.submit_document');
    }

    /** Store department submission, assign Legal Document ID and set pending_review */
    public function storeSubmission(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'responsible_officer' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:20480'
        ]);

        $doc = \App\Models\Document::create([
            'title' => $request->title,
            'description' => $request->responsible_officer,
            'category' => $request->document_type,
            'department' => $request->department,
            'status' => 'pending_review',
            'source' => 'legal_submission',
            'file_path' => '',
            'uploader_id' => auth()->id(),
            'uploaded_by' => auth()->id(),
            'metadata' => [
                'submitted_date' => $request->date,
                'responsible_officer' => $request->responsible_officer,
            ],
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('legal_documents', 'public');
            $doc->update(['file_path' => $path]);
        }

        // Assign a Legal Document ID (readable)
        $doc->update(['reference_id' => 'LGL-' . str_pad($doc->id, 6, '0', STR_PAD_LEFT)]);

        return redirect()->route('legal.legal_documents', ['tab' => 'create'])->with('success', 'Submission received and set to Pending Review.');
    }

    /** Export reports (created vs submitted, types, expiring, AI risks) */
    public function exportReports(Request $request)
    {
        $from = $request->input('from') ? \Carbon\Carbon::parse($request->input('from'))->startOfDay() : now()->subMonth();
        $to = $request->input('to') ? \Carbon\Carbon::parse($request->input('to'))->endOfDay() : now();

        $base = Document::whereBetween('created_at', [$from, $to]);
        $createdByDept = (clone $base)->select('department', \DB::raw('count(*) as count'))->groupBy('department')->get();
        $types = (clone $base)->select('category', \DB::raw('count(*) as count'))->groupBy('category')->get();
        $expiring = Document::where('retention_until', '>=', now())->where('retention_until', '<=', now()->addDays(90))->get();
        $aiRisks = (clone $base)->whereNotNull('legal_risk_score')->select('legal_risk_score', \DB::raw('count(*) as count'))->groupBy('legal_risk_score')->get();

        $sheets = [
            'Created by Department' => array_merge([["Department","Count"]], $createdByDept->map(fn($r)=>[$r->department,$r->count])->toArray()),
            'Types' => array_merge([["Type","Count"]], $types->map(fn($r)=>[$r->category,$r->count])->toArray()),
            'Expiring (90d)' => array_merge([["Title","Department","Retention Until"]], $expiring->map(fn($d)=>[$d->title,$d->department,optional($d->retention_until)->format('Y-m-d')])->toArray()),
            'AI Risk' => array_merge([["Risk","Count"]], $aiRisks->map(fn($r)=>[$r->legal_risk_score,$r->count])->toArray()),
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\VisitorReportExport($sheets), 'legal_reports_'.now()->format('Ymd_His').'.xlsx');
    }

    /** Execution/Monitoring: mark as signed */
    public function markSigned(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        $doc->update(['metadata->signed_at' => now()->toDateTimeString()]);
        return back()->with('success','Document marked as signed.');
    }

    /** Execution/Monitoring: set renewal date */
    public function setRenewal(Request $request, $id)
    {
        $request->validate(['renewal_date' => 'required|date|after:today']);
        $doc = Document::findOrFail($id);
        $meta = $doc->metadata ?? [];
        $meta['renewal_date'] = $request->renewal_date;
        $doc->update(['metadata' => $meta]);
        return back()->with('success','Renewal date set.');
    }

    /** Signatures: request a signature from a signer (internal stub; can connect to e-sign later) */
    public function requestSignature(Request $request, $id)
    {
        $request->validate([
            'signer_email' => 'required|email',
            'signer_name' => 'required|string|max:255',
            'due_date' => 'nullable|date|after:today'
        ]);
        $doc = Document::findOrFail($id);
        $meta = $doc->metadata ?? [];
        $meta['signatures'] = $meta['signatures'] ?? [];
        $req = [
            'id' => (string) \Str::uuid(),
            'name' => $request->signer_name,
            'email' => $request->signer_email,
            'status' => 'requested',
            'requested_at' => now()->toDateTimeString(),
            'due_date' => $request->due_date
        ];
        $meta['signatures'][] = $req;
        $doc->update(['metadata' => $meta]);
        return back()->with('success', 'Signature request sent to ' . $request->signer_email);
    }

    /** Signatures: send a reminder (records reminder time) */
    public function remindSignature(Request $request, $id)
    {
        $request->validate(['signature_id' => 'required|string']);
        $doc = Document::findOrFail($id);
        $meta = $doc->metadata ?? [];
        foreach (($meta['signatures'] ?? []) as &$s) {
            if (($s['id'] ?? null) === $request->signature_id) {
                $s['last_reminded_at'] = now()->toDateTimeString();
            }
        }
        $doc->update(['metadata' => $meta]);
        return back()->with('success', 'Reminder recorded for signature request.');
    }

    /** Signatures: cancel a signature request */
    public function cancelSignature(Request $request, $id)
    {
        $request->validate(['signature_id' => 'required|string']);
        $doc = Document::findOrFail($id);
        $meta = $doc->metadata ?? [];
        foreach (($meta['signatures'] ?? []) as &$s) {
            if (($s['id'] ?? null) === $request->signature_id) {
                $s['status'] = 'cancelled';
                $s['cancelled_at'] = now()->toDateTimeString();
            }
        }
        $doc->update(['metadata' => $meta]);
        return back()->with('success', 'Signature request cancelled.');
    }

    /**
     * Legal Cases - Detailed case management with create functionality
     */
    public function legalCases()
    {
        $cases = \App\Models\LegalCase::with(['assignedTo', 'createdBy', 'documents'])
            ->latest()
            ->paginate(20);
            
        return view('legal.legal_cases', compact('cases'));
    }

    /**
     * Create new legal case
     */
    public function create()
    {
        return view('legal.create');
    }

    /**
     * Store new legal case
     */
    public function store(Request $request)
    {
        $request->validate([
            'case_title' => 'required|string|max:255',
            'case_description' => 'nullable|string',
            'case_type' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:department_accounts,Dept_no',
            'legal_document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240'
        ]);

        try {
            // Create legal case
            $legalCase = \App\Models\LegalCase::create([
                'case_title' => $request->case_title,
                'case_description' => $request->case_description,
                'case_type' => $request->case_type,
                'priority' => $request->priority,
                'status' => 'pending',
                'assigned_to' => $request->assigned_to,
                'created_by' => Auth::user()->Dept_no,
            ]);

            // Handle file upload if provided
            if ($request->hasFile('legal_document')) {
                $file = $request->file('legal_document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('legal_documents', $fileName, 'public');

                // Create document record
                $document = Document::create([
                    'title' => $request->case_title,
                    'description' => $request->case_description,
                    'category' => 'legal_case',
                    'file_path' => $filePath,
                    'uploaded_by' => Auth::user()->Dept_no,
                    'status' => 'active',
                    'source' => 'legal_management',
                    'linked_case_id' => $legalCase->id,
                ]);
            }

            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Legal case created successfully!',
                    'case' => $legalCase
                ]);
            }

            return redirect()->route('legal.case_deck')->with('success', 'Legal case created successfully!');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating legal case: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error creating legal case: ' . $e->getMessage()]);
        }
    }

    /**
     * Show legal case details
     */
    public function show($id)
    {
        $case = \App\Models\LegalCase::with(['assignedTo', 'createdBy', 'documents'])->findOrFail($id);
        return view('legal.show', compact('case'));
    }

    /**
     * Edit legal case
     */
    public function edit($id)
    {
        $case = \App\Models\LegalCase::findOrFail($id);
        return view('legal.edit', compact('case'));
    }

    /**
     * Update legal case
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'case_title' => 'required|string|max:255',
            'case_description' => 'nullable|string',
            'case_type' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:department_accounts,Dept_no',
            'status' => 'required|in:pending,active,on_hold,closed'
        ]);

        $case = \App\Models\LegalCase::findOrFail($id);
        $case->update($request->all());

        return redirect()->route('legal.legal_cases')->with('success', 'Legal case updated successfully!');
    }

    /**
     * Destroy legal case
     */
    public function destroy($id)
    {
        try {
            $case = \App\Models\LegalCase::findOrFail($id);
            $case->delete();

            // Log the action
            AccessLog::create([
                'user_id' => Auth::user()->Dept_no,
                'action' => 'delete_legal_case',
                'description' => 'Deleted legal case ID ' . $case->id,
                'ip_address' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Legal case deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting case: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Document request management (legacy - kept for compatibility)
     */
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

    public function approveRequest($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);
        
        if ($documentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $documentRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->Dept_no,
            'remarks' => request('remarks')
        ]);

        // Update document status to released
        $documentRequest->document->update(['status' => 'released']);

        // Notify requester
        $documentRequest->requester->notify(new DocumentRequestStatusNotification($documentRequest));

        // Log action
        AccessLog::create([
            'user_id' => Auth::user()->Dept_no,
            'action' => 'approve_document_request',
            'description' => 'Approved document request ID ' . $documentRequest->id,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('legal.pending')->with('success', 'Document release request approved successfully!');
    }

    public function denyRequest($id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);
        
        if ($documentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $documentRequest->update([
            'status' => 'denied',
            'approved_by' => Auth::user()->Dept_no,
            'remarks' => request('remarks')
        ]);

        // Notify requester
        $documentRequest->requester->notify(new DocumentRequestStatusNotification($documentRequest));

        // Log action
        AccessLog::create([
            'user_id' => Auth::user()->Dept_no,
            'action' => 'deny_document_request',
            'description' => 'Denied document request ID ' . $documentRequest->id,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('legal.pending')->with('success', 'Document release request denied.');
    }

    /**
     * Approve a legal case
     */
    public function approveCase($id)
    {
        try {
            $case = \App\Models\LegalCase::findOrFail($id);
            
            // Check if case can be approved (only pending cases)
            if ($case->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending cases can be approved.'
                ], 400);
            }

            $case->update([
                'status' => 'completed',
                'outcome' => 'approved'
            ]);

            // Log the action
            AccessLog::create([
                'user_id' => Auth::user()->Dept_no,
                'action' => 'approve_legal_case',
                'description' => 'Approved legal case ID ' . $case->id,
                'ip_address' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Legal case approved successfully!',
                'case' => $case
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving case: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Decline a legal case
     */
    public function declineCase($id)
    {
        try {
            $case = \App\Models\LegalCase::findOrFail($id);
            
            // Check if case can be declined (only pending cases)
            if ($case->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending cases can be declined.'
                ], 400);
            }

            $case->update([
                'status' => 'rejected',
                'outcome' => 'declined'
            ]);

            // Log the action
            AccessLog::create([
                'user_id' => Auth::user()->Dept_no,
                'action' => 'decline_legal_case',
                'description' => 'Declined legal case ID ' . $case->id,
                'ip_address' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Legal case declined successfully!',
                'case' => $case
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error declining case: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Category-based document views (legacy - kept for compatibility)
     */
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

    /**
     * Drafting Workspace - Word-style editor for creating legal documents
     */
    public function draftingWorkspace(Request $request)
    {
        $documentId = $request->get('document_id');
        $templateKey = $request->get('template'); // Get the raw template parameter
        
        $document = null;
        if ($documentId) {
            $document = Document::where('id', $documentId)
                ->where('source', 'legal_management')
                ->where('uploaded_by', auth()->id())
                ->first();
        }

        // Available templates
        $templates = [
            'service_contract' => [
                'title' => 'Service Contract Template',
                'content' => $this->getServiceContractTemplate()
            ],
            'guest_agreement' => [
                'title' => 'Guest Agreement Template',
                'content' => $this->getGuestAgreementTemplate()
            ],
            'vendor_agreement' => [
                'title' => 'Vendor Agreement Template',
                'content' => $this->getVendorAgreementTemplate()
            ],
            'hr_policy' => [
                'title' => 'HR Policy Template',
                'content' => $this->getHRPolicyTemplate()
            ]
        ];

        // Validate that $templateKey is a string and exists as a key in $templates
        $template = null;
        if (is_string($templateKey) && array_key_exists($templateKey, $templates)) {
            $template = $templateKey;
        }

        return view('legal.drafting_workspace', compact('document', 'templates', 'template'));
    }

    /**
     * Save document as draft
     */
    public function saveDraft(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'document_type' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'document_id' => 'nullable|exists:documents,id'
        ]);

        $data = [
            'title' => $request->title,
            'description' => 'Draft document created in drafting workspace',
            'category' => $request->document_type,
            'department' => $request->department,
            'status' => 'draft',
            'source' => 'legal_management',
            'file_path' => '',
            'uploader_id' => auth()->id(),
            'uploaded_by' => auth()->id(),
            'metadata' => [
                'content' => $request->content,
                'created_in_workspace' => true,
                'last_saved' => now()->toISOString()
            ],
        ];

        if ($request->document_id) {
            // Update existing document
            $document = Document::find($request->document_id);
            $document->update($data);
        } else {
            // Create new document
            $document = Document::create($data);
            $document->update(['reference_id' => 'LGL-' . str_pad($document->id, 6, '0', STR_PAD_LEFT)]);
        }

        // Log the action
        AccessLog::create([
            'user_id' => auth()->id(),
            'action' => 'save_legal_draft',
            'description' => "Saved legal document draft: {$document->title}",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully',
            'document_id' => $document->id,
            'reference_id' => $document->reference_id
        ]);
    }

    /**
     * Submit document for review
     */
    public function submitForReview(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'document_type' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'document_id' => 'nullable|exists:documents,id'
        ]);

        $data = [
            'title' => $request->title,
            'description' => 'Document submitted for legal review',
            'category' => $request->document_type,
            'department' => $request->department,
            'status' => 'pending_review',
            'source' => 'legal_management',
            'file_path' => '',
            'uploader_id' => auth()->id(),
            'uploaded_by' => auth()->id(),
            'metadata' => [
                'content' => $request->content,
                'created_in_workspace' => true,
                'submitted_for_review' => now()->toISOString()
            ],
        ];

        if ($request->document_id) {
            // Update existing document
            $document = Document::find($request->document_id);
            $document->update($data);
        } else {
            // Create new document
            $document = Document::create($data);
            $document->update(['reference_id' => 'LGL-' . str_pad($document->id, 6, '0', STR_PAD_LEFT)]);
        }

        // Log the action
        AccessLog::create([
            'user_id' => auth()->id(),
            'action' => 'submit_legal_document',
            'description' => "Submitted legal document for review: {$document->title}",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document submitted for review successfully',
            'document_id' => $document->id,
            'reference_id' => $document->reference_id
        ]);
    }

    /**
     * Export document as PDF or Word
     */
    public function exportDocument(Request $request, $id)
    {
        $document = Document::where('id', $id)
            ->where('source', 'legal_management')
            ->where('uploaded_by', auth()->id())
            ->firstOrFail();

        $format = $request->get('format', 'pdf');
        
        if ($format === 'pdf') {
            return $this->generatePdfExport($document);
        } elseif ($format === 'word') {
            return $this->generateWordExport($document);
        }

        return response()->json(['error' => 'Invalid format'], 400);
    }

    /**
     * Template content methods
     */
    private function getServiceContractTemplate()
    {
        return file_get_contents(resource_path('views/templates/service_contract_template.html'));
    }

    private function getGuestAgreementTemplate()
    {
        return '<h1>GUEST ACCOMMODATION AGREEMENT</h1>
        <p><strong>Guest Name:</strong> [GUEST NAME]</p>
        <p><strong>Check-in Date:</strong> [DATE]</p>
        <p><strong>Check-out Date:</strong> [DATE]</p>
        
        <h2>1. ACCOMMODATION TERMS</h2>
        <p>[Describe accommodation details]</p>
        
        <h2>2. GUEST RESPONSIBILITIES</h2>
        <p>[List guest responsibilities and rules]</p>
        
        <h2>3. FACILITY RULES</h2>
        <p>[Include facility-specific rules]</p>
        
        <h2>4. LIABILITY</h2>
        <p>[Include liability and insurance clauses]</p>
        
        <p><strong>Guest Signature:</strong> _________________ Date: _______</p>';
    }

    private function getVendorAgreementTemplate()
    {
        return '<h1>VENDOR SUPPLY AGREEMENT</h1>
        <p><strong>Vendor:</strong> [VENDOR NAME]</p>
        <p><strong>Effective Date:</strong> [DATE]</p>
        <p><strong>Term:</strong> [DURATION]</p>
        
        <h2>1. SUPPLY TERMS</h2>
        <p>[Describe goods/services to be supplied]</p>
        
        <h2>2. PRICING AND PAYMENT</h2>
        <p>[Include pricing structure and payment terms]</p>
        
        <h2>3. QUALITY STANDARDS</h2>
        <p>[Specify quality requirements]</p>
        
        <h2>4. DELIVERY TERMS</h2>
        <p>[Include delivery schedules and requirements]</p>
        
        <p><strong>Vendor Signature:</strong> _________________ Date: _______</p>';
    }

    private function getHRPolicyTemplate()
    {
        return '<h1>HUMAN RESOURCES POLICY</h1>
        <p><strong>Policy Title:</strong> [POLICY NAME]</p>
        <p><strong>Effective Date:</strong> [DATE]</p>
        <p><strong>Department:</strong> [DEPARTMENT]</p>
        
        <h2>1. PURPOSE</h2>
        <p>[Describe the purpose of this policy]</p>
        
        <h2>2. SCOPE</h2>
        <p>[Define who this policy applies to]</p>
        
        <h2>3. POLICY STATEMENT</h2>
        <p>[Include the main policy content]</p>
        
        <h2>4. PROCEDURES</h2>
        <p>[Detail implementation procedures]</p>
        
        <h2>5. COMPLIANCE</h2>
        <p>[Include compliance requirements]</p>
        
        <p><strong>Approved by:</strong> [APPROVER NAME] Date: _______</p>';
    }

    private function generatePdfExport($document)
    {
        try {
            // Get document content from metadata
            $content = $document->metadata['content'] ?? $document->description ?? 'No content available';
            
            // Clean HTML content for PDF
            $cleanContent = strip_tags($content, '<h1><h2><h3><h4><h5><h6><p><strong><em><ul><ol><li><br>');
            
            // Create HTML for PDF
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>' . htmlspecialchars($document->title) . '</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                    h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
                    h2 { color: #34495e; margin-top: 30px; }
                    h3 { color: #7f8c8d; }
                    p { margin: 10px 0; }
                    .header { text-align: center; margin-bottom: 40px; }
                    .document-info { background: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; margin-bottom: 30px; }
                    .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #7f8c8d; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>' . htmlspecialchars($document->title) . '</h1>
                </div>
                
                <div class="document-info">
                    <p><strong>Document ID:</strong> ' . htmlspecialchars($document->reference_id ?? 'N/A') . '</p>
                    <p><strong>Category:</strong> ' . htmlspecialchars($document->category ?? 'N/A') . '</p>
                    <p><strong>Department:</strong> ' . htmlspecialchars($document->department ?? 'N/A') . '</p>
                    <p><strong>Status:</strong> ' . htmlspecialchars($document->status ?? 'N/A') . '</p>
                    <p><strong>Created:</strong> ' . ($document->created_at ? $document->created_at->format('F j, Y \a\t g:i A') : 'N/A') . '</p>
                </div>
                
                <div class="content">
                    ' . $cleanContent . '
                </div>
                
                <div class="footer">
                    <p>Generated on ' . now()->format('F j, Y \a\t g:i A') . '</p>
                    <p>Soliera Legal Document Management System</p>
                </div>
            </body>
            </html>';
            
            // Generate PDF using DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'legal_document_' . ($document->reference_id ?? $document->id) . '_' . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('PDF export failed', ['document_id' => $document->id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    private function generateWordExport($document)
    {
        try {
            // Get document content from metadata
            $content = $document->metadata['content'] ?? $document->description ?? 'No content available';
            
            // Clean HTML content for Word
            $cleanContent = strip_tags($content, '<h1><h2><h3><h4><h5><h6><p><strong><em><ul><ol><li><br>');
            
            // Create a new Word document
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            
            // Set document properties
            $properties = $phpWord->getDocInfo();
            $properties->setCreator('Soliera Legal System');
            $properties->setTitle($document->title);
            $properties->setDescription('Legal Document Export');
            $properties->setSubject($document->category ?? 'Legal Document');
            
            // Add a section
            $section = $phpWord->addSection([
                'marginTop' => 720,
                'marginBottom' => 720,
                'marginLeft' => 720,
                'marginRight' => 720,
            ]);
            
            // Add title
            $section->addText($document->title, [
                'name' => 'Arial',
                'size' => 16,
                'bold' => true,
                'color' => '2c3e50'
            ], [
                'alignment' => 'center',
                'spaceAfter' => 240
            ]);
            
            // Add document information
            $section->addText('Document Information', [
                'name' => 'Arial',
                'size' => 12,
                'bold' => true,
                'color' => '34495e'
            ], [
                'spaceBefore' => 120,
                'spaceAfter' => 60
            ]);
            
            $infoText = "Document ID: " . ($document->reference_id ?? 'N/A') . "\n";
            $infoText .= "Category: " . ($document->category ?? 'N/A') . "\n";
            $infoText .= "Department: " . ($document->department ?? 'N/A') . "\n";
            $infoText .= "Status: " . ($document->status ?? 'N/A') . "\n";
            $infoText .= "Created: " . ($document->created_at ? $document->created_at->format('F j, Y \a\t g:i A') : 'N/A');
            
            $section->addText($infoText, [
                'name' => 'Arial',
                'size' => 10
            ], [
                'spaceAfter' => 240
            ]);
            
            // Add content
            $section->addText('Document Content', [
                'name' => 'Arial',
                'size' => 12,
                'bold' => true,
                'color' => '34495e'
            ], [
                'spaceBefore' => 120,
                'spaceAfter' => 60
            ]);
            
            // Convert HTML content to plain text for Word
            $plainText = strip_tags($cleanContent);
            $plainText = html_entity_decode($plainText);
            
            $section->addText($plainText, [
                'name' => 'Arial',
                'size' => 11
            ], [
                'spaceAfter' => 120
            ]);
            
            // Add footer
            $section->addTextBreak(2);
            $section->addText('Generated on ' . now()->format('F j, Y \a\t g:i A'), [
                'name' => 'Arial',
                'size' => 9,
                'italic' => true,
                'color' => '7f8c8d'
            ], [
                'alignment' => 'center'
            ]);
            
            $section->addText('Soliera Legal Document Management System', [
                'name' => 'Arial',
                'size' => 9,
                'italic' => true,
                'color' => '7f8c8d'
            ], [
                'alignment' => 'center'
            ]);
            
            // Generate filename
            $filename = 'legal_document_' . ($document->reference_id ?? $document->id) . '_' . now()->format('Y-m-d') . '.docx';
            
            // Save the document
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            
            // Create a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'word_export_');
            $objWriter->save($tempFile);
            
            // Return the file as download
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Word export failed', ['document_id' => $document->id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to generate Word document: ' . $e->getMessage()], 500);
        }
    }
}