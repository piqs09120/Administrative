<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Mail\RequestSubmittedMail;
use App\Mail\RequestApprovedMail;
use App\Mail\RequestCompletedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Services\GeminiService;
use App\Services\DocumentTextExtractorService;
use App\Services\FacilityCalendarService;
use App\Services\SecureDocumentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Notifications\FacilityReservationStatusNotification;
use App\Models\AccessLog;
use App\Jobs\ProcessReservationDocument;
use App\Jobs\CheckAndAutoApproveReservation;
use App\Jobs\GenerateDigitalPasses;
use App\Services\VisitorService;
use App\Services\ReservationWorkflowService;
use App\Exports\MonthlyFacilityReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\EscalateFacilityDamageToLegal;

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

    public function newRequest(Request $request)
    {
        $facilities = Facility::all();
        $requests = \App\Models\FacilityRequest::with(['facility', 'assignedTo'])->latest()->get();
        
        // Get active tab from request parameter
        $validTabs = ['reservation', 'maintenance', 'equipment_request'];
        $tabParam = $request->get('tab');
        $activeTab = in_array($tabParam, $validTabs) ? $tabParam : 'reservation';
        
        return view('facility_reservations.new_request', compact('facilities', 'requests', 'activeTab'));
    }

    // Monitoring Summary API for Facilities module
    public function monitoringSummary(Request $request)
    {
        $now = now();
        
        // Get ALL facility requests of type 'reservation' - this is what the system actually uses
        $allRequests = \App\Models\FacilityRequest::with(['facility', 'assignedTo'])
            ->where('request_type', 'reservation')
            ->orderByRaw("CASE WHEN status = 'approved' AND requested_datetime <= ? AND (requested_end_datetime IS NULL OR requested_end_datetime >= ?) THEN 0 WHEN status = 'approved' AND (requested_end_datetime IS NULL OR requested_end_datetime >= ?) THEN 1 WHEN status = 'pending' THEN 2 ELSE 3 END", [$now, $now, $now])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        // Map to card data format
        $facilities = $allRequests->map(function($request) use ($now) {
            // Determine status based on time and database status
            // Priority: Time-based status > Database status
            
            // Check time conditions
            $hasStarted = $request->requested_datetime && $request->requested_datetime <= $now;
            $hasEnded = $request->requested_end_datetime && $request->requested_end_datetime < $now;
            $isUpcoming = $request->requested_datetime && $request->requested_datetime > $now;
            $isCurrentlyActive = $hasStarted && !$hasEnded;
            
            // Determine status - prioritize time-based status over database status
            // If approved (regardless of database status), check time window
            if ($request->status === 'approved') {
                if ($isCurrentlyActive || $isUpcoming) {
                    // Approved and either currently active or upcoming -> Active
                    $status = 'active';
                    $statusLabel = 'Active';
                } elseif ($hasEnded) {
                    // Approved but time has passed -> Completed
                    $status = 'completed';
                    $statusLabel = 'Completed';
                } else {
                    // Approved but no time info -> Active (default for approved)
                    $status = 'active';
                    $statusLabel = 'Active';
                }
            } elseif ($request->status === 'pending') {
                $status = 'pending';
                $statusLabel = 'Pending';
            } elseif ($request->status === 'completed') {
                // Only show as completed if time has actually ended
                // If time hasn't ended yet, show as active (might be incorrectly marked as completed)
                if ($hasEnded || (!$request->requested_end_datetime && $hasStarted && $now->diffInHours($request->requested_datetime) > 24)) {
                    $status = 'completed';
                    $statusLabel = 'Completed';
                } else {
                    // Completed in DB but time hasn't passed -> show as Active
                    $status = 'active';
                    $statusLabel = 'Active';
                }
            } else {
                // Fallback to database status
                $status = strtolower($request->status);
                $statusLabel = ucfirst($request->status);
            }
            
            $requestNotes = is_array($request->notes) ? $request->notes : (json_decode($request->notes, true) ?: []);
            $legalCaseStatus = data_get($requestNotes, 'legal.case_status');
            if ($legalCaseStatus) {
                if ($legalCaseStatus === 'completed') {
                    $status = 'completed';
                    $statusLabel = 'Completed';
                } elseif ($legalCaseStatus !== 'completed') {
                    $status = 'pending';
                    $statusLabel = 'Pending Legal';
                }
            }
            
            return [
                'id' => $request->id,
                'facility_id' => $request->facility_id,
                'facility_name' => $request->facility->name ?? 'Unknown Facility',
                'facility_location' => $request->facility->location ?? $request->location ?? 'N/A',
                'reserver_name' => $request->contact_name ?? 'Unknown',
                'reserver_email' => $request->contact_email ?? 'N/A',
                'reserver_department' => $request->department ?? 'N/A',
                'purpose' => $request->description ?? 'N/A',
                'start_time' => $request->requested_datetime ? $request->requested_datetime->format('M d, Y h:i A') : 'N/A',
                'end_time' => $request->requested_end_datetime ? $request->requested_end_datetime->format('M d, Y h:i A') : 'N/A',
                'start_time_raw' => $request->requested_datetime ? $request->requested_datetime->toIso8601String() : null,
                'end_time_raw' => $request->requested_end_datetime ? $request->requested_end_datetime->toIso8601String() : null,
                'status' => $status,
                'status_label' => $statusLabel,
                'legal_case_status' => $legalCaseStatus,
                'legal_case_number' => data_get($requestNotes, 'legal.case_number'),
                'created_at' => $request->created_at->diffForHumans(),
            ];
        });

        // Get stats
        $totalActive = \App\Models\FacilityRequest::where('request_type', 'reservation')
            ->where('status', 'approved')
            ->where('requested_datetime', '<=', $now)
            ->where(function($q) use ($now) {
                $q->whereNull('requested_end_datetime')
                  ->orWhere('requested_end_datetime', '>=', $now);
            })
            ->count();
        $totalFacilities = \App\Models\Facility::count();
        $availableFacilities = \App\Models\Facility::where('status', 'available')->count();

        return response()->json([
            'success' => true,
            'data' => $facilities->values(),
            'summary' => [
                'total_active' => $totalActive,
                'total_facilities' => $totalFacilities,
                'available_facilities' => $availableFacilities,
            ],
        ]);
    }

    // Equipment details list for Equipment Requests
    public function equipmentDetails(Request $request)
    {
        $items = \App\Models\FacilityRequest::where('request_type', 'equipment_request')
            ->latest()
            ->get()
            ->map(function($r){
                $notes = is_array($r->notes) ? $r->notes : (json_decode($r->notes, true) ?: []);
                return [
                    'id' => $r->id,
                    'code' => 'REQ-' . str_pad($r->id, 6, '0', STR_PAD_LEFT),
                    'department' => $r->department,
                    'priority' => $r->priority,
                    'status' => $r->status,
                    'equipment_item' => $notes['equipment_item'] ?? null,
                    'equipment_quantity' => $notes['equipment_quantity'] ?? 1,
                    'requested_datetime' => optional($r->requested_datetime)->toDateTimeString(),
                    'location' => $r->location,
                    'contact_name' => $r->contact_name,
                    'contact_email' => $r->contact_email,
                ];
            });

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'request_type' => 'required|string|in:maintenance,reservation,equipment_request',
            'department' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'location' => 'required|string',
            'facility_id' => 'nullable|exists:facilities,id',
            'requested_datetime' => 'required|date',
            'requested_end_datetime' => 'nullable|date|after:requested_datetime',
            'description' => 'required|string',
            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            // Equipment request optional fields (handled below)
            'equipment_item' => 'nullable|string',
            'equipment_quantity' => 'nullable|integer|min:1'
        ]);

        // If reservation type, facility_id is required
        if ($validated['request_type'] === 'reservation' && empty($validated['facility_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a facility for reservation requests.'
            ], 422);
        }

        // If equipment request, store equipment details in notes (JSON) and clear facility_id
        if ($validated['request_type'] === 'equipment_request') {
            $equipmentItem = $request->input('equipment_item');
            $equipmentQty = (int)($request->input('equipment_quantity') ?? 1);
            $validated['notes'] = json_encode([
                'equipment_item' => $equipmentItem,
                'equipment_quantity' => max(1, $equipmentQty)
            ]);
            $validated['facility_id'] = null;
        }

        $request_data = \App\Models\FacilityRequest::create($validated);

        // Send email notification
        try {
            Mail::to($request_data->contact_email)->send(new RequestSubmittedMail($request_data));
        } catch (\Exception $e) {
            Log::error('Failed to send request submitted email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Request submitted successfully!',
            'data' => $request_data,
            'view_url' => route('facility_reservations.new_request', ['tab' => $validated['request_type']])
        ]);
    }

    public function approveRequest($id)
    {
        $request = \App\Models\FacilityRequest::findOrFail($id);
        
        if ($request->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This request has already been processed.'
            ], 400);
        }
        
        $request->update(['status' => 'approved']);
        
        // Update facility status to occupied if it's a reservation
        if ($request->request_type === 'reservation' && $request->facility_id) {
            $facility = Facility::find($request->facility_id);
            if ($facility) {
                $facility->update(['status' => 'occupied']);
                Log::info("Facility {$facility->name} (ID: {$facility->id}) status updated to occupied for approved reservation");
            }
        }
        
        // Send email notification
        try {
            Mail::to($request->contact_email)->send(new RequestApprovedMail($request));
        } catch (\Exception $e) {
            Log::error('Failed to send request approved email: ' . $e->getMessage());
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Request approved successfully!'
        ]);
    }

    public function completeRequest($id, Request $requestData)
    {
        $request = \App\Models\FacilityRequest::findOrFail($id);
        
        if ($request->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved requests can be marked as completed.'
            ], 400);
        }
        
        // Get inspection data from request (Laravel's input() handles both JSON and form data)
        $damageFlag = $requestData->input('damage_flag', 0);
        $damageCost = $requestData->input('damage_cost');
        $damageSeverity = $requestData->input('damage_severity');
        $damageDescription = $requestData->input('damage_description');
        $inspectionNotes = $requestData->input('inspection_notes');
        
        // Convert damage_flag to boolean (handle string "1"/"0" or boolean true/false)
        // Handle different input formats: "1", "0", 1, 0, true, false, "true", "false"
        if (is_string($damageFlag)) {
            $damageFlag = in_array(strtolower($damageFlag), ['1', 'true', 'yes', 'on'], true);
        } else {
            $damageFlag = (bool) $damageFlag;
        }
        
        Log::info('Complete request - inspection data received', [
            'request_id' => $request->id,
            'damage_flag_raw' => $requestData->input('damage_flag'),
            'damage_flag_processed' => $damageFlag,
            'damage_cost' => $damageCost,
            'has_damage' => $damageFlag,
        ]);
        
        // Store inspection data in notes (as JSON)
        $notes = is_array($request->notes) ? $request->notes : (json_decode($request->notes, true) ?: []);
        $notes['inspection'] = [
            'damage_flag' => $damageFlag, // Already converted to boolean above
            'damage_cost' => $damageCost ? (float) $damageCost : null,
            'damage_severity' => $damageSeverity,
            'damage_description' => $damageDescription,
            'inspection_notes' => $inspectionNotes,
            'inspected_by' => auth()->id(),
            'inspected_at' => now()->toDateTimeString(),
        ];
        
        $request->update([
            'status' => 'completed',
            'notes' => $notes,
        ]);

        // Free up the facility if it was a reservation
        if ($request->request_type === 'reservation' && $request->facility_id) {
            $facility = Facility::find($request->facility_id);
            
            // If damage found, create legal case BEFORE freeing facility
            $legalCaseId = null;
            if ($damageFlag) {
                Log::info("Damage flag is true, proceeding to create legal case", [
                    'request_id' => $request->id,
                    'facility_id' => $request->facility_id,
                    'damage_cost' => $damageCost,
                    'damage_flag_value' => $damageFlag,
                ]);
                try {
                    // Create FacilityReservation record if it doesn't exist for legal escalation
                    // Try to find existing reservation by facility and time range
                    $reservation = FacilityReservation::with(['facility', 'reserver'])
                        ->where('facility_id', $request->facility_id)
                        ->whereBetween('start_time', [
                            $request->requested_datetime->copy()->subHours(1),
                            $request->requested_datetime->copy()->addHours(1)
                        ])
                        ->first();
                    
                    if (!$reservation) {
                        // Get the user who made the request (from FacilityRequest)
                        $reservedByUserId = $request->assigned_to;
                        if (!$reservedByUserId) {
                            // Try to find user by contact_email
                            $reservedByUser = \App\Models\User::where('email', $request->contact_email)->first();
                            $reservedByUserId = $reservedByUser ? $reservedByUser->id : auth()->id();
                        }
                        
                        // Create a reservation record for legal tracking
                        $reservation = FacilityReservation::create([
                            'facility_id' => $request->facility_id,
                            'facility_request_id' => $request->id,
                            'reserved_by' => $reservedByUserId,
                            'start_time' => $request->requested_datetime,
                            'end_time' => $request->requested_end_datetime ?? $request->requested_datetime->copy()->addHours(1),
                            'purpose' => $request->description,
                            'status' => 'approved',
                            'damage_flag' => true, // Explicitly set to true
                            'damage_cost' => $damageCost ? (float) $damageCost : 0,
                            'inspection_notes' => ($inspectionNotes ?? '') . ($damageDescription ? "\n\nDamage Description: " . $damageDescription : '') . ($damageSeverity ? "\n\nDamage Severity: " . ucfirst($damageSeverity) : ''),
                            'inspected_by' => auth()->id(),
                            'inspected_at' => now(),
                            'returned_at' => now(),
                            'requester_name' => $request->contact_name ?? 'Unknown',
                            'requester_contact' => $request->contact_email ?? 'N/A',
                        ]);
                        
                        // Load relationships after creation
                        $reservation->load(['facility', 'reserver']);
                        
                        // Refresh to ensure damage_flag is loaded
                        $reservation->refresh();
                    } else {
                        // Update existing reservation with damage info
                        $reservation->update([
                            'facility_request_id' => $reservation->facility_request_id ?? $request->id,
                            'damage_flag' => true, // Explicitly set to true
                            'damage_cost' => $damageCost ? (float) $damageCost : ($reservation->damage_cost ?? 0),
                            'inspection_notes' => ($inspectionNotes ?? '') . ($damageDescription ? "\n\nDamage Description: " . $damageDescription : '') . ($damageSeverity ? "\n\nDamage Severity: " . ucfirst($damageSeverity) : ''),
                            'inspected_by' => auth()->id(),
                            'inspected_at' => now(),
                            'returned_at' => now(),
                        ]);
                        
                        // Ensure relationships are loaded
                        if (!$reservation->relationLoaded('facility')) {
                            $reservation->load(['facility', 'reserver']);
                        }
                        
                        // Refresh to ensure damage_flag is updated
                        $reservation->refresh();
                    }
                    
                    // Verify damage_flag is set correctly
                    Log::info("Reservation damage flag status", [
                        'reservation_id' => $reservation->id,
                        'damage_flag' => $reservation->damage_flag,
                        'damage_flag_type' => gettype($reservation->damage_flag),
                    ]);
                    
                    // Always escalate to legal if damage is found (regardless of cost)
                    // Use the variable directly instead of checking the model property
                    if ($damageFlag || $reservation->damage_flag) {
                        Log::info("Reservation has damage flag, creating legal case", [
                            'reservation_id' => $reservation->id,
                            'damage_flag' => $reservation->damage_flag,
                            'damage_cost' => $reservation->damage_cost,
                        ]);
                        
                        // Create legal case synchronously so it's available immediately for redirect
                        try {
                            $legalCaseService = app(\App\Services\LegalCaseService::class);
                            $case = $legalCaseService->createFromReservationDamage($reservation);
                            $legalCaseId = $case->id;
                            
                            // Verify case was created
                            $verifyCase = \App\Models\LegalCase::find($case->id);
                            if (!$verifyCase) {
                                throw new \Exception("Legal case was not found after creation");
                            }
                            
                            Log::info("Legal case created and verified", [
                                'legal_case_id' => $case->id,
                                'case_number' => $case->case_number,
                                'case_type' => $case->case_type,
                                'case_title' => $case->case_title,
                            ]);
                            
                            // Update reservation remarks and legal_case_id
                            $reservation->remarks = trim(($reservation->remarks ? $reservation->remarks . ' ' : '') . 'Escalated to Legal.');
                            $reservation->legal_case_id = $case->id;
                            $reservation->save();
                            
                            // Update facility request notes with legal case info
                            $requestNotes = is_array($request->notes) ? $request->notes : (json_decode($request->notes, true) ?: []);
                            $requestNotes['legal'] = [
                                'case_id' => $case->id,
                                'case_number' => $case->case_number,
                                'case_status' => 'pending',
                                'case_priority' => $case->priority,
                                'created_at' => now()->toDateTimeString(),
                                'updated_at' => now()->toDateTimeString(),
                            ];
                            $request->update(['notes' => $requestNotes]);
                            
                            Log::info("Facility damage escalated to Legal for reservation {$reservation->id}, case #{$case->case_number}", [
                                'legal_case_id' => $case->id,
                                'case_type' => $case->case_type,
                                'damage_cost' => $reservation->damage_cost,
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Failed to create legal case synchronously: " . $e->getMessage(), [
                                'reservation_id' => $reservation->id,
                                'error' => $e->getMessage(),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                            // Fallback to async dispatch if sync creation fails
                            EscalateFacilityDamageToLegal::dispatch($reservation->id);
                        }
                    } else {
                        Log::warning("Reservation does not have damage flag set", [
                            'reservation_id' => $reservation->id,
                            'damage_flag' => $reservation->damage_flag,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to escalate facility damage to legal: " . $e->getMessage(), [
                        'request_id' => $request->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
            
            // Free up the facility (regardless of damage status)
            if ($facility) {
                $facility->update(['status' => 'available']);
                Log::info("Facility {$facility->name} (ID: {$facility->id}) status updated to available after request completion");
            }
        }

        // Send completion email
        try {
            Mail::to($request->contact_email)->send(new RequestCompletedMail($request));
            Log::info("Request completion email sent to {$request->contact_email} for request {$request->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send request completion email: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'has_damage' => (bool) $damageFlag,
            'legal_case_id' => $legalCaseId ?? null,
            'message' => $damageFlag
                ? 'Inspection submitted. Damage case has been created and escalated to Legal Management.'
                : 'Facility checked out successfully! The facility is now available.'
        ]);
    }

    public function showRequest($id)
    {
        $request = \App\Models\FacilityRequest::with('facility')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

    public function freeFacility($id)
    {
        $facility = Facility::findOrFail($id);
        
        if ($facility->status !== 'occupied') {
            return response()->json([
                'success' => false,
                'message' => 'This facility is not currently occupied.'
            ], 400);
        }
        
        $facility->update(['status' => 'available']);
        
        return response()->json([
            'success' => true,
            'message' => 'Facility has been freed and is now available.'
        ]);
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

    public function userHistory(Request $request)
    {
        $userId = auth()->id();
        
        // Get user's reservations with pagination
        $reservations = FacilityReservation::where('reserved_by', $userId)
            ->with(['facility', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get analytics data with safe fallbacks to avoid blank page on error
        try {
            $analytics = [
                'total_reservations' => FacilityReservation::where('reserved_by', $userId)->count(),
                'approved_reservations' => FacilityReservation::where('reserved_by', $userId)->where('facility_reservations.status', 'approved')->count(),
                'pending_reservations' => FacilityReservation::where('reserved_by', $userId)->where('facility_reservations.status', 'pending')->count(),
                'denied_reservations' => FacilityReservation::where('reserved_by', $userId)->where('facility_reservations.status', 'denied')->count(),
                'most_used_facility' => $this->getMostUsedFacility($userId),
                'upcoming_reservations' => FacilityReservation::where('reserved_by', $userId)
                    ->where('start_time', '>', now())
                    ->where('facility_reservations.status', 'approved')
                    ->count(),
                'monthly_stats' => $this->getMonthlyStats($userId),
                'peak_booking_times' => $this->getPeakBookingTimes($userId)
            ];
        } catch (\Throwable $e) {
            \Log::warning('User history analytics failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            $analytics = [
                'total_reservations' => 0,
                'approved_reservations' => 0,
                'pending_reservations' => 0,
                'denied_reservations' => 0,
                'most_used_facility' => null,
                'upcoming_reservations' => 0,
                'monthly_stats' => collect(),
                'peak_booking_times' => collect(),
            ];
        }
        
        return view('facility_reservations.user_history', compact('reservations', 'analytics'));
    }

    private function getMostUsedFacility($userId)
    {
        return FacilityReservation::where('reserved_by', $userId)
            ->where('facility_reservations.status', 'approved')
            ->join('facilities', 'facility_reservations.facility_id', '=', 'facilities.id')
            ->selectRaw('facilities.name, COUNT(*) as usage_count')
            ->groupBy('facilities.id', 'facilities.name')
            ->orderBy('usage_count', 'desc')
            ->first();
    }

    private function getMonthlyStats($userId)
    {
        return FacilityReservation::where('reserved_by', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getPeakBookingTimes($userId)
    {
        return FacilityReservation::where('reserved_by', $userId)
            ->where('status', 'approved')
            ->selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
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

    public function calendar(Request $request, $facilityId = null)
    {
        $facilities = Facility::where('status', 'available')->get();
        $selectedFacility = $facilityId ? Facility::findOrFail($facilityId) : ($facilities->first() ?: null);
        
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

    /**
     * Lightweight real-time stats endpoint for dashboard polling
     */
    public function realtimeStats()
    {
        $todayCount = FacilityReservation::whereDate('created_at', now()->toDateString())->count();
        $latest = FacilityReservation::with(['facility', 'reserver'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'facility' => $r->facility->name ?? 'Unknown',
                    'reserver' => $r->reserver->name ?? 'Unknown',
                    'status' => $r->status,
                    'purpose' => $r->purpose,
                    'start_time' => optional($r->start_time)->toDateTimeString(),
                    'end_time' => optional($r->end_time)->toDateTimeString(),
                    'created_at' => optional($r->created_at)->toDateTimeString(),
                ];
            });

        return response()->json([
            'success' => true,
            'today_reservations' => $todayCount,
            'latest' => $latest,
        ]);
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

    public function adminAnalytics(Request $request)
    {
        Log::info('Admin analytics method called - using ultra-simple approach');
        
        // Ultra-simple analytics that will definitely load fast
        $analytics = [
            'overview' => [
                'total_reservations' => FacilityReservation::count(),
                'approved_reservations' => FacilityReservation::where('status', 'approved')->count(),
                'pending_reservations' => FacilityReservation::where('status', 'pending')->count(),
                'denied_reservations' => FacilityReservation::where('status', 'denied')->count(),
                'total_facilities' => \App\Models\Facility::count(),
                'active_users' => FacilityReservation::distinct('reserved_by')->count('reserved_by'),
                'this_month_reservations' => FacilityReservation::whereMonth('created_at', now()->month)->count(),
                'approval_rate' => $this->calculateApprovalRate()
            ],
            'facility_usage' => collect(),
            'reservation_trends' => collect([
                ['month' => now()->subMonths(5)->format('Y-m'), 'count' => 0],
                ['month' => now()->subMonths(4)->format('Y-m'), 'count' => 0],
                ['month' => now()->subMonths(3)->format('Y-m'), 'count' => 0],
                ['month' => now()->subMonths(2)->format('Y-m'), 'count' => 0],
                ['month' => now()->subMonth()->format('Y-m'), 'count' => 0],
                ['month' => now()->format('Y-m'), 'count' => FacilityReservation::whereMonth('created_at', now()->month)->count()]
            ]),
            'user_activity' => collect(),
            'conflict_analysis' => [
                'potential_conflicts' => 0,
                'resolved_conflicts' => 0,
                'conflict_rate' => 0
            ],
            'revenue_analytics' => [
                'total_revenue' => 0,
                'monthly_revenue' => 0,
                'average_booking_value' => 0
            ],
            'peak_hours' => collect([
                ['hour' => 9, 'count' => 0],
                ['hour' => 10, 'count' => 0],
                ['hour' => 11, 'count' => 0],
                ['hour' => 12, 'count' => 0],
                ['hour' => 13, 'count' => 0],
                ['hour' => 14, 'count' => 0],
                ['hour' => 15, 'count' => 0],
                ['hour' => 16, 'count' => 0],
                ['hour' => 17, 'count' => 0]
            ]),
            'monthly_comparison' => [
                'current_month' => FacilityReservation::whereMonth('created_at', now()->month)->count(),
                'last_month' => FacilityReservation::whereMonth('created_at', now()->subMonth()->month)->count(),
                'growth_rate' => 0
            ]
        ];

        // Get recent reservations for admin review (ultra-simple)
        $recentReservations = FacilityReservation::with(['facility:id,name', 'reserver:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending reservations requiring attention (ultra-simple)
        $pendingReservations = FacilityReservation::with(['facility:id,name', 'reserver:id,name'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        Log::info('Returning ultra-simple admin analytics view', [
            'overview' => $analytics['overview'],
            'recent_reservations_count' => $recentReservations->count(),
            'pending_reservations_count' => $pendingReservations->count()
        ]);
        
        return view('facility_reservations.admin_analytics', compact('analytics', 'recentReservations', 'pendingReservations'));
    }


    public function getOverviewStats()
    {
        return [
            'total_reservations' => FacilityReservation::count(),
            'approved_reservations' => FacilityReservation::where('status', 'approved')->count(),
            'pending_reservations' => FacilityReservation::where('status', 'pending')->count(),
            'denied_reservations' => FacilityReservation::where('status', 'denied')->count(),
            'total_facilities' => \App\Models\Facility::count(),
            'active_users' => FacilityReservation::distinct('reserved_by')->count('reserved_by'),
            'this_month_reservations' => FacilityReservation::whereMonth('created_at', now()->month)->count(),
            'approval_rate' => $this->calculateApprovalRate()
        ];
    }

    private function getFacilityUsageStats()
    {
        return \App\Models\Facility::withCount(['reservations' => function($query) {
            $query->where('status', 'approved');
        }])
        ->orderBy('reservations_count', 'desc')
        ->limit(5)
        ->get();
    }

    private function getReservationTrends()
    {
        return FacilityReservation::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6)) // Reduced from 12 to 6 months
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12) // Add limit for safety
            ->get();
    }

    private function getUserActivityStats()
    {
        return FacilityReservation::selectRaw('reserved_by, COUNT(*) as reservation_count')
            ->where('created_at', '>=', now()->subMonths(3)) // Reduced to 3 months
            ->with('reserver:id,name')
            ->groupBy('reserved_by')
            ->orderBy('reservation_count', 'desc')
            ->limit(3) // Reduced to 3
            ->get();
    }

    private function getConflictAnalysis()
    {
        // This would analyze potential conflicts and overlapping reservations
        return [
            'potential_conflicts' => 0, // Implement conflict detection logic
            'resolved_conflicts' => 0,
            'conflict_rate' => 0
        ];
    }

    private function getRevenueAnalytics()
    {
        // Calculate revenue from approved reservations (last 12 months)
        // Guard against pathological scans by constraining the time window
        $totalRevenue = FacilityReservation::where('facility_reservations.status', 'approved')
            ->whereNotNull('facility_reservations.start_time')
            ->whereNotNull('facility_reservations.end_time')
            ->where('facility_reservations.start_time', '>=', now()->subYear())
            ->join('facilities', 'facility_reservations.facility_id', '=', 'facilities.id')
            ->selectRaw('SUM(facilities.hourly_rate * GREATEST(0, TIMESTAMPDIFF(HOUR, facility_reservations.start_time, facility_reservations.end_time))) as total_revenue')
            ->value('total_revenue') ?? 0;

        return [
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'average_booking_value' => $this->getAverageBookingValue()
        ];
    }

    private function getPeakHoursAnalysis()
    {
        return FacilityReservation::where('status', 'approved')
            ->where('created_at', '>=', now()->subMonths(3)) // Add time constraint
            ->selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(10) // Add limit
            ->get();
    }

    private function getMonthlyComparison()
    {
        $currentMonth = FacilityReservation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $lastMonth = FacilityReservation::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        return [
            'current_month' => $currentMonth,
            'last_month' => $lastMonth,
            'growth_rate' => $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0
        ];
    }

    private function calculateApprovalRate()
    {
        $total = FacilityReservation::whereIn('status', ['approved', 'denied'])->count();
        $approved = FacilityReservation::where('status', 'approved')->count();
        
        return $total > 0 ? ($approved / $total) * 100 : 0;
    }

    private function getMonthlyRevenue()
    {
        return FacilityReservation::where('facility_reservations.status', 'approved')
            ->whereNotNull('facility_reservations.start_time')
            ->whereNotNull('facility_reservations.end_time')
            ->whereMonth('facility_reservations.start_time', now()->month)
            ->whereYear('facility_reservations.created_at', now()->year)
            ->join('facilities', 'facility_reservations.facility_id', '=', 'facilities.id')
            ->selectRaw('SUM(facilities.hourly_rate * GREATEST(0, TIMESTAMPDIFF(HOUR, facility_reservations.start_time, facility_reservations.end_time))) as monthly_revenue')
            ->value('monthly_revenue') ?? 0;
    }

    private function getAverageBookingValue()
    {
        $totalRevenue = $this->getRevenueAnalytics()['total_revenue'];
        $totalBookings = FacilityReservation::where('status', 'approved')->count();
        
        return $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;
    }

    /**
     * Generate monthly facility usage report
     */
    public function generateMonthlyReport(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'facility_id' => 'nullable|exists:facilities,id',
            'format' => 'required|in:excel,pdf,json'
        ]);

        $month = $request->input('month');
        $year = $request->input('year');
        $facilityId = $request->input('facility_id');
        $format = $request->input('format');

        // Get report data
        $reportData = $this->getMonthlyReportData($month, $year, $facilityId);

        if ($format === 'excel') {
            return $this->exportMonthlyReportExcel($month, $year, $facilityId);
        } elseif ($format === 'pdf') {
            return $this->exportMonthlyReportPdf($reportData, $month, $year);
        } else {
            return response()->json([
                'success' => true,
                'data' => $reportData,
                'generated_at' => now()->toISOString()
            ]);
        }
    }

    /**
     * Export monthly report as Excel
     */
    public function exportMonthlyReportExcel($month, $year, $facilityId = null)
    {
        $monthName = \Carbon\Carbon::createFromDate($year, $month)->format('F');
        $filename = "facility_usage_report_{$monthName}_{$year}" . ($facilityId ? "_facility_{$facilityId}" : '') . ".xlsx";
        
        return Excel::download(new MonthlyFacilityReportExport($month, $year, $facilityId), $filename);
    }

    /**
     * Export monthly report as PDF
     */
    public function exportMonthlyReportPdf($reportData, $month, $year)
    {
        $monthName = \Carbon\Carbon::createFromDate($year, $month)->format('F');
        $pdf = Pdf::loadView('facility_reservations.monthly_report_pdf', [
            'reportData' => $reportData,
            'month' => $month,
            'year' => $year,
            'monthName' => $monthName
        ]);
        
        $filename = "facility_usage_report_{$monthName}_{$year}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Get monthly report data
     */
    public function getMonthlyReportData($month, $year, $facilityId = null)
    {
        $query = FacilityReservation::with(['facility', 'reserver', 'approver'])
            ->whereMonth('start_time', $month)
            ->whereYear('start_time', $year);

        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        $reservations = $query->orderBy('start_time')->get();

        // Calculate summary statistics
        $totalReservations = $reservations->count();
        $approvedReservations = $reservations->where('status', 'approved')->count();
        $pendingReservations = $reservations->where('status', 'pending')->count();
        $deniedReservations = $reservations->where('status', 'denied')->count();
        
        $totalHours = $reservations->where('status', 'approved')->sum(function ($reservation) {
            return $reservation->start_time && $reservation->end_time ? 
                $reservation->start_time->diffInHours($reservation->end_time) : 0;
        });

        $totalRevenue = $reservations->where('status', 'approved')->sum('payment_amount') ?? 0;

        // Facility usage breakdown
        $facilityUsage = $reservations->where('status', 'approved')
            ->groupBy('facility_id')
            ->map(function ($facilityReservations, $facilityId) {
                $facility = $facilityReservations->first()->facility;
                return [
                    'facility_id' => $facilityId,
                    'facility_name' => $facility->name ?? 'Unknown',
                    'reservation_count' => $facilityReservations->count(),
                    'total_hours' => $facilityReservations->sum(function ($reservation) {
                        return $reservation->start_time && $reservation->end_time ? 
                            $reservation->start_time->diffInHours($reservation->end_time) : 0;
                    }),
                    'revenue' => $facilityReservations->sum('payment_amount') ?? 0
                ];
            });

        // Daily usage pattern
        $dailyUsage = $reservations->where('status', 'approved')
            ->groupBy(function ($reservation) {
                return $reservation->start_time->format('Y-m-d');
            })
            ->map(function ($dayReservations, $date) {
                return [
                    'date' => $date,
                    'reservation_count' => $dayReservations->count(),
                    'total_hours' => $dayReservations->sum(function ($reservation) {
                        return $reservation->start_time && $reservation->end_time ? 
                            $reservation->start_time->diffInHours($reservation->end_time) : 0;
                    })
                ];
            });

        return [
            'summary' => [
                'total_reservations' => $totalReservations,
                'approved_reservations' => $approvedReservations,
                'pending_reservations' => $pendingReservations,
                'denied_reservations' => $deniedReservations,
                'approval_rate' => $totalReservations > 0 ? ($approvedReservations / $totalReservations) * 100 : 0,
                'total_hours' => $totalHours,
                'total_revenue' => $totalRevenue,
                'average_booking_duration' => $approvedReservations > 0 ? $totalHours / $approvedReservations : 0
            ],
            'facility_usage' => $facilityUsage->values(),
            'daily_usage' => $dailyUsage->values(),
            'reservations' => $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'facility_name' => $reservation->facility->name ?? 'N/A',
                    'reserved_by' => $reservation->reserver->name ?? 'N/A',
                    'start_time' => $reservation->start_time ? $reservation->start_time->format('Y-m-d H:i') : 'N/A',
                    'end_time' => $reservation->end_time ? $reservation->end_time->format('Y-m-d H:i') : 'N/A',
                    'duration_hours' => $reservation->start_time && $reservation->end_time ? 
                        round($reservation->start_time->diffInHours($reservation->end_time), 2) : 0,
                    'purpose' => $reservation->purpose ?? 'Not specified',
                    'status' => $reservation->status,
                    'payment_amount' => $reservation->payment_amount ?? 0
                ];
            })
        ];
    }

    /**
     * Get monthly reports dashboard
     */
    public function monthlyReports()
    {
        $facilities = Facility::where('status', 'available')->get();
        
        // Get available months/years with data
        $availablePeriods = FacilityReservation::selectRaw('YEAR(start_time) as year, MONTH(start_time) as month')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($period) {
                return [
                    'year' => $period->year,
                    'month' => $period->month,
                    'month_name' => \Carbon\Carbon::createFromDate($period->year, $period->month)->format('F Y')
                ];
            });

        return view('facility_reservations.monthly_reports', compact('facilities', 'availablePeriods'));
    }

    /**
     * Get monthly report summary for dashboard
     */
    public function getMonthlyReportSummary(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'facility_id' => 'nullable|exists:facilities,id'
        ]);

        $reportData = $this->getMonthlyReportData($request->month, $request->year, $request->facility_id);
        
        return response()->json([
            'success' => true,
            'data' => $reportData
        ]);
    }

    /**
     * Export facilities monitoring data as PDF
     */
    public function exportMonitoringPdf(Request $request)
    {
        $now = now();
        $oneWeekAgo = $now->copy()->subDays(6)->startOfDay();

        $query = \App\Models\FacilityRequest::query();

        $total = (clone $query)->count();
        $inProgress = (clone $query)->where('status', 'pending')->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $urgent = (clone $query)->where('priority', 'urgent')->count();

        // Weekly counts
        $weekly = (clone $query)
            ->whereBetween('created_at', [$oneWeekAgo, $now])
            ->get()
            ->groupBy(function ($r) { return $r->created_at->format('D'); })
            ->map->count();

        $days = collect(range(0,6))->map(function($i) use ($oneWeekAgo){ return $oneWeekAgo->copy()->addDays($i)->format('D'); });
        $weeklySeries = $days->map(function($d) use ($weekly){ return [ 'day' => $d, 'count' => (int)($weekly[$d] ?? 0) ]; });

        $recent = (clone $query)
            ->with('facility:id,name')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function($r){
                return [
                    'id' => $r->id,
                    'code' => 'REQ-' . str_pad($r->id, 3, '0', STR_PAD_LEFT),
                    'type' => $r->request_type,
                    'priority' => $r->priority,
                    'status' => $r->status,
                    'title' => ucfirst($r->request_type) . ' - ' . (\Illuminate\Support\Str::limit($r->description, 40) ?: 'Request'),
                    'department' => $r->department,
                    'facility' => $r->facility->name ?? null,
                    'created_at' => $r->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get facility statistics
        $facilities = \App\Models\Facility::all();
        $totalFacilities = $facilities->count();
        $availableFacilities = $facilities->where('status', 'available')->count();
        $occupiedFacilities = $facilities->where('status', 'occupied')->count();

        $reportData = [
            'summary' => [
                'total_requests' => $total,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'urgent' => $urgent,
                'total_facilities' => $totalFacilities,
                'available_facilities' => $availableFacilities,
                'occupied_facilities' => $occupiedFacilities,
            ],
            'weekly_data' => $weeklySeries,
            'recent_requests' => $recent,
            'facilities' => $facilities,
            'generated_at' => now(),
            'date_range' => [
                'start' => $oneWeekAgo,
                'end' => $now
            ]
        ];

        $filename = 'facilities_monitoring_report_' . now()->format('Y-m-d_H-i-s');
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('facilities.reports.monitoring_pdf', $reportData);
        return $pdf->download($filename . '.pdf');
    }

    // 1) Show inspection form
    public function returnReview($id)
    {
        $reservation = FacilityReservation::with(['facility','reserver'])->findOrFail($id);
        // (Optional) authorize here if you added policies
        // $this->authorize('update', $reservation);
        return view('facility_reservations.return_review', compact('reservation'));
    }

    // 2) Handle inspection submission
    public function submitReturnInspection(Request $request, $id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        // $this->authorize('update', $reservation); // optional policy
        $data = $request->validate([
            'inspection_notes' => 'nullable|string',
            'damage_flag'      => 'nullable|boolean',
            'damage_cost'      => 'nullable|numeric|min:0',
            'damage_photos.*'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);
        
        // Ensure damage_flag is boolean (checkbox returns "1" or "0" as string)
        $data['damage_flag'] = isset($data['damage_flag']) && $data['damage_flag'] !== '0' && $data['damage_flag'] !== false;

        $photoPaths = [];
        if ($request->hasFile('damage_photos')) {
            foreach ($request->file('damage_photos') as $photo) {
                // reuse your secure repository
                $stored = $this->secureRepository->storeDocument($photo, $reservation->id, [
                    'type' => 'damage_photo',
                ]);
                if ($stored['success']) {
                    $photoPaths[] = $stored['path'];
                }
            }
        }

        $reservation->update([
            'inspection_notes' => $data['inspection_notes'] ?? null,
            'damage_flag'      => (bool) ($data['damage_flag'] ?? false),
            'damage_cost'      => $data['damage_cost'] ?? null,
            'damage_photos'    => !empty($photoPaths) ? $photoPaths : $reservation->damage_photos,
            'inspected_by'     => auth()->id(),
            'inspected_at'     => now(),
            'checked_out_at'   => $reservation->checked_out_at ?? now(),
            'returned_at'      => now(),
        ]);

        $reservation->logWorkflowStep('return_inspected', 'Return inspection recorded', [
            'damage_flag' => $reservation->damage_flag,
            'damage_cost' => $reservation->damage_cost,
            'photo_count' => count($photoPaths),
        ]);

        // Auto-escalate
        if ($reservation->needsLegalEscalation()) {
            EscalateFacilityDamageToLegal::dispatch($reservation->id);
        }

        return redirect()
            ->route('facility_reservations.show', $reservation->id)
            ->with('success', $reservation->needsLegalEscalation()
                ? 'Inspection submitted and escalated to Legal.'
                : 'Inspection submitted.');
    }
}
