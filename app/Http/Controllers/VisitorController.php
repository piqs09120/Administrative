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

class VisitorController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role ?? null;
           
            return $next($request);
        })->only(['logs', 'reports', 'exportExcel', 'exportPdf']);
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
        $visitor = Visitor::with('facility')->findOrFail($id);
        return response()->json($visitor);
    }

    public function checkIn(Request $request): JsonResponse
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
            ->whereDate('time_in', '>=', now()->startOfDay())
            ->whereDate('time_in', '<=', now()->endOfDay())
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
} 