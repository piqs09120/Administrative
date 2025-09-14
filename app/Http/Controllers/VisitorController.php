<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Facility;
use App\Models\User;
use App\Models\VisitorCheckinLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VisitorExport;
use App\Exports\VisitorReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\VisitorService;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorCheckedOutMail;

class VisitorController extends Controller
{
    protected $visitorService;
    protected $workflowService;

    public function __construct(VisitorService $visitorService, \App\Services\ReservationWorkflowService $workflowService)
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role ?? null;
           
            return $next($request);
        })->only(['logs', 'reports', 'exportExcel', 'exportPdf']);

        $this->visitorService = $visitorService;
        $this->workflowService = $workflowService;
    }

    public function index(Request $request)
    {
        $visitors = Visitor::with('facility')->latest()->get();
        $facilities = Facility::all();
        $users = User::all();
        
        // Get active tab from request parameter
        $validTabs = ['current', 'scheduled', 'monitoring'];
        $tabParam = $request->get('tab');
        $activeTab = in_array($tabParam, $validTabs) ? $tabParam : 'current';
        
        return view('visitor.index', compact('visitors', 'facilities', 'users', 'activeTab'));
    }

    public function create()
    {
        // Show pending/newly registered visitors in a table for review
        $pendingVisitors = Visitor::where('status', 'registered')
            ->whereNull('time_in')
            ->latest()
            ->paginate(20);

        return view('visitor.create', compact('pendingVisitors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact' => 'required|string|max:255',
            'purpose' => 'required|string|max:1000',
            'facility_id' => 'nullable',
            'host_employee' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'id_type' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'time_in' => 'nullable|date',
        ]);

        // Generate pass ID
        $passId = $this->generatePassId();
        
        // Combine expected date and time into a proper datetime
        $expectedDateTimeOut = null;
        if ($request->expected_date_out && $request->expected_time_out) {
            $expectedDateTimeOut = \Carbon\Carbon::parse($request->expected_date_out . ' ' . $request->expected_time_out);
        } elseif ($request->expected_time_out) {
            // If only time is provided, assume today's date
            $expectedDateTimeOut = \Carbon\Carbon::parse($request->expected_time_out);
        }

        // Calculate pass validity based on expected time out
        $validity = $this->calculatePassValidity($request);
        
        $visitorData = [
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact,
            'purpose' => $request->purpose,
            'facility_id' => $request->facility_id,
            'host_employee' => $request->host_employee,
            'company' => $request->company,
            'department' => $request->department,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'vehicle_plate' => $request->vehicle_plate,
            'arrival_date' => $request->arrival_date,
            'arrival_time' => $request->arrival_time,
            'expected_date_out' => $request->expected_date_out,
            'expected_time_out' => $expectedDateTimeOut,
            'time_in' => null, // Not checked in yet - will be set when they actually check in
            'pass_id' => $passId,
            'pass_type' => 'visitor',
            'pass_validity' => '24_hours',
            'pass_valid_from' => $validity['valid_from'],
            'pass_valid_until' => $validity['valid_until'],
            'access_level' => null,
            'escort_required' => 'no',
            'status' => 'registered', // Changed from 'active' to 'registered'
        ];
        
        $visitor = Visitor::create($visitorData);
        
        // Generate digital pass data
        $this->generateDigitalPass($visitor);
        
        // Log the registration activity
        $this->logVisitorActivity($visitor, 'register', 'Visitor registered and pass generated');
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Visitor registered and pass issued! Please check them in when they arrive.',
                'visitor' => $visitor->load('facility')
            ]);
        }
        
        // After normal form submit, go to the "New Visitor" page (table view)
        return redirect()->route('visitor.create')->with('success', 'Visitor registered and added to the New Visitors queue.');
    }

    /**
     * Public store endpoint for landing page (no auth).
     */
    public function publicStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact' => 'required|string|max:255',
            'purpose' => 'required|string|max:1000',
            'facility_id' => 'nullable',
            'host_employee' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'id_type' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'expected_date_out' => 'nullable|date',
            'expected_time_out' => 'nullable|date_format:H:i',
            'arrival_date' => 'nullable|date',
            'arrival_time' => 'nullable|date_format:H:i',
        ]);

        // Generate pass ID
        $passId = $this->generatePassId();

        // Combine expected date and time into a proper datetime
        $expectedDateTimeOut = null;
        if ($request->expected_date_out && $request->expected_time_out) {
            $expectedDateTimeOut = \Carbon\Carbon::parse($request->expected_date_out . ' ' . $request->expected_time_out);
        } elseif ($request->expected_time_out) {
            // If only time is provided, assume today's date
            $expectedDateTimeOut = \Carbon\Carbon::parse($request->expected_time_out);
        }

        // Calculate pass validity based on expected time out
        $validity = $this->calculatePassValidity($request);

        $visitor = Visitor::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact' => $validated['contact'],
            'purpose' => $validated['purpose'],
            'facility_id' => $request->facility_id ?: null,
            'host_employee' => $request->host_employee,
            'department' => $request->department,
            'company' => $request->company,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'vehicle_plate' => $request->vehicle_plate,
            'arrival_date' => $request->arrival_date,
            'arrival_time' => $request->arrival_time,
            'expected_date_out' => $request->expected_date_out,
            'expected_time_out' => $expectedDateTimeOut,
            'time_in' => null,
            'pass_id' => $passId,
            'pass_type' => 'visitor',
            'pass_validity' => '24_hours',
            'pass_valid_from' => $validity['valid_from'],
            'pass_valid_until' => $validity['valid_until'],
            'access_level' => null,
            'escort_required' => 'no',
            'status' => 'registered',
        ]);

        $this->generateDigitalPass($visitor);

        return response()->json([
            'success' => true,
            'message' => 'Visitor registered successfully',
            'redirect' => route('visitor.create')
        ]);
    }

    public function show($id)
    {
        $visitor = Visitor::with('facility')->findOrFail($id);
        return view('visitor.show', compact('visitor'));
    }

    public function edit($id)
    {
        $visitor = Visitor::findOrFail($id);
        $facilities = Facility::all();
        $users = User::all();
        return view('visitor.edit', compact('visitor', 'facilities', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:facilities,id',
            'time_in' => 'required|date',
            'time_out' => 'nullable|date|after:time_in',
            'company' => 'nullable|string|max:255',
            'host_employee' => 'nullable|string|max:255',
        ]);

        $visitor = Visitor::findOrFail($id);
        $visitor->update($request->all());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Visitor updated successfully!',
                'visitor' => $visitor->load('facility')
            ]);
        }
        
        return redirect()->route('visitor.show', $id)->with('success', 'Visitor log updated!');
    }

    public function destroy($id)
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Visitor deleted successfully!'
            ]);
        }
        
        return redirect()->route('visitor.index')->with('success', 'Visitor log deleted!');
    }

    // AJAX Methods for Real-time Functionality
    public function searchVisitors(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        
        $visitors = Visitor::with('facility')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('company', 'like', "%{$query}%")
            ->orWhere('purpose', 'like', "%{$query}%")
            ->orWhere('contact', 'like', "%{$query}%")
            ->latest()
            ->get();
            
        return response()->json($visitors);
    }

    public function getVisitorDetails($id): JsonResponse
    {
        $visitor = Visitor::with(['facility', 'facilityReservation'])->findOrFail($id);
        
        $digitalPass = null;
        if ($visitor->facilityReservation && $visitor->facilityReservation->digital_pass_data) {
            foreach ($visitor->facilityReservation->digital_pass_data as $pass) {
                if (($pass['visitor_id'] ?? null) == $visitor->id) {
                    $digitalPass = $pass;
                    break;
                }
            }
        }
        
        $visitorArray = $visitor->toArray();
        $visitorArray['digital_pass'] = $digitalPass; // Add digital pass data to the response
        
        return response()->json($visitorArray);
    }

    // Renamed from checkIn to store, as it performs a store operation.
    // This function will also be used by other modules to "pre-register" visitors
    // from Facility Reservations or Document Management requests.
    public function checkIn(Request $request): JsonResponse // This method is now effectively a store method.
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:facilities,id',
            'company' => 'nullable|string|max:255',
            'host_employee' => 'nullable|string|max:255',
        ]);

        $visitor = Visitor::create([
            'name' => $request->name,
            'contact' => $request->contact,
            'purpose' => $request->purpose,
            'facility_id' => $request->facility_id,
            'company' => $request->company,
            'host_employee' => $request->host_employee,
            'time_in' => now(),
            'status' => 'active',
        ]);

        // Log the check-in activity
        $this->logVisitorActivity($visitor, 'checkin', 'Visitor checked in');

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked in successfully!',
            'visitor' => $visitor->load('facility')
        ]);
    }

    public function checkOut($id): JsonResponse
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->update([
            'time_out' => now(),
            'status' => 'checked_out'
        ]);

        // Log the check-out activity
        $this->logVisitorActivity($visitor, 'checkout', 'Visitor checked out');

        // Send a graceful checkout email if address is available
        try {
            if (!empty($visitor->email)) {
                Mail::to($visitor->email)->send(new VisitorCheckedOutMail($visitor));
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to send checkout email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked out successfully!',
            'visitor' => $visitor->load('facility')
        ]);
    }

    public function checkInExisting($id): JsonResponse
    {
        $visitor = Visitor::findOrFail($id);
        
        if ($visitor->time_in) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor is already checked in!'
            ]);
        }

        $visitor->update([
            'time_in' => now(),
            'status' => 'active'
        ]);

        // Log the check-in activity
        $this->logVisitorActivity($visitor, 'checkin', 'Visitor checked in');

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked in successfully!',
            'visitor' => $visitor->load('facility')
        ]);
    }

    public function getCurrentVisitors(): JsonResponse
    {
        try {
            $visitors = Visitor::with('facility')
                ->whereNotNull('time_in')  // Must be checked in
                ->whereNull('time_out')    // Must not be checked out
                ->latest()
                ->get();
                
            return response()->json($visitors);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading visitors',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getScheduledVisits(): JsonResponse
    {
        // Return registered visitors (not checked in yet) for today
        $visitors = Visitor::with('facility')
            ->whereNull('time_in')  // Not checked in yet
            ->whereDate('created_at', now()->toDateString()) // Registered today
            ->latest()
            ->get();
            
        return response()->json($visitors);
    }

    public function getVisitorStats(): JsonResponse
    {
        $totalVisitors = Visitor::count();
        $currentlyIn = Visitor::whereNotNull('time_in')->whereNull('time_out')->count();
        $todayVisitors = Visitor::whereDate('created_at', today())->count();
        $completedVisits = Visitor::whereNotNull('time_out')->count();

        return response()->json([
            'total' => $totalVisitors,
            'currentlyIn' => $currentlyIn,
            'todayVisitors' => $todayVisitors,
            'completed' => $completedVisits
        ]);
    }

    // Quick Actions
    public function viewAllVisitors(): JsonResponse
    {
        $visitors = Visitor::with('facility')->latest()->get();
        return response()->json($visitors);
    }

    public function scheduleVisit(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'scheduled_time' => 'required|date|after:now',
            'facility_id' => 'nullable|exists:facilities,id',
            'company' => 'nullable|string|max:255',
            'host_employee' => 'nullable|string|max:255',
        ]);

        $visitor = Visitor::create([
            'name' => $request->name,
            'contact' => $request->contact,
            'purpose' => $request->purpose,
            'facility_id' => $request->facility_id,
            'company' => $request->company,
            'host_employee' => $request->host_employee,
            'time_in' => $request->scheduled_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visit scheduled successfully!',
            'visitor' => $visitor->load('facility')
        ]);
    }

    public function emergencyEvacuation(): JsonResponse
    {
        // In a real app, this would trigger emergency protocols
        // For now, just return a success message
        return response()->json([
            'success' => true,
            'message' => 'Emergency evacuation protocol activated! All visitors have been notified.'
        ]);
    }

    public function buildingDirectory(): JsonResponse
    {
        $facilities = Facility::all();
        return response()->json($facilities);
    }

    public function exportExcel()
    {
        return Excel::download(new VisitorExport, 'visitor_report.xlsx');
    }

    public function exportPdf()
    {
        $visitors = \App\Models\Visitor::with('facility')->latest()->get();
        $pdf = Pdf::loadView('visitor.export_pdf', compact('visitors'));
        return $pdf->download('visitor_report.pdf');
    }

    public function exportReport(Request $request)
    {
        try {
            $data = $request->json()->all();
            $timeRange = $data['timeRange'] ?? 'This Week';
            $statistics = $data['statistics'] ?? [];
            $analytics = $data['analytics'] ?? [];
            
            // Create a comprehensive report
            $reportData = [
                'Report Information' => [
                    ['Field', 'Value'],
                    ['Generated At', now()->format('Y-m-d H:i:s')],
                    ['Time Range', $timeRange],
                    ['Report Type', 'Visitor Analytics Report'],
                    ['', '']
                ],
                'Summary Statistics' => [
                    ['Metric', 'Value'],
                    ['Total Visitors', $statistics['Total Visitors'] ?? 'N/A'],
                    ['Avg. Visit Duration', $statistics['Avg. Visit Duration'] ?? 'N/A'],
                    ['Peak Capacity', $statistics['Peak Capacity'] ?? 'N/A'],
                    ['Security Incidents', $statistics['Security Incidents'] ?? 'N/A'],
                    ['', '']
                ],
                'Peak Visiting Hours' => [
                    ['Time', 'Visitor Count'],
                    ...($analytics['peakHours'] ?? [])
                ],
                'Visitors by Department' => [
                    ['Department', 'Count', 'Percentage'],
                    ...($analytics['departments'] ?? [])
                ],
                'Visit Purposes' => [
                    ['Purpose', 'Count', 'Percentage'],
                    ...($analytics['purposes'] ?? [])
                ],
                'Highlights' => [
                    ['Highlight'],
                    ...array_map(fn($item) => [$item], $analytics['summary']['highlights'] ?? [])
                ],
                'Areas for Improvement' => [
                    ['Improvement'],
                    ...array_map(fn($item) => [$item], $analytics['summary']['improvements'] ?? [])
                ],
                'Recommendations' => [
                    ['Recommendation'],
                    ...array_map(fn($item) => [$item], $analytics['summary']['recommendations'] ?? [])
                ]
            ];
            
            // Generate Excel file
            $filename = 'visitor-analytics-report-' . strtolower(str_replace(' ', '-', $timeRange)) . '-' . now()->format('Y-m-d') . '.xlsx';
            
            return Excel::download(new \App\Exports\VisitorReportExport($reportData), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Export report error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report'], 500);
        }
    }

    public function manageReservationVisitors(\App\Models\FacilityReservation $reservation)
    {
        // Load the visitor coordination task for this reservation
        $visitorTask = $reservation->tasks()->where('task_type', 'visitor_coordination')->firstOrFail();

        // Load associated visitors (if any have been extracted)
        $visitors = $reservation->visitors()->get();

        // Retrieve AI classification result from the document task
        $documentTask = $reservation->tasks()->where('task_type', 'document_classification')->first();
        $aiClassification = $documentTask ? ($documentTask->details['ai_classification'] ?? null) : null;
        
        return view('visitor.manage_reservation_visitors', compact('reservation', 'visitorTask', 'visitors', 'aiClassification'));
    }

    public function performExtractionFromReservation(\App\Models\FacilityReservation $reservation)
    {
        $visitorTask = $reservation->tasks()->where('task_type', 'visitor_coordination')->firstOrFail();
        
        if ($visitorTask->status !== 'pending') {
            return redirect()->back()->with('error', 'Visitor extraction is not pending for this reservation task.');
        }

        // Retrieve AI classification result from the document task to pass to the job
        $documentTask = $reservation->tasks()->where('task_type', 'document_classification')->first();
        $aiResult = $documentTask ? ($documentTask->details['ai_classification'] ?? []) : [];

        if (empty($aiResult)) {
            return redirect()->back()->with('error', 'AI classification data not found for visitor extraction. Cannot proceed.');
        }

        // Update task status, which will trigger the ProcessVisitorExtractionJob via WorkflowService
        $this->workflowService->updateTaskStatus($visitorTask, 'in_progress', 'Visitor data extraction initiated by VM team.');
        
        return redirect()->back()->with('success', 'Visitor data extraction process started. Please refresh to see updates.');
    }

    public function performApprovalFromReservation(\App\Models\FacilityReservation $reservation)
    {
        $visitorTask = $reservation->tasks()->where('task_type', 'visitor_coordination')->firstOrFail();
        
        if ($visitorTask->status !== 'pending' && $visitorTask->status !== 'in_progress') {
            return redirect()->back()->with('error', 'Visitor approval is not pending or in progress for this task.');
        }

        if ($reservation->visitors->isEmpty()) {
            return redirect()->back()->with('error', 'No visitors records found for this reservation to approve. Please extract first.');
        }

        // Update task status to completed, which will trigger GenerateDigitalPasses via WorkflowService
        $this->workflowService->updateTaskStatus($visitorTask, 'completed', 'Visitors approved by VM team.');
        
        return redirect()->back()->with('success', 'Visitors approved! Digital passes are being generated and security team will be notified.');
    }

    /**
     * Calculate pass validity dates based on the selected validity period
     */
    private function calculatePassValidity(Request $request): array
    {
        $now = now();
        $validFrom = $now;
        $validUntil = $now;

        // If expected date and time are provided, combine them for pass validity
        if ($request->expected_date_out && $request->expected_time_out) {
            $expectedTimeOut = \Carbon\Carbon::parse($request->expected_date_out . ' ' . $request->expected_time_out);
            $validFrom = $now;
            $validUntil = $expectedTimeOut;
        } elseif ($request->expected_time_out) {
            // If only time is provided, assume today's date
            $expectedTimeOut = \Carbon\Carbon::parse($request->expected_time_out);
            $validFrom = $now;
            $validUntil = $expectedTimeOut;
        } elseif ($request->pass_validity === 'custom') {
            $validFrom = $request->pass_valid_from ? \Carbon\Carbon::parse($request->pass_valid_from) : $now;
            $validUntil = $request->pass_valid_until ? \Carbon\Carbon::parse($request->pass_valid_until) : $now->addHours(24);
        } else {
            switch ($request->pass_validity) {
                case '1_hour':
                    $validUntil = $now->copy()->addHour();
                    break;
                case '4_hours':
                    $validUntil = $now->copy()->addHours(4);
                    break;
                case '24_hours':
                    $validUntil = $now->copy()->addHours(24);
                    break;
                case '1_day':
                    $validUntil = $now->copy()->addDay();
                    break;
                case '3_days':
                    $validUntil = $now->copy()->addDays(3);
                    break;
                case '1_week':
                    $validUntil = $now->copy()->addWeek();
                    break;
                default:
                    $validUntil = $now->copy()->addHours(24);
            }
        }

        return [
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ];
    }

    /**
     * Generate a unique pass ID
     */
    private function generatePassId(): string
    {
        do {
            $passId = 'PASS-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (Visitor::where('pass_id', $passId)->exists());

        return $passId;
    }

    /**
     * Generate digital pass data for the visitor
     */
    private function generateDigitalPass(Visitor $visitor): void
    {
        $passData = [
            'pass_id' => $visitor->pass_id,
            'visitor_name' => $visitor->name,
            'visitor_id' => $visitor->id,
            'pass_type' => $visitor->pass_type,
            'access_level' => $visitor->access_level,
            'escort_required' => $visitor->escort_required,
            'valid_from' => $visitor->pass_valid_from->format('Y-m-d H:i:s'),
            'valid_until' => $visitor->pass_valid_until->format('Y-m-d H:i:s'),
            'facility' => $visitor->facility->name ?? 'N/A',
            'purpose' => $visitor->purpose ?? 'N/A',
            'special_instructions' => $visitor->special_instructions,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'qr_code' => $this->generateQRCode($visitor->pass_id),
        ];

        $visitor->update(['pass_data' => $passData]);
    }

    /**
     * Generate QR code data for the pass
     */
    private function generateQRCode(string $passId): string
    {
        // Payload users should be taken to when QR is scanned
        $payload = url("/verify-pass/{$passId}");
        // Use a public QR image generator to avoid adding new dependencies
        // Keep this stable so the same QR is shown in UI and emails
        $qrService = 'https://api.qrserver.com/v1/create-qr-code/';
        $params = http_build_query([
            'size' => '200x200',
            'data' => $payload
        ]);
        return "{$qrService}?{$params}";
    }

    /**
     * Log visitor activity (check-in, check-out, registration)
     */
    private function logVisitorActivity(Visitor $visitor, string $action, string $notes = null): void
    {
        VisitorCheckinLog::create([
            'visitor_id' => $visitor->id,
            'checked_in_by' => auth()->id(),
            'action' => $action,
            'notes' => $notes,
            'visitor_data' => $visitor->toArray(),
            'action_time' => now()
        ]);
    }

    /**
     * Get check-in monitoring data
     */
    public function getCheckinMonitoring(Request $request): JsonResponse
    {
        $query = VisitorCheckinLog::with(['visitor', 'checkedInBy']);

        // Apply filters
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('action_time', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('action_time', '<=', $request->date_to);
        }

        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('checked_in_by', $request->user_id);
        }

        $logs = $query->latest('action_time')->paginate(20);

        return response()->json($logs);
    }

    /**
     * Get check-in statistics
     */
    public function getCheckinStats(): JsonResponse
    {
        $today = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        $stats = [
            'today_registrations' => VisitorCheckinLog::registrations()->today()->count(),
            'today_checkins' => VisitorCheckinLog::checkins()->today()->count(),
            'today_checkouts' => VisitorCheckinLog::checkouts()->today()->count(),
            'this_week_registrations' => VisitorCheckinLog::registrations()->thisWeek()->count(),
            'this_month_registrations' => VisitorCheckinLog::registrations()->thisMonth()->count(),
            'most_active_user' => $this->getMostActiveUser(),
            'recent_activities' => VisitorCheckinLog::with(['visitor', 'checkedInBy'])
                ->latest('action_time')
                ->limit(10)
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Get most active user (most check-ins/registrations)
     */
    private function getMostActiveUser(): ?array
    {
        $user = VisitorCheckinLog::with('checkedInBy')
            ->selectRaw('checked_in_by, COUNT(*) as activity_count')
            ->where('action', '!=', 'checkout')
            ->groupBy('checked_in_by')
            ->orderBy('activity_count', 'desc')
            ->first();

        if (!$user) {
            return null;
        }

        return [
            'user' => $user->checkedInBy,
            'activity_count' => $user->activity_count
        ];
    }

    /**
     * Get visitors with passes for monitoring table
     */
    public function getMonitoringVisitors(): JsonResponse
    {
        try {
            \Log::info('Getting monitoring visitors...');
            
            // Include all visitors (with or without pass) so newly registered, unapproved entries appear as Pending
            $visitors = Visitor::with(['facility'])
                ->orderByDesc('created_at')
                ->get();
                
            \Log::info('Found ' . $visitors->count() . ' visitors with passes');
            
            $mappedVisitors = $visitors->map(function ($visitor) {
                    // Determine display status with robust rules:
                    // - If not checked in yet (no time_in), always Pending
                    // - If checked out, Completed
                    // - If explicitly registered/pending_approval, Pending
                    // - If revoked, Revoked
                    // - If expired by validity, Expired
                    // - Else, Active
                    if (is_null($visitor->time_in)) {
                        $status = 'Pending';
                    } elseif (!is_null($visitor->time_out) || $visitor->status === 'checked_out') {
                        $status = 'Completed';
                    } elseif (in_array($visitor->status, ['registered', 'pending_approval'])) {
                        $status = 'Pending';
                    } elseif ($visitor->status === 'revoked') {
                        $status = 'Revoked';
                    } elseif ($visitor->pass_valid_until && \Carbon\Carbon::now()->gt($visitor->pass_valid_until)) {
                        $status = 'Expired';
                    } else {
                        $status = 'Active';
                    }

                    return [
                        'id' => $visitor->id,
                        'name' => $visitor->name,
                        'email' => $visitor->email,
                        'contact' => $visitor->contact,
                        'id_type' => $visitor->id_type,
                        'id_number' => $visitor->id_number,
                        'company' => $visitor->company ?? 'N/A',
                        'purpose' => $visitor->purpose ?? 'N/A',
                        'host_employee' => $visitor->host_employee ?? 'N/A',
                        'department' => $visitor->department ?? 'N/A',
                        'check_in_time' => $visitor->arrival_date && $visitor->arrival_time ? \Carbon\Carbon::parse(\Carbon\Carbon::parse($visitor->arrival_date)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($visitor->arrival_time)->format('H:i:s'))->format('M j, Y g:i A') : 'N/A',
                        'actual_check_in_time' => $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in)->format('M j, Y g:i A') : 'N/A',
                        // Expected date out: separate date field
                        'expected_date_out' => $visitor->expected_date_out ? \Carbon\Carbon::parse($visitor->expected_date_out)->format('M j, Y') : 'N/A',
                        // Expected time out: use the actual expected_time_out field
                        'expected_time_out' => $visitor->expected_time_out ? \Carbon\Carbon::parse($visitor->expected_time_out)->format('g:i A') : 'N/A',
                        'vehicle_plate' => $visitor->vehicle_plate ?? 'N/A',
                        'status' => $status,
                        'pass_id' => $visitor->pass_id,
                        'facility_name' => $visitor->facility ? $visitor->facility->name : 'N/A'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $mappedVisitors
            ]);

        } catch (\Exception $e) {
            \Log::error('Monitoring visitors error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Unable to load monitoring data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get visitor pass details for viewing
     */
    public function getVisitorPass($id): JsonResponse
    {
        try {
            \Log::info('Getting visitor pass for ID: ' . $id);
            
            $visitor = Visitor::findOrFail($id);
            \Log::info('Visitor found: ' . $visitor->name . ', Pass ID: ' . ($visitor->pass_id ?? 'NULL'));

            // Check if visitor has a pass
            if (!$visitor->pass_id) {
                \Log::warning('Visitor ' . $visitor->name . ' (ID: ' . $id . ') has no pass_id');
                return response()->json([
                    'success' => false,
                    'message' => 'No pass found for this visitor'
                ], 404);
            }

            // Calculate pass validity
            $validForHours = $visitor->pass_validity ?? 24;
            $checkInTime = $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in) : null;
            $issuedAt = $visitor->pass_valid_from ? \Carbon\Carbon::parse($visitor->pass_valid_from) : $visitor->created_at;
            
            // Determine pass status - handle all cases including expired passes
            $status = 'Active';
            if ($visitor->status === 'checked_out') {
                $status = 'Used';
            } elseif ($visitor->status === 'revoked') {
                $status = 'Revoked';
            } elseif ($visitor->pass_valid_until && \Carbon\Carbon::now()->gt($visitor->pass_valid_until)) {
                $status = 'Expired';
            }

            // Generate download URL (you may need to implement actual pass generation)
            $downloadUrl = route('visitor.pass.download', $visitor->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'visitor_id' => $visitor->id,
                    'pass_id' => $visitor->pass_id,
                    'pass_number' => $visitor->pass_id,
                    'visitor_name' => $visitor->name,
                    'company' => $visitor->company ?? 'N/A',
                    'purpose' => $visitor->purpose ?? 'N/A',
                    'valid_for_hours' => $validForHours,
                    'status' => $status,
                    'issued_at' => $issuedAt->toISOString(),
                    'check_in_time' => $checkInTime ? $checkInTime->toISOString() : null,
                    'download_url' => $downloadUrl,
                    'pass_type' => $visitor->pass_type ?? 'Visitor Pass',
                    'access_level' => $visitor->access_level ?? 'Standard',
                    'escort_required' => $visitor->escort_required ?? false,
                    'special_instructions' => $visitor->special_instructions ?? null,
                    'valid_from' => $visitor->pass_valid_from ? \Carbon\Carbon::parse($visitor->pass_valid_from)->toISOString() : null,
                    'valid_until' => $visitor->pass_valid_until ? \Carbon\Carbon::parse($visitor->pass_valid_until)->toISOString() : null,
                    'arrival_date' => $visitor->arrival_date ? \Carbon\Carbon::parse($visitor->arrival_date)->format('M j, Y') : null,
                    'arrival_time' => $visitor->arrival_time ? \Carbon\Carbon::parse($visitor->arrival_time)->format('g:i A') : null,
                    'expected_date_out' => $visitor->expected_date_out ? \Carbon\Carbon::parse($visitor->expected_date_out)->format('M j, Y') : null,
                    'expected_time_out' => $visitor->expected_time_out ? \Carbon\Carbon::parse($visitor->expected_time_out)->format('g:i A') : null,
                    'facility' => $visitor->facility ? $visitor->facility->name : 'N/A',
                    'qr_code' => $visitor->pass_data['qr_code'] ?? $this->generateQRCode($visitor->pass_id)
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error getting visitor pass: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to load pass details'
            ], 500);
        }
    }

    /**
     * Download visitor pass
     */
    public function downloadVisitorPass($id)
    {
        try {
            $visitor = Visitor::findOrFail($id);
            
            if (!$visitor->pass_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pass found for this visitor'
                ], 404);
            }

            // For now, return a simple response indicating the download would work
            // In a real implementation, you would generate and return a PDF or image
            return response()->json([
                'success' => true,
                'message' => 'Pass download initiated',
                'download_url' => route('visitor.pass.download', $visitor->id)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error downloading visitor pass: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to download pass'
            ], 500);
        }
    }

    /**
     * Approve a newly registered visitor (pre-check-in stage)
     */
    public function approveVisitor($id)
    {
        $visitor = Visitor::findOrFail($id);
        // Approve and auto check-in
        $visitor->update([
            'status' => 'active',
            'time_in' => $visitor->time_in ?? now(),
            // Ensure visitor appears in Current Visitors by clearing any prior checkout
            'time_out' => null,
        ]);

        // Ensure pass data exists
        if (!$visitor->pass_id) {
            $visitor->update(['pass_id' => $this->generatePassId()]);
        }
        if (!$visitor->pass_valid_from) {
            $visitor->update([
                'pass_type' => $visitor->pass_type ?? 'visitor',
                'pass_validity' => $visitor->pass_validity ?? '24_hours',
                'pass_valid_from' => now(),
                'pass_valid_until' => now()->addHours(24),
                'access_level' => $visitor->access_level ?? null,
                'escort_required' => $visitor->escort_required ?? 'no',
            ]);
        }
        $this->generateDigitalPass($visitor);

        // Log actions
        $this->logVisitorActivity($visitor, 'checkin', 'Visitor approved and auto-checked in');

        // Beautiful approval email (Markdown Mailable)
        try {
            if (!empty($visitor->email)) {
                Mail::to($visitor->email)->send(new \App\Mail\VisitorApprovedMail($visitor));
            }
        } catch (\Throwable $e) {
            \Log::error('Failed sending visitor approved email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Visitor approved, auto-checked in, and pass emailed.');
    }

    /**
     * Decline a newly registered visitor
     */
    public function declineVisitor($id)
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->update(['status' => 'declined']);

        // Optionally log the action
        $this->logVisitorActivity($visitor, 'register', 'Visitor declined');

        return redirect()->back()->with('success', 'Visitor declined.');
    }
} 