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
        
        // Build the query for documents
        $query = Document::where('source', 'legal_management')
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
            
        // Build the query for statistics
        $statsQuery = Document::where('source', 'legal_management');
        
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
            
        return view('legal.legal_documents', compact('documents', 'stats', 'search', 'category', 'status'));
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
}