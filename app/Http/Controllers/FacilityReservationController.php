<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Services\GeminiService;
use App\Services\DocumentTextExtractorService;
use App\Services\FacilityCalendarService;
use App\Services\SecureDocumentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Notifications\FacilityReservationStatusNotification;
use App\Models\AccessLog;
use App\Jobs\ProcessReservationDocument;
use App\Jobs\CheckAndAutoApproveReservation;
use App\Jobs\GenerateDigitalPasses;
use App\Services\VisitorService;
use App\Services\ReservationWorkflowService;

class FacilityReservationController extends Controller
{
    protected $geminiService;
    protected $textExtractor;
    protected $calendarService;
    protected $secureRepository;
    protected $visitorService;
    protected $workflowService;

    public function __construct(GeminiService $geminiService, DocumentTextExtractorService $textExtractor, FacilityCalendarService $calendarService, SecureDocumentRepository $secureRepository, VisitorService $visitorService, ReservationWorkflowService $workflowService)
    {
        $this->geminiService = $geminiService;
        $this->textExtractor = $textExtractor;
        $this->calendarService = $calendarService;
        $this->secureRepository = $secureRepository;
        $this->visitorService = $visitorService;
        $this->workflowService = $workflowService;
        
        // Role restrictions removed - all users can now approve/deny reservations
    }

    public function index()
    {
        // Show all reservations to all users - role restrictions removed
        $reservations = FacilityReservation::with(['facility', 'reserver', 'approver'])->latest()->get();
        // Facilities list for the modal form on the index page
        $facilities = Facility::where('status', 'available')->get();
        return view('facility_reservations.index', compact('reservations', 'facilities'));
    }

    public function create()
    {
        $facilities = Facility::where('status', 'available')->get();
        return view('facility_reservations.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'purpose' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240' // 10MB max
        ]);

        // Step 1: Create the reservation
        $reservation = FacilityReservation::create([
            'facility_id' => $request->facility_id,
            'reserved_by' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'purpose' => $request->purpose,
            'status' => 'pending',
            // Auto-populate requester information from authenticated user
            'requester_name' => Auth::user()->name,
            'requester_contact' => Auth::user()->email,
            'workflow_stage' => 'submitted'
        ]);
        
        // Initialize workflow log
        $reservation->logWorkflowStep('submitted', 'Facility reservation request submitted', [
            'facility' => $reservation->facility->name,
            'requester' => Auth::user()->name
        ]);

        // Step 2: Handle document upload if provided (following TO BE diagram)
        if ($request->hasFile('document')) {
            // Upload supporting document to storage (still needed for access)
            $fileName = time() . '_' . $request->file('document')->getClientOriginalName();
            $documentPath = $request->file('document')->storeAs('facility_reservations/' . $reservation->id, $fileName, 'public');
            $reservation->update(['document_path' => $documentPath]);

            $reservation->logWorkflowStep('document_upload_started', 'Starting document upload process');
            
            // Create a Document model entry for this uploaded file
            $document = \App\Models\Document::create([
                'title' => 'Reservation Document for ' . ($reservation->facility->name ?? 'Facility #' . $reservation->facility_id),
                'description' => $reservation->purpose ?? 'Supporting document for facility reservation.',
                'category' => 'general', // Will be updated by AI
                'file_path' => $documentPath,
                'uploaded_by' => Auth::id(),
                'status' => 'archived',
                'source' => 'facility_reservations', // Mark as uploaded via facility reservations
            ]);
            
            // Link the created Document to the FacilityReservation
            $reservation->update(['document_id' => $document->id]);
            
            // Store document in secure repository (now using the created Document model)
            $storeResult = $this->secureRepository->storeDocument(
                $request->file('document'), 
                $reservation->id, // still pass reservation ID for secure storage naming
                $document->id // Pass document ID for logging/linking if needed by repository
            );
            
            if ($storeResult['success']) {
                $reservation->logWorkflowStep('document_stored_secure_repository', 
                    'Document stored and indexed in secure repository', $storeResult['index']);
            }
            // Kick off AI processing workflow and task creation
            // ProcessReservationDocument job will create the document_classification task
            \App\Jobs\ProcessReservationDocument::dispatch($reservation->id);
            $reservation->logWorkflowStep('document_processing_queued', 'AI document processing queued');
        } else {
            // If no document uploaded, create a basic document_classification task as completed
            // and set initial status to ready_for_approval
            $this->workflowService->createDocumentClassificationTask($reservation, [
                'category' => 'no_document',
                'summary' => 'No document provided for this reservation.',
            ]);
            $reservation->logWorkflowStep('no_document_proceed_to_approval', 'No document uploaded, proceeding directly to approval workflow');
            // The workflow service will update current_workflow_status and dispatch CheckAndAutoApproveReservation
        }

        // Send submission confirmation (status pending) with error handling
        try {
            $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        } catch (\Exception $e) {
            Log::warning('Failed to send submission confirmation email', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('facility_reservations.index')
            ->with('success', 'Reservation request submitted! The system will automatically check facility availability and process your request.');
    }

    public function show($id)
    {
        $reservation = FacilityReservation::with(['facility', 'reserver', 'approver', 'tasks'])->findOrFail($id);
        return view('facility_reservations.show', compact('reservation'));
    }

    public function approve($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        
        if ($reservation->current_workflow_status !== 'ready_for_approval') {
            return redirect()->back()->with('error', 'This reservation is not yet ready for manual approval. All required tasks must be completed first.');
        }
        
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'This reservation has already been processed.');
        }
        
        $reservation->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks'),
            'current_workflow_status' => 'approved'
        ]);
        
        // Notify reserver
        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve_facility_reservation',
            'description' => 'Approved facility reservation ID ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);
        
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation approved!');
    }

    public function deny($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        
        if ($reservation->status !== 'pending' && $reservation->status !== 'denied') {
            return redirect()->back()->with('error', 'This reservation has already been processed or cannot be denied at this stage.');
        }
        
        $reservation->update([
            'status' => 'denied',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks'),
            'current_workflow_status' => 'denied'
        ]);
        
        // Notify reserver
        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'deny_facility_reservation',
            'description' => 'Denied facility reservation ID ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);
        
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation denied.');
    }

    public function legalReview($id)
    {
        $reservation = FacilityReservation::with(['facility', 'reserver', 'approver', 'tasks'])->findOrFail($id);
        
        $legalTask = $reservation->tasks()->where('task_type', 'legal_review')->first();
        if (!$legalTask) {
            return redirect()->back()->with('error', 'Legal review is not required or task does not exist for this reservation.');
        }
        
        // Ensure the task is still pending
        if ($legalTask->status !== 'pending') {
            return redirect()->back()->with('error', 'This legal review task has already been processed.');
        }
        
        return view('facility_reservations.legal_review', compact('reservation', 'legalTask'));
    }

    public function legalApprove($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        $legalTask = $reservation->tasks()->where('task_type', 'legal_review')->first();
        
        if (!$legalTask || $legalTask->status !== 'pending') {
            return redirect()->back()->with('error', 'Legal review task not found or already completed.');
        }
        
        $this->workflowService->updateTaskStatus($legalTask, 'completed', request('legal_comment'));
        
        // No direct status update on reservation here; workflow service handles it
        
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'legal_approve_facility_reservation',
            'description' => 'Legal approved facility reservation ID ' . $reservation->id . ' (Task ID: ' . $legalTask->id . ')',
            'ip_address' => request()->ip()
        ]);
        
        return redirect()->route('facility_reservations.show', $id)->with('success', 'Legal review completed successfully!');
    }

    public function legalFlag($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        $legalTask = $reservation->tasks()->where('task_type', 'legal_review')->first();
        
        if (!$legalTask || $legalTask->status !== 'pending') {
            return redirect()->back()->with('error', 'Legal review task not found or already completed.');
        }
        
        $this->workflowService->updateTaskStatus($legalTask, 'flagged', request('legal_comment'));
        
        // Also deny the main reservation if flagged by legal
        $reservation->update([
            'status' => 'denied',
            'approved_by' => Auth::id(),
            'remarks' => 'Flagged by legal review: ' . request('legal_comment'),
            'current_workflow_status' => 'denied_by_legal'
        ]);
        
        // Notify reserver
        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'legal_flag_facility_reservation',
            'description' => 'Legal flagged facility reservation ID ' . $reservation->id . ' (Task ID: ' . $legalTask->id . ')',
            'ip_address' => request()->ip()
        ]);
        
        return redirect()->route('facility_reservations.show', $id)->with('success', 'Reservation flagged by legal review.');
    }

    public function extractVisitorData($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        
        $documentTask = $reservation->tasks()->where('task_type', 'document_classification')->first();
        if (!$documentTask || $documentTask->status !== 'completed') {
            return redirect()->back()->with('error', 'Document classification must be complete before extracting visitor data.');
        }
        
        $aiResult = $documentTask->details['ai_classification'] ?? [];
        if (empty($aiResult)) {
            return redirect()->back()->with('error', 'AI classification data not found for visitor extraction.');
        }

        $visitorTask = $reservation->tasks()->where('task_type', 'visitor_coordination')->first();
        
        if (!$visitorTask) {
            // If task doesn't exist, create it via workflow service, which will then trigger extraction
            $visitorTask = $this->workflowService->createVisitorCoordinationTask($reservation);
            $this->workflowService->updateTaskStatus($visitorTask, 'pending', 'Visitor coordination task created by user action.');
        } else if ($visitorTask->status === 'pending') {
            // If task exists and is pending, it means it's awaiting extraction/approval
            // Mark it in progress to trigger extraction via workflow service
            $this->workflowService->updateTaskStatus($visitorTask, 'in_progress', 'User initiated visitor data extraction.');
        } else {
            return redirect()->back()->with('error', 'Visitor coordination task is not in a state to extract data.');
        }
        
        return redirect()->route('facility_reservations.show', $id)->with('success', 'Visitor data extraction initiated. Please allow a moment for processing.');
    }

    public function approveVisitors($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        
        $visitorTask = $reservation->tasks()->where('task_type', 'visitor_coordination')->first();
        if (!$visitorTask || $visitorTask->status !== 'pending' && $visitorTask->status !== 'in_progress') {
            return redirect()->back()->with('error', 'Visitor coordination task not found or not in an approvable state.');
        }

        // Instead of checking reservation->visitor_data, we check if actual Visitor models exist.
        if ($reservation->visitors->isEmpty()) {
            return redirect()->back()->with('error', 'No visitors records found for this reservation to approve.');
        }
        
        // Update the visitor task status to completed
        $this->workflowService->updateTaskStatus($visitorTask, 'completed', 'Visitors approved by user.');
        
        // The workflow service (or a job it dispatches) will now handle the actual approval and pass generation.

        return redirect()->route('facility_reservations.show', $id)->with('success', 'Visitors approved! Digital passes are being generated and security team will be notified.');
    }

    public function calendar($facilityId = null, Request $request)
    {
        $facilities = Facility::where('status', 'available')->get();
        $selectedFacility = $facilityId ? Facility::findOrFail($facilityId) : $facilities->first();
        
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $calendar = [];
        if ($selectedFacility) {
            $calendar = $this->calendarService->getFacilityCalendar($selectedFacility->id, $startDate, $endDate);
        }
        
        return view('facility_reservations.calendar', compact('facilities', 'selectedFacility', 'calendar', 'startDate', 'endDate'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        $availability = $this->calendarService->checkAvailability(
            $request->facility_id,
            $request->start_time,
            $request->end_time,
            $request->exclude_reservation_id
        );

        return response()->json($availability);
    }

    private function generateDigitalPasses($reservation)
    {
        // This method is no longer used as GenerateDigitalPasses is dispatched from VisitorService
        // Log::info('Digital passes generated for reservation', [
        //     'reservation_id' => $reservation->id,
        //     'visitors' => $reservation->visitor_data,
        //     'facility' => $reservation->facility->name,
        //     'date_range' => $reservation->start_time . ' to ' . $reservation->end_time
        // ]);
    }

    public function destroy($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        
        // The boot method in FacilityReservation model now handles deleting associated documents and tasks.
        // if ($reservation->document_path) {
        //     Storage::disk('public')->delete($reservation->document_path);
        // }
        
        $reservation->delete();
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation deleted.');
    }
}
