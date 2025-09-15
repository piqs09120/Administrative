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
    public function legalDocuments(Request $request): \Illuminate\View\View
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status');
        
        // Build the query for Documents tab
        // Show ONLY legal documents (from legal_management and legal_submission sources)
        $query = Document::whereIn('source', ['legal_management', 'legal_submission', 'ai_builder'])
            ->with(['uploader' => function($q) {
                $q->select('Dept_no', 'employee_name', 'dept_name');
            }]);
            
        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%');
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
        // Stats should only include legal documents
        $statsQuery = Document::whereIn('source', ['legal_management', 'legal_submission', 'ai_builder']);
        
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
    public function approveDocument(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        $doc->update(['status' => 'active']);
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Document approved.']);
        }
        return back()->with('success', 'Document approved.');
    }

    /** Review: Reject */
    public function rejectDocument(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        $doc->update(['status' => 'rejected']);
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Document rejected.']);
        }
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

        $base = Document::whereIn('source', ['legal_management', 'legal_submission', 'ai_builder'])
            ->whereBetween('created_at', [$from, $to]);
        $createdByDept = (clone $base)->select('department', \DB::raw('count(*) as count'))->groupBy('department')->get();
        $types = (clone $base)->select('category', \DB::raw('count(*) as count'))->groupBy('category')->get();
        $expiring = Document::whereIn('source', ['legal_management', 'legal_submission', 'ai_builder'])
            ->where('retention_until', '>=', now())
            ->where('retention_until', '<=', now()->addDays(90))
            ->get();
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
            'priority' => 'required|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|string',
            'employee_involved' => 'nullable|string|max:255',
            'incident_date' => 'nullable|date',
            'incident_location' => 'nullable|string|max:255'
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
                'employee_involved' => $request->employee_involved,
                'incident_date' => $request->incident_date,
                'incident_location' => $request->incident_location,
            ]);


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
     * Comprehensive legal case review interface
     */
    public function reviewCase($id)
    {
        $case = \App\Models\LegalCase::with(['assignedTo', 'createdBy', 'documents'])->findOrFail($id);
        
        // Get case statistics
        $stats = [
            'days_open' => $case->created_at ? $case->created_at->diffInDays(now()) : 0,
            'evidence_count' => $case->documents()->count(),
            'witness_count' => 0, // To be implemented
            'notes_count' => 0, // To be implemented
        ];
        
        return view('legal.case_review', compact('case', 'stats'));
    }

    /**
     * Compliance assessment for a legal case
     */
    public function complianceAssessment($id)
    {
        $case = \App\Models\LegalCase::with(['assignedTo', 'createdBy', 'documents'])->findOrFail($id);
        
        // Mock compliance data - in real implementation, this would come from a compliance service
        $complianceData = [
            'overall_score' => 85,
            'issues_found' => 3,
            'critical_issues' => 1,
            'categories' => [
                'labor_law' => ['score' => 75, 'status' => 'needs_review', 'issues' => ['Overtime calculation errors', 'Break time violations']],
                'health_safety' => ['score' => 95, 'status' => 'compliant', 'issues' => []],
                'data_protection' => ['score' => 60, 'status' => 'non_compliant', 'issues' => ['Missing data breach procedures', 'Inadequate consent forms']],
                'financial' => ['score' => 90, 'status' => 'compliant', 'issues' => []],
                'hospitality' => ['score' => 80, 'status' => 'partial', 'issues' => ['Food safety documentation incomplete']]
            ],
            'risks' => [
                ['type' => 'high', 'title' => 'Data Breach Potential', 'description' => 'The incident may involve unauthorized access to customer data'],
                ['type' => 'medium', 'title' => 'Labor Law Violation', 'description' => 'Potential violation of overtime regulations']
            ],
            'recommendations' => [
                'immediate' => ['Notify data protection authority within 72 hours', 'Secure all affected systems immediately'],
                'short_term' => ['Conduct internal investigation', 'Update data protection policies'],
                'long_term' => ['Implement compliance monitoring', 'Regular compliance audits']
            ]
        ];
        
        return view('legal.compliance_assessment', compact('case', 'complianceData'));
    }

    /**
     * Start investigation for a legal case
     */
    public function startInvestigation(Request $request, $id)
    {
        $request->validate([
            'investigation_type' => 'required|string|in:internal,external,joint',
            'investigation_scope' => 'required|string|max:1000',
            'assigned_investigators' => 'required|array',
            'expected_completion_date' => 'required|date|after:today'
        ]);

        try {
            $case = \App\Models\LegalCase::findOrFail($id);
            
            // Update case with investigation details
            $case->update([
                'status' => 'ongoing',
                'metadata' => array_merge($case->metadata ?? [], [
                    'investigation' => [
                        'type' => $request->investigation_type,
                        'scope' => $request->investigation_scope,
                        'assigned_investigators' => $request->assigned_investigators,
                        'expected_completion_date' => $request->expected_completion_date,
                        'started_at' => now()->toISOString(),
                        'started_by' => auth()->id()
                    ]
                ])
            ]);

            // Log the action
            AccessLog::create([
                'user_id' => auth()->id(),
                'action' => 'start_legal_investigation',
                'description' => "Started investigation for legal case ID {$case->id}",
                'ip_address' => request()->ip()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investigation started successfully!',
                    'case' => $case
                ]);
            }

            return redirect()->back()->with('success', 'Investigation started successfully!');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error starting investigation: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error starting investigation: ' . $e->getMessage()]);
        }
    }

    /**
     * Add evidence to a legal case
     */
    public function addEvidence(Request $request, $id)
    {
        $request->validate([
            'evidence_type' => 'required|string|max:255',
            'evidence_description' => 'required|string|max:1000',
            'evidence_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mp3,wav|max:10240',
            'evidence_date' => 'required|date'
        ]);

        try {
            $case = \App\Models\LegalCase::findOrFail($id);
            
            // Handle file upload
            $file = $request->file('evidence_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('legal_evidence', $fileName, 'public');

            // Create evidence document
            $document = Document::create([
                'title' => $request->evidence_description,
                'description' => "Evidence for case: {$case->case_title}",
                'category' => 'legal_evidence',
                'file_path' => $filePath,
                'uploaded_by' => auth()->id(),
                'status' => 'active',
                'source' => 'legal_management',
                'linked_case_id' => $case->id,
                'metadata' => [
                    'evidence_type' => $request->evidence_type,
                    'evidence_date' => $request->evidence_date,
                    'case_id' => $case->id
                ]
            ]);

            // Log the action
            AccessLog::create([
                'user_id' => auth()->id(),
                'action' => 'add_legal_evidence',
                'description' => "Added evidence to legal case ID {$case->id}",
                'ip_address' => request()->ip()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Evidence added successfully!',
                    'document' => $document
                ]);
            }

            return redirect()->back()->with('success', 'Evidence added successfully!');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding evidence: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error adding evidence: ' . $e->getMessage()]);
        }
    }

    /**
     * Add legal notes to a case
     */
    public function addNotes(Request $request, $id)
    {
        $request->validate([
            'note_type' => 'required|string|in:investigation,legal,compliance,general',
            'note_content' => 'required|string|max:2000',
            'note_priority' => 'required|string|in:low,normal,high,urgent'
        ]);

        try {
            $case = \App\Models\LegalCase::findOrFail($id);
            
            // Add note to case metadata
            $metadata = $case->metadata ?? [];
            $notes = $metadata['notes'] ?? [];
            
            $notes[] = [
                'id' => uniqid(),
                'type' => $request->note_type,
                'content' => $request->note_content,
                'priority' => $request->note_priority,
                'created_at' => now()->toISOString(),
                'created_by' => auth()->id(),
                'created_by_name' => auth()->user()->name ?? 'Unknown'
            ];
            
            $metadata['notes'] = $notes;
            $case->update(['metadata' => $metadata]);

            // Log the action
            AccessLog::create([
                'user_id' => auth()->id(),
                'action' => 'add_legal_note',
                'description' => "Added note to legal case ID {$case->id}",
                'ip_address' => request()->ip()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Note added successfully!',
                    'note' => end($notes)
                ]);
            }

            return redirect()->back()->with('success', 'Note added successfully!');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding note: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error adding note: ' . $e->getMessage()]);
        }
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
     * Escalate a legal case
     */
    public function escalateCase(Request $request, $id)
    {
        $request->validate([
            'escalate_to' => 'required|string|max:255',
            'escalation_reason' => 'required|string|max:255',
            'escalation_notes' => 'required|string|max:1000'
        ]);

        try {
            $case = \App\Models\LegalCase::findOrFail($id);
            
            // Update case with escalation details
            $case->update([
                'status' => 'escalated',
                'metadata' => array_merge($case->metadata ?? [], [
                    'escalate_to' => $request->escalate_to,
                    'escalation_reason' => $request->escalation_reason,
                    'escalation_notes' => $request->escalation_notes,
                    'escalated_at' => now()->toISOString(),
                    'escalated_by' => auth()->id()
                ])
            ]);

            // Log the action
            AccessLog::create([
                'user_id' => auth()->id(),
                'action' => 'escalate_legal_case',
                'description' => "Escalated legal case ID {$case->id} to {$request->escalate_to}",
                'ip_address' => request()->ip()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Case escalated successfully!',
                    'case' => $case
                ]);
            }

            return redirect()->back()->with('success', 'Case escalated successfully!');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error escalating case: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error escalating case: ' . $e->getMessage()]);
        }
    }

    /**
     * Put a legal case on hold
     */
    public function holdCase(Request $request, $id)
    {
        $request->validate([
            'hold_reason' => 'required|string|max:255',
            'expected_resolution_date' => 'required|date|after:today',
            'hold_notes' => 'required|string|max:1000'
        ]);

        try {
            $case = \App\Models\LegalCase::findOrFail($id);
            
            // Update case with hold details
            $case->update([
                'status' => 'on_hold',
                'metadata' => array_merge($case->metadata ?? [], [
                    'hold_reason' => $request->hold_reason,
                    'expected_resolution_date' => $request->expected_resolution_date,
                    'hold_notes' => $request->hold_notes,
                    'held_at' => now()->toISOString(),
                    'held_by' => auth()->id()
                ])
            ]);

            // Log the action
            AccessLog::create([
                'user_id' => auth()->id(),
                'action' => 'hold_legal_case',
                'description' => "Put legal case ID {$case->id} on hold - Reason: {$request->hold_reason}",
                'ip_address' => request()->ip()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Case put on hold successfully!',
                    'case' => $case
                ]);
            }

            return redirect()->back()->with('success', 'Case put on hold successfully!');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error putting case on hold: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error putting case on hold: ' . $e->getMessage()]);
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
        $documentId = $request->get('document_id') ?? $request->get('edit');
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
            'employment_contract' => [
                'title' => 'Employment Contract Template',
                'content' => $this->getEmploymentContractTemplate()
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

        // Log the action with DeptAccount Dept_no (not Laravel user id)
        try {
            $deptNo = null;
            $empId = session('emp_id');
            if ($empId) {
                $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empId)->first())->Dept_no;
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
                'action' => 'save_legal_draft',
                'description' => "Saved legal document draft: {$document->title}",
                'ip_address' => $request->ip()
            ]);
        } catch (\Throwable $e) {
            // swallow logging errors
        }

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

    private function getEmploymentContractTemplate()
    {
        return '<div style="font-family: \'Times New Roman\', serif; font-size: 12pt; line-height: 1.6; max-width: 8.5in; margin: 0 auto; padding: 1in; background: white;">
            <!-- Letterhead -->
            <div style="text-align: center; margin-bottom: 3em; border-bottom: 2px solid #000; padding-bottom: 1em;">
                <h1 style="font-size: 20pt; font-weight: bold; margin-bottom: 0.3em; text-transform: uppercase; letter-spacing: 2px; color: #000;">SOLIERA HOTEL</h1>
                <p style="font-size: 11pt; color: #333; margin: 0.2em 0; font-weight: 500;">[HOTEL ADDRESS]</p>
                <p style="font-size: 11pt; color: #333; margin: 0.2em 0; font-weight: 500;">[CITY, STATE ZIP] • Phone: [PHONE] • Email: [EMAIL]</p>
            </div>
            
            <!-- Document Title -->
            <div style="text-align: center; margin-bottom: 3em;">
                <h2 style="font-size: 18pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5em; color: #000;">EMPLOYMENT CONTRACT</h2>
                <p style="font-size: 12pt; margin: 0; color: #333;">Date: <strong>' . date('F j, Y') . '</strong></p>
            </div>
            
            <div style="margin-bottom: 2em;">
                <p style="text-align: justify; margin-bottom: 1em; text-indent: 0.5in;">
                    This Employment Contract ("Contract") is entered into on <strong>' . date('F j, Y') . '</strong> between <strong>SOLIERA HOTEL</strong> ("Company"), a corporation organized under the laws of [STATE], with its principal place of business at [HOTEL ADDRESS], and <strong>[EMPLOYEE_NAME]</strong> ("Employee"), residing at [EMPLOYEE_ADDRESS].
                </p>
            </div>
            
            <div style="margin-bottom: 2.5em;">
                <h3 style="font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.8em; border-bottom: 1px solid #000; padding-bottom: 0.3em; color: #000;">1. POSITION AND DUTIES</h3>
                <p style="text-align: justify; margin-bottom: 0.8em; text-indent: 0.5in; line-height: 1.7;">
                    Employee shall serve as <strong>[POSITION_TITLE]</strong> and shall perform such duties and responsibilities as may be assigned by the Company from time to time. Employee agrees to devote their full time, attention, and efforts to the business of the Company and to perform their duties faithfully, diligently, and to the best of their ability.
                </p>
            </div>
            
            <div style="margin-bottom: 2.5em;">
                <h3 style="font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.8em; border-bottom: 1px solid #000; padding-bottom: 0.3em; color: #000;">2. COMPENSATION</h3>
                <p style="text-align: justify; margin-bottom: 0.8em; text-indent: 0.5in; line-height: 1.7;">
                    Employee shall receive a base salary of <strong>$[SALARY_AMOUNT]</strong> per <strong>[PAY_PERIOD]</strong>, payable in accordance with the Company\'s regular payroll practices. Employee\'s salary shall be subject to review and adjustment at the Company\'s discretion.
                </p>
            </div>
            
            <div style="margin-bottom: 2.5em;">
                <h3 style="font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.8em; border-bottom: 1px solid #000; padding-bottom: 0.3em; color: #000;">3. WORK SCHEDULE</h3>
                <p style="text-align: justify; margin-bottom: 0.8em; text-indent: 0.5in; line-height: 1.7;">
                    Employee\'s regular work schedule shall be <strong>[WORK_HOURS]</strong> per week, Monday through Friday, from <strong>[START_TIME]</strong> to <strong>[END_TIME]</strong>. Employee may be required to work additional hours as necessary to fulfill their job responsibilities.
                </p>
            </div>
            
            <div style="margin-bottom: 2.5em;">
                <h3 style="font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.8em; border-bottom: 1px solid #000; padding-bottom: 0.3em; color: #000;">4. BENEFITS</h3>
                <p style="text-align: justify; margin-bottom: 0.8em; text-indent: 0.5in; line-height: 1.7;">
                    Employee shall be entitled to participate in the Company\'s benefit programs, including but not limited to:
                </p>
                <ul style="margin-left: 1.2in; margin-bottom: 0.8em; line-height: 1.6;">
                    <li style="margin-bottom: 0.4em;">Health insurance coverage</li>
                    <li style="margin-bottom: 0.4em;">Dental and vision insurance</li>
                    <li style="margin-bottom: 0.4em;">Retirement savings plan (401k)</li>
                    <li style="margin-bottom: 0.4em;">Paid time off and vacation days</li>
                    <li style="margin-bottom: 0.4em;">Sick leave and personal days</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2.5em;">
                <h3 style="font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.8em; border-bottom: 1px solid #000; padding-bottom: 0.3em; color: #000;">5. TERM OF EMPLOYMENT</h3>
                <p style="text-align: justify; margin-bottom: 0.8em; text-indent: 0.5in; line-height: 1.7;">
                    This Contract shall commence on <strong>[START_DATE]</strong> and shall continue until terminated by either party in accordance with the terms herein. Employment is at-will, meaning either party may terminate this Contract at any time, with or without cause, upon [NOTICE_PERIOD] written notice.
                </p>
            </div>
            
            <div style="margin-bottom: 2.5em;">
                <h3 style="font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.8em; border-bottom: 1px solid #000; padding-bottom: 0.3em; color: #000;">6. CONFIDENTIALITY</h3>
                <p style="text-align: justify; margin-bottom: 0.8em; text-indent: 0.5in; line-height: 1.7;">
                    Employee agrees to maintain the confidentiality of all proprietary and confidential information of the Company and shall not disclose such information to any third party without the Company\'s written consent.
                </p>
            </div>
            
            <div style="margin-bottom: 2.5em;">
                <h3 style="font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.8em; border-bottom: 1px solid #000; padding-bottom: 0.3em; color: #000;">7. GOVERNING LAW</h3>
                <p style="text-align: justify; margin-bottom: 0.8em; text-indent: 0.5in; line-height: 1.7;">
                    This Contract shall be governed by and construed in accordance with the laws of [STATE], without regard to conflict of law principles.
                </p>
            </div>
            
            <div style="margin-top: 4em;">
                <p style="text-align: justify; margin-bottom: 3em; font-size: 11pt;">
                    IN WITNESS WHEREOF, the parties have executed this Contract as of the date first written above.
                </p>
                
                <div style="display: flex; justify-content: space-between; margin-top: 3em; page-break-inside: avoid;">
                    <div style="width: 45%; text-align: center;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 0.8em; height: 1.5em; width: 100%;"></div>
                        <p style="font-weight: bold; margin: 0.5em 0; font-size: 11pt; text-transform: uppercase;">SOLIERA HOTEL</p>
                        <p style="font-size: 9pt; margin: 0.3em 0; color: #666;">Authorized Representative</p>
                        <p style="font-size: 9pt; margin: 0.3em 0; color: #666;">Date: _______________</p>
                    </div>
                    <div style="width: 45%; text-align: center;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 0.8em; height: 1.5em; width: 100%;"></div>
                        <p style="font-weight: bold; margin: 0.5em 0; font-size: 11pt; text-transform: uppercase;">[EMPLOYEE_NAME]</p>
                        <p style="font-size: 9pt; margin: 0.3em 0; color: #666;">Employee</p>
                        <p style="font-size: 9pt; margin: 0.3em 0; color: #666;">Date: _______________</p>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function getGuestAgreementTemplate()
    {
        return '<div style="font-family: \'Times New Roman\', serif; font-size: 12pt; line-height: 1.6; max-width: 8.5in; margin: 0 auto; padding: 1in;">
            <div style="text-align: center; margin-bottom: 2em;">
                <h1 style="font-size: 18pt; font-weight: bold; margin-bottom: 0.5em; text-transform: uppercase; letter-spacing: 1px;">SOLIERA HOTEL</h1>
                <p style="font-size: 10pt; color: #666; margin: 0;">[HOTEL ADDRESS]</p>
                <p style="font-size: 10pt; color: #666; margin: 0;">[CITY, STATE ZIP]</p>
            </div>
            
            <div style="text-align: center; margin-bottom: 2em;">
                <h2 style="font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1em;">GUEST ACCOMMODATION AGREEMENT</h2>
                <p style="font-size: 11pt; margin: 0;">Date: <strong>' . date('F j, Y') . '</strong></p>
            </div>
            
            <div style="margin-bottom: 2em;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1em;">
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Guest Name:</p>
                        <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">[GUEST NAME]</p>
                    </div>
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Guest ID/Passport:</p>
                        <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">[ID_NUMBER]</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1em;">
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Check-in Date:</p>
                        <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">[CHECK_IN_DATE]</p>
                    </div>
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Check-out Date:</p>
                        <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">[CHECK_OUT_DATE]</p>
                    </div>
                </div>
                <div style="margin-bottom: 1em;">
                    <p style="margin: 0; font-weight: bold;">Room Number:</p>
                    <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em; width: 200px;">[ROOM_NUMBER]</p>
                </div>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">1. ACCOMMODATION TERMS</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    The Hotel agrees to provide accommodation services to the Guest for the duration specified above. The accommodation includes the assigned room and access to designated hotel facilities as outlined in this agreement.
                </p>
                <ul style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">Room type: [ROOM_TYPE]</li>
                    <li style="margin-bottom: 0.3em;">Maximum occupancy: [MAX_OCCUPANCY] persons</li>
                    <li style="margin-bottom: 0.3em;">Included amenities: [AMENITIES]</li>
                    <li style="margin-bottom: 0.3em;">Special requests: [SPECIAL_REQUESTS]</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">2. GUEST RESPONSIBILITIES</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    The Guest agrees to comply with the following responsibilities and rules during their stay:
                </p>
                <ul style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">Maintain the room and hotel property in good condition</li>
                    <li style="margin-bottom: 0.3em;">Respect quiet hours and other guests\' privacy</li>
                    <li style="margin-bottom: 0.3em;">Report any damages or issues immediately to hotel staff</li>
                    <li style="margin-bottom: 0.3em;">Comply with all hotel policies and procedures</li>
                    <li style="margin-bottom: 0.3em;">Provide valid identification when requested</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">3. FACILITY RULES</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    The following rules apply to all hotel facilities and common areas:
                </p>
                <ul style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">No smoking in non-designated areas</li>
                    <li style="margin-bottom: 0.3em;">No pets allowed without prior approval</li>
                    <li style="margin-bottom: 0.3em;">Pool and fitness center hours: [FACILITY_HOURS]</li>
                    <li style="margin-bottom: 0.3em;">No loud music or disruptive behavior</li>
                    <li style="margin-bottom: 0.3em;">Proper attire required in common areas</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">4. LIABILITY AND INSURANCE</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    The Hotel\'s liability for loss or damage to Guest property is limited to the extent provided by law. The Guest is responsible for their personal belongings and any damages caused by their actions. The Hotel maintains appropriate insurance coverage for its operations.
                </p>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">5. PAYMENT TERMS</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    Payment for accommodation and services is due as follows: [PAYMENT_TERMS]. The Guest agrees to pay all applicable taxes and fees. Any additional charges incurred during the stay will be added to the final bill.
                </p>
            </div>
            
            <div style="margin-top: 3em;">
                <p style="text-align: justify; margin-bottom: 2em;">
                    By signing below, both parties acknowledge that they have read, understood, and agree to be bound by the terms and conditions of this Guest Accommodation Agreement.
                </p>
                
                <div style="display: flex; justify-content: space-between; margin-top: 2em;">
                    <div style="width: 45%;">
                        <p style="border-bottom: 1px solid #000; margin-bottom: 0.5em; height: 2em;"></p>
                        <p style="text-align: center; font-weight: bold; margin: 0;">SOLIERA HOTEL</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Authorized Representative</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Date: _______________</p>
                    </div>
                    <div style="width: 45%;">
                        <p style="border-bottom: 1px solid #000; margin-bottom: 0.5em; height: 2em;"></p>
                        <p style="text-align: center; font-weight: bold; margin: 0;">[GUEST_NAME]</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Guest</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Date: _______________</p>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function getVendorAgreementTemplate()
    {
        return '<div style="font-family: \'Times New Roman\', serif; font-size: 12pt; line-height: 1.6; max-width: 8.5in; margin: 0 auto; padding: 1in;">
            <div style="text-align: center; margin-bottom: 2em;">
                <h1 style="font-size: 18pt; font-weight: bold; margin-bottom: 0.5em; text-transform: uppercase; letter-spacing: 1px;">SOLIERA HOTEL</h1>
                <p style="font-size: 10pt; color: #666; margin: 0;">[HOTEL ADDRESS]</p>
                <p style="font-size: 10pt; color: #666; margin: 0;">[CITY, STATE ZIP]</p>
            </div>
            
            <div style="text-align: center; margin-bottom: 2em;">
                <h2 style="font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1em;">VENDOR SUPPLY AGREEMENT</h2>
                <p style="font-size: 11pt; margin: 0;">Date: <strong>' . date('F j, Y') . '</strong></p>
            </div>
            
            <div style="margin-bottom: 2em;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1em;">
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Vendor Name:</p>
                        <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">[VENDOR_NAME]</p>
                    </div>
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Vendor ID/Tax ID:</p>
                        <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">[VENDOR_ID]</p>
                    </div>
                </div>
                <div style="margin-bottom: 1em;">
                    <p style="margin: 0; font-weight: bold;">Vendor Address:</p>
                    <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">[VENDOR_ADDRESS]</p>
                </div>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">1. SUPPLY TERMS</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    The Vendor agrees to supply the following goods and/or services to Soliera Hotel in accordance with the terms and conditions set forth in this agreement:
                </p>
                <ul style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">Product/Service: [PRODUCT_SERVICE]</li>
                    <li style="margin-bottom: 0.3em;">Quantity: [QUANTITY]</li>
                    <li style="margin-bottom: 0.3em;">Unit Price: $[UNIT_PRICE]</li>
                    <li style="margin-bottom: 0.3em;">Total Contract Value: $[TOTAL_VALUE]</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">2. PRICING AND PAYMENT</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    Payment terms and conditions:
                </p>
                <ul style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">Payment method: [PAYMENT_METHOD]</li>
                    <li style="margin-bottom: 0.3em;">Payment terms: [PAYMENT_TERMS]</li>
                    <li style="margin-bottom: 0.3em;">Invoice requirements: [INVOICE_REQUIREMENTS]</li>
                    <li style="margin-bottom: 0.3em;">Late payment penalties: [LATE_PAYMENT_PENALTIES]</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">3. QUALITY STANDARDS</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    All goods and services provided under this agreement must meet the following quality standards:
                </p>
                <ul style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">Compliance with all applicable health and safety regulations</li>
                    <li style="margin-bottom: 0.3em;">Meeting specified industry standards and certifications</li>
                    <li style="margin-bottom: 0.3em;">Freshness and quality requirements as specified</li>
                    <li style="margin-bottom: 0.3em;">Proper packaging and labeling</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">4. DELIVERY TERMS</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    Delivery terms and schedule are as follows:
                </p>
                <ul style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">Delivery frequency: [DELIVERY_FREQUENCY]</li>
                    <li style="margin-bottom: 0.3em;">Delivery days: [DELIVERY_DAYS]</li>
                    <li style="margin-bottom: 0.3em;">Delivery time window: [DELIVERY_TIME]</li>
                    <li style="margin-bottom: 0.3em;">Delivery location: [DELIVERY_LOCATION]</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">5. TERM AND TERMINATION</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    This agreement shall commence on [START_DATE] and continue until [END_DATE], unless terminated earlier in accordance with the terms herein. Either party may terminate this agreement with [NOTICE_PERIOD] written notice.
                </p>
            </div>
            
            <div style="margin-top: 3em;">
                <p style="text-align: justify; margin-bottom: 2em;">
                    IN WITNESS WHEREOF, the parties have executed this Vendor Supply Agreement as of the date first written above.
                </p>
                
                <div style="display: flex; justify-content: space-between; margin-top: 2em;">
                    <div style="width: 45%;">
                        <p style="border-bottom: 1px solid #000; margin-bottom: 0.5em; height: 2em;"></p>
                        <p style="text-align: center; font-weight: bold; margin: 0;">SOLIERA HOTEL</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Authorized Representative</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Date: _______________</p>
                    </div>
                    <div style="width: 45%;">
                        <p style="border-bottom: 1px solid #000; margin-bottom: 0.5em; height: 2em;"></p>
                        <p style="text-align: center; font-weight: bold; margin: 0;">[VENDOR_NAME]</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Authorized Representative</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Date: _______________</p>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function getHRPolicyTemplate()
    {
        return '<div style="font-family: \'Times New Roman\', serif; font-size: 12pt; line-height: 1.6; max-width: 8.5in; margin: 0 auto; padding: 1in;">
            <div style="text-align: center; margin-bottom: 2em;">
                <h1 style="font-size: 18pt; font-weight: bold; margin-bottom: 0.5em; text-transform: uppercase; letter-spacing: 1px;">SOLIERA HOTEL</h1>
                <p style="font-size: 10pt; color: #666; margin: 0;">[HOTEL ADDRESS]</p>
                <p style="font-size: 10pt; color: #666; margin: 0;">[CITY, STATE ZIP]</p>
            </div>
            
            <div style="text-align: center; margin-bottom: 2em;">
                <h2 style="font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1em;">HUMAN RESOURCES POLICY</h2>
                <p style="font-size: 11pt; margin: 0;">Policy Number: <strong>[POLICY_NUMBER]</strong></p>
            </div>
            
            <div style="margin-bottom: 2em;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1em;">
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Policy Title:</p>
                        <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">[POLICY_NAME]</p>
                    </div>
                    <div style="width: 48%;">
                        <p style="margin: 0; font-weight: bold;">Effective Date:</p>
                        <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em;">' . date('F j, Y') . '</p>
                    </div>
                </div>
                <div style="margin-bottom: 1em;">
                    <p style="margin: 0; font-weight: bold;">Department:</p>
                    <p style="border-bottom: 1px solid #000; margin: 0; padding: 0.2em 0; min-height: 1.2em; width: 300px;">[DEPARTMENT]</p>
                </div>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">1. PURPOSE</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    The purpose of this policy is to establish clear guidelines and standards for [POLICY_SUBJECT] within Soliera Hotel. This policy ensures consistency, fairness, and compliance with applicable laws and regulations while promoting a positive work environment for all employees.
                </p>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">2. SCOPE</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    This policy applies to all employees of Soliera Hotel, including full-time, part-time, temporary, and contract workers. It also extends to all departments and levels of management within the organization.
                </p>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">3. POLICY STATEMENT</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    Soliera Hotel is committed to [POLICY_COMMITMENT]. All employees are expected to adhere to the following principles and guidelines:
                </p>
                <ul style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">[PRINCIPLE_1]</li>
                    <li style="margin-bottom: 0.3em;">[PRINCIPLE_2]</li>
                    <li style="margin-bottom: 0.3em;">[PRINCIPLE_3]</li>
                    <li style="margin-bottom: 0.3em;">[PRINCIPLE_4]</li>
                </ul>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">4. PROCEDURES</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    The following procedures shall be followed to ensure proper implementation of this policy:
                </p>
                <ol style="margin-left: 1in; margin-bottom: 0.5em;">
                    <li style="margin-bottom: 0.3em;">[PROCEDURE_1]</li>
                    <li style="margin-bottom: 0.3em;">[PROCEDURE_2]</li>
                    <li style="margin-bottom: 0.3em;">[PROCEDURE_3]</li>
                    <li style="margin-bottom: 0.3em;">[PROCEDURE_4]</li>
                </ol>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">5. COMPLIANCE AND ENFORCEMENT</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    Compliance with this policy is mandatory for all employees. Violations of this policy may result in disciplinary action, up to and including termination of employment. The Human Resources Department is responsible for monitoring compliance and addressing any violations.
                </p>
            </div>
            
            <div style="margin-bottom: 2em;">
                <h3 style="font-size: 12pt; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5em; border-bottom: 1px solid #000; padding-bottom: 0.2em;">6. REVIEW AND UPDATES</h3>
                <p style="text-align: justify; margin-bottom: 0.5em; text-indent: 0.5in;">
                    This policy will be reviewed annually or as needed to ensure it remains current and effective. Any updates or changes will be communicated to all employees through appropriate channels.
                </p>
            </div>
            
            <div style="margin-top: 3em;">
                <div style="display: flex; justify-content: space-between; margin-top: 2em;">
                    <div style="width: 45%;">
                        <p style="border-bottom: 1px solid #000; margin-bottom: 0.5em; height: 2em;"></p>
                        <p style="text-align: center; font-weight: bold; margin: 0;">[APPROVER_NAME]</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Human Resources Director</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Date: _______________</p>
                    </div>
                    <div style="width: 45%;">
                        <p style="border-bottom: 1px solid #000; margin-bottom: 0.5em; height: 2em;"></p>
                        <p style="text-align: center; font-weight: bold; margin: 0;">[GENERAL_MANAGER]</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">General Manager</p>
                        <p style="text-align: center; font-size: 10pt; margin: 0;">Date: _______________</p>
                    </div>
                </div>
            </div>
        </div>';
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

    /**
     * Send an approved document for e-signature (provider-agnostic stub).
     */
    public function sendForESign(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        if ($document->status !== 'approved' && $document->status !== 'pending_signature') {
            return response()->json(['success' => false, 'message' => 'Document must be approved before sending for e-signature'], 422);
        }

        // Collect signer data
        $request->validate([
            'hotel_signer_name' => 'required|string|max:255',
            'hotel_signer_email' => 'required|email',
            'vendor_signer_name' => 'required|string|max:255',
            'vendor_signer_email' => 'required|email',
        ]);

        $signers = [
            'hotel' => [
                'name' => $request->hotel_signer_name,
                'email' => $request->hotel_signer_email,
                'role' => 'HOTEL_SIGNER'
            ],
            'vendor' => [
                'name' => $request->vendor_signer_name,
                'email' => $request->vendor_signer_email,
                'role' => 'VENDOR_SIGNER'
            ]
        ];

        // Mark as sent (simulate provider call). Integrate actual provider here.
        $document->update([
            'signature_status' => 'sent',
            'signers' => $signers,
            'workflow_log' => array_merge($document->workflow_log ?? [], [[
                'at' => now()->toIso8601String(),
                'action' => 'send_esign',
                'by' => auth()->id(),
            ]])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'E-signature request sent',
        ]);
    }

    /**
     * Webhook endpoint for e-signature provider (provider-agnostic stub).
     */
    public function esignWebhook(Request $request)
    {
        // Validate payload as needed (HMAC, token)
        $documentId = $request->input('document_id');
        $status = $request->input('status'); // completed, declined, failed
        $fileUrl = $request->input('file_url');

        $document = Document::find($documentId);
        if (!$document) {
            return response()->json(['success' => false], 404);
        }

        if ($status === 'completed') {
            // Optionally download signed PDF and store
            $path = null;
            try {
                if ($fileUrl) {
                    $contents = file_get_contents($fileUrl);
                    $stored = 'legal/signed/' . ($document->reference_id ?? ('DOC-' . $document->id)) . '-' . now()->format('YmdHis') . '.pdf';
                    \Storage::disk('local')->put($stored, $contents);
                    $path = $stored;
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to download signed file', ['err' => $e->getMessage()]);
            }

            $document->update([
                'signature_status' => 'completed',
                'signed_at' => now(),
                'final_pdf_path' => $path ?? $document->final_pdf_path,
            ]);
        } else {
            $document->update(['signature_status' => $status]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Monitoring summary: status counts, signatures, expiring/renewals
     */
    public function monitoringSummary(Request $request)
    {
        $base = Document::query();
        // Filters
        if ($dept = $request->get('department')) { $base->where('department', $dept); }
        if ($type = $request->get('type')) { $base->where('category', $type); }

        $total = (clone $base)->count();
        $pending = (clone $base)->where('status','pending_review')->count();
        $approved = (clone $base)->where('status','active')->count();
        $rejected = (clone $base)->where('status','rejected')->count();
        $returned = (clone $base)->where('status','returned')->count();
        $drafts = (clone $base)->where('status','draft')->count();

        // Signature states
        $signing = (clone $base)->where('signature_status','sent')->count();
        $signed = (clone $base)->where('signature_status','completed')->count();

        // Expiring (retention within 90d) and renewals due
        $expiring = (clone $base)->whereNotNull('retention_until')
            ->whereBetween('retention_until', [now(), now()->addDays(90)])
            ->count();

        // Upcoming renewal (metadata->renewal_date within 60d)
        $renewals = (clone $base)->whereRaw("JSON_EXTRACT(metadata, '$.renewal_date') IS NOT NULL")
            ->get()
            ->filter(function($d){
                $date = optional(optional(collect($d->metadata))->get('renewal_date'));
                if (!$date) return false;
                try { $dt = \Carbon\Carbon::parse($date); } catch (\Throwable $e) { return false; }
                return $dt->between(now(), now()->addDays(60));
            })->count();

        return response()->json([
            'success' => true,
            'counts' => compact('total','pending','approved','rejected','returned','drafts','signing','signed','expiring','renewals')
        ]);
    }

    /**
     * Monitoring list: paginated documents with key fields for table
     */
    public function monitoringList(Request $request)
    {
        $query = Document::query();
        if ($dept = $request->get('department')) { $query->where('department', $dept); }
        if ($type = $request->get('type')) { $query->where('category', $type); }
        if ($status = $request->get('status')) { $query->where('status', $status); }
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search){
                $q->where('title','like',"%$search%")
                  ->orWhere('reference_id','like',"%$search%");
            });
        }

        $docs = $query->latest()->paginate(15);

        $items = collect($docs->items())->map(function($d){
            $meta = $d->metadata ?? [];
            return [
                'id' => $d->id,
                'reference_id' => $d->reference_id,
                'title' => $d->title,
                'category' => $d->category,
                'department' => $d->department,
                'status' => $d->status,
                'signature_status' => $d->signature_status,
                'renewal_date' => $meta['renewal_date'] ?? null,
                'retention_until' => optional($d->retention_until)->format('Y-m-d'),
                'created_at' => optional($d->created_at)->format('Y-m-d'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $docs->currentPage(),
                'last_page' => $docs->lastPage(),
                'total' => $docs->total()
            ]
        ]);
    }

    /**
     * AI Document Builder - Main interface
     */
    public function aiDocumentBuilder()
    {
        return view('legal.ai_document_builder');
    }

    /**
     * Generate AI content for a specific section
     */
    public function aiGenerateSection(Request $request)
    {
        $request->validate([
            'section_id' => 'required|string',
            'prompt' => 'required|string',
            'document_type' => 'required|string',
            'context' => 'nullable|array'
        ]);

        try {
            $geminiService = app(\App\Services\GeminiService::class);
            
            // Build enhanced prompt with context
            $enhancedPrompt = $this->buildEnhancedPrompt(
                $request->prompt,
                $request->document_type,
                $request->context ?? []
            );

            // Generate content using Gemini
            $response = $geminiService->generateContent($enhancedPrompt);
            
            if ($response && !isset($response['error'])) {
                return response()->json([
                    'success' => true,
                    'content' => $response['content'] ?? $response
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response['error'] ?? 'Failed to generate content'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('AI section generation failed', [
                'error' => $e->getMessage(),
                'section_id' => $request->section_id,
                'document_type' => $request->document_type
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate AI suggestions for entire document
     */
    public function aiGenerateDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'title' => 'required|string',
            'department' => 'required|string',
            'priority' => 'required|string',
            'sections' => 'required|array'
        ]);

        try {
            $geminiService = app(\App\Services\GeminiService::class);
            
            // Build comprehensive prompt for document analysis
            $prompt = $this->buildDocumentAnalysisPrompt(
                $request->document_type,
                $request->title,
                $request->department,
                $request->priority,
                $request->sections
            );

            $response = $geminiService->generateContent($prompt);
            
            if ($response && !isset($response['error'])) {
                // Parse response into structured suggestions
                $suggestions = $this->parseDocumentSuggestions($response);
                
                return response()->json([
                    'success' => true,
                    'suggestions' => $suggestions
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response['error'] ?? 'Failed to generate suggestions'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('AI document generation failed', [
                'error' => $e->getMessage(),
                'document_type' => $request->document_type
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save AI-generated document as draft
     */
    public function saveAiDocument(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'priority' => 'required|string',
            'type' => 'required|string',
            'sections' => 'required|array'
        ]);

        try {
            // Combine all sections into final content
            $content = $this->combineDocumentSections($request->sections, $request->type);
            
            $document = Document::create([
                'title' => $request->title,
                'description' => 'AI-generated document created with AI Document Builder',
                'category' => $request->type,
                'department' => $request->department,
                'status' => 'draft',
                'source' => 'ai_builder',
                'file_path' => '',
                'uploader_id' => auth()->id(),
                'uploaded_by' => auth()->id(),
                'metadata' => [
                    'ai_generated' => true,
                    'priority' => $request->priority,
                    'sections' => $request->sections,
                    'content' => $content,
                    'created_in_ai_builder' => true,
                    'last_saved' => now()->toISOString()
                ],
            ]);

            $document->update(['reference_id' => 'AI-' . str_pad($document->id, 6, '0', STR_PAD_LEFT)]);

            // Log the action with DeptAccount Dept_no (not Laravel user id)
            try {
                $deptNo = null;
                $empId = session('emp_id');
                if ($empId) {
                    $deptNo = optional(\App\Models\DeptAccount::where('employee_id', $empId)->first())->Dept_no;
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
                    'action' => 'save_ai_document',
                    'description' => "Saved AI-generated document: {$document->title}",
                    'ip_address' => $request->ip()
                ]);
            } catch (\Throwable $e) {
                // swallow logging errors
            }

            return response()->json([
                'success' => true,
                'message' => 'Document saved as draft successfully',
                'document_id' => $document->id,
                'reference_id' => $document->reference_id
            ]);
        } catch (\Exception $e) {
            \Log::error('AI document save failed', [
                'error' => $e->getMessage(),
                'title' => $request->title
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit AI-generated document for review
     */
    public function submitAiDocument(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'priority' => 'required|string',
            'type' => 'required|string',
            'sections' => 'required|array'
        ]);

        try {
            // Combine all sections into final content
            $content = $this->combineDocumentSections($request->sections, $request->type);
            
            $document = Document::create([
                'title' => $request->title,
                'description' => 'AI-generated document submitted for legal review',
                'category' => $request->type,
                'department' => $request->department,
                'status' => 'pending_review',
                'source' => 'ai_builder',
                'file_path' => '',
                'uploader_id' => auth()->id(),
                'uploaded_by' => auth()->id(),
                'metadata' => [
                    'ai_generated' => true,
                    'priority' => $request->priority,
                    'sections' => $request->sections,
                    'content' => $content,
                    'created_in_ai_builder' => true,
                    'submitted_for_review' => now()->toISOString()
                ],
            ]);

            $document->update(['reference_id' => 'AI-' . str_pad($document->id, 6, '0', STR_PAD_LEFT)]);

            // Run AI analysis on submission
            try {
                $geminiService = app(\App\Services\GeminiService::class);
                $analysis = $geminiService->analyzeDocument($content);
                
                if (is_array($analysis) && empty($analysis['error'])) {
                    $document->update([
                        'ai_analysis' => $analysis,
                        'category' => $analysis['category'] ?? $document->category,
                        'legal_risk_score' => $analysis['legal_risk_score'] ?? $document->legal_risk_score
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Auto AI analysis on AI document submit failed', [
                    'doc_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Log the action
            AccessLog::create([
                'user_id' => auth()->id(),
                'action' => 'submit_ai_document',
                'description' => "Submitted AI-generated document for review: {$document->title}",
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document submitted for review successfully',
                'document_id' => $document->id,
                'reference_id' => $document->reference_id
            ]);
        } catch (\Exception $e) {
            \Log::error('AI document submit failed', [
                'error' => $e->getMessage(),
                'title' => $request->title
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error submitting document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build enhanced prompt with context
     */
    public function buildEnhancedPrompt($basePrompt, $documentType, $context)
    {
        $contextStr = '';
        if (!empty($context)) {
            $contextStr = "\n\nContext:\n";
            foreach ($context as $key => $value) {
                $contextStr .= "- {$key}: {$value}\n";
            }
        }

        return "You are a legal document expert. Generate professional, legally sound content for the following request:\n\n" .
               "Document Type: {$documentType}\n" .
               "Request: {$basePrompt}{$contextStr}\n\n" .
               "Please provide:\n" .
               "1. Professional, clear, and legally appropriate content\n" .
               "2. Proper legal terminology and structure\n" .
               "3. Complete sentences and paragraphs\n" .
               "4. No placeholders or incomplete sections\n\n" .
               "Generate the content now:";
    }

    /**
     * Build document analysis prompt
     */
    public function buildDocumentAnalysisPrompt($documentType, $title, $department, $priority, $sections)
    {
        $sectionsContent = '';
        foreach ($sections as $section) {
            if (!empty($section['content'])) {
                $sectionsContent .= "\n{$section['id']}: {$section['content']}\n";
            }
        }

        return "You are a legal document expert. Analyze this {$documentType} document and provide suggestions:\n\n" .
               "Document: {$title}\n" .
               "Department: {$department}\n" .
               "Priority: {$priority}\n" .
               "Content: {$sectionsContent}\n\n" .
               "Please provide suggestions in this format:\n" .
               "STRUCTURE: [suggestions for document structure]\n" .
               "CONTENT: [suggestions for content improvement]\n" .
               "COMPLIANCE: [legal compliance recommendations]\n\n" .
               "Analyze and provide suggestions:";
    }

    /**
     * Parse document suggestions from AI response
     */
    public function parseDocumentSuggestions($response)
    {
        $content = is_array($response) ? ($response['content'] ?? $response) : $response;
        
        $suggestions = [
            'structure' => 'No specific structure suggestions.',
            'content' => 'No specific content suggestions.',
            'compliance' => 'No specific compliance notes.'
        ];

        // Parse structured response
        if (preg_match('/STRUCTURE:\s*(.+?)(?=CONTENT:|$)/s', $content, $matches)) {
            $suggestions['structure'] = trim($matches[1]);
        }
        if (preg_match('/CONTENT:\s*(.+?)(?=COMPLIANCE:|$)/s', $content, $matches)) {
            $suggestions['content'] = trim($matches[1]);
        }
        if (preg_match('/COMPLIANCE:\s*(.+?)$/s', $content, $matches)) {
            $suggestions['compliance'] = trim($matches[1]);
        }

        return $suggestions;
    }

    /**
     * Combine document sections into final content
     */
    public function combineDocumentSections($sections, $documentType)
    {
        $content = "<h1>{$documentType}</h1>\n\n";
        
        foreach ($sections as $sectionId => $sectionContent) {
            if (!empty($sectionContent)) {
                $content .= "<h2>Section: {$sectionId}</h2>\n";
                $content .= "<p>{$sectionContent}</p>\n\n";
            }
        }
        
        return $content;
    }

    /**
     * Bulk upload documents
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,doc,docx,txt|max:20480',
            'category' => 'required|string|max:255',
            'department' => 'required|string|max:255'
        ]);

        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $path = $file->store('legal_documents', 'public');
                
                Document::create([
                    'title' => $file->getClientOriginalName(),
                    'description' => 'Bulk uploaded document',
                    'category' => $request->category,
                    'department' => $request->department,
                    'status' => 'pending_review',
                    'source' => 'bulk_upload',
                    'file_path' => $path,
                    'uploader_id' => auth()->id(),
                    'uploaded_by' => auth()->id(),
                ]);
                
                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to upload {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        if ($uploadedCount > 0) {
            return response()->json([
                'success' => true,
                'message' => "Successfully uploaded {$uploadedCount} documents",
                'uploaded_count' => $uploadedCount,
                'errors' => $errors
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No documents were uploaded',
                'errors' => $errors
            ], 400);
        }
    }
}