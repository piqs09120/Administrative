<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VisitorExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\VisitorService;

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

    public function index()
    {
        $visitors = Visitor::with('facility')->latest()->get();
        $facilities = Facility::all();
        $users = User::all();
        return view('visitor.index', compact('visitors', 'facilities', 'users'));
    }

    public function create()
    {
        $facilities = Facility::all();
        $users = User::all();
        return view('visitor.create', compact('facilities', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:facilities,id',
            'time_in' => 'required|date',
            'company' => 'nullable|string|max:255',
            'host_employee' => 'nullable|string|max:255',
        ]);

        $visitor = Visitor::create($request->all());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Visitor registered successfully!',
                'visitor' => $visitor->load('facility')
            ]);
        }
        
        return redirect()->route('visitor.index')->with('success', 'Visitor logged successfully!');
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
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked in successfully!',
            'visitor' => $visitor->load('facility')
        ]);
    }

    public function checkOut($id): JsonResponse
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->update(['time_out' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked out successfully!',
            'visitor' => $visitor->load('facility')
        ]);
    }

    public function getCurrentVisitors(): JsonResponse
    {
        $visitors = Visitor::with('facility')
            ->whereNull('time_out')
            ->latest()
            ->get();
            
        return response()->json($visitors);
    }

    public function getScheduledVisits(): JsonResponse
    {
        // For now, return visitors scheduled for today
        // In a real app, you'd have a separate scheduled_visits table
        $visitors = Visitor::with('facility')
            ->whereDate('time_in', now()->toDateString())
            ->whereNull('time_out') // Only show scheduled visits that haven't checked out
            ->latest()
            ->get();
            
        return response()->json($visitors);
    }

    public function getVisitorStats(): JsonResponse
    {
        $totalVisitors = Visitor::count();
        $currentlyIn = Visitor::whereNull('time_out')->count();
        $todayVisitors = Visitor::whereDate('time_in', today())->count();
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
} 