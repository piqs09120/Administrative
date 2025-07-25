<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComplianceExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplianceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role ?? null;
            if (!in_array(strtolower($role), ['administrator', 'manager'])) {
                abort(403, 'Only Administrators and Managers can access compliance reports.');
            }
            return $next($request);
        })->only(['index', 'reports', 'exportExcel', 'exportPdf']);
    }

    public function health()
    {
        $healthChecks = [
            [
                'category' => 'Fire Safety',
                'items' => [
                    ['name' => 'Fire Extinguishers', 'status' => 'compliant', 'last_check' => '2024-01-08', 'next_due' => '2024-04-08'],
                    ['name' => 'Smoke Detectors', 'status' => 'compliant', 'last_check' => '2024-01-05', 'next_due' => '2024-04-05'],
                    ['name' => 'Emergency Exits', 'status' => 'compliant', 'last_check' => '2024-01-10', 'next_due' => '2024-04-10'],
                    ['name' => 'Sprinkler System', 'status' => 'pending', 'last_check' => '2023-12-15', 'next_due' => '2024-01-15'],
                ]
            ],
            [
                'category' => 'Health & Safety',
                'items' => [
                    ['name' => 'First Aid Kits', 'status' => 'compliant', 'last_check' => '2024-01-09', 'next_due' => '2024-04-09'],
                    ['name' => 'Safety Signage', 'status' => 'compliant', 'last_check' => '2024-01-07', 'next_due' => '2024-04-07'],
                    ['name' => 'Emergency Lighting', 'status' => 'non-compliant', 'last_check' => '2023-11-20', 'next_due' => '2024-01-20'],
                    ['name' => 'HVAC System', 'status' => 'compliant', 'last_check' => '2024-01-06', 'next_due' => '2024-04-06'],
                ]
            ]
        ];
        
        return view('compliance.health', compact('healthChecks'));
    }
    
    public function fire()
    {
        $fireChecks = [
            ['equipment' => 'Fire Extinguisher - Lobby', 'type' => 'ABC Dry Chemical', 'location' => 'Main Lobby', 'status' => 'Good', 'last_inspection' => '2024-01-08', 'next_due' => '2024-04-08'],
            ['equipment' => 'Fire Extinguisher - Kitchen', 'type' => 'Class K Wet Chemical', 'location' => 'Main Kitchen', 'status' => 'Good', 'last_inspection' => '2024-01-08', 'next_due' => '2024-04-08'],
            ['equipment' => 'Smoke Detector - Room 101', 'type' => 'Photoelectric', 'location' => 'Guest Room 101', 'status' => 'Good', 'last_inspection' => '2024-01-05', 'next_due' => '2024-04-05'],
            ['equipment' => 'Fire Alarm Panel', 'type' => 'Addressable System', 'location' => 'Security Office', 'status' => 'Needs Attention', 'last_inspection' => '2023-12-20', 'next_due' => '2024-01-20'],
        ];
        
        return view('compliance.fire', compact('fireChecks'));
    }
    
    public function food()
    {
        $foodSafety = [
            ['area' => 'Main Kitchen', 'temperature' => '38°F', 'status' => 'Good', 'last_check' => '2024-01-10 14:30', 'inspector' => 'John Smith'],
            ['area' => 'Walk-in Freezer', 'temperature' => '0°F', 'status' => 'Good', 'last_check' => '2024-01-10 14:25', 'inspector' => 'John Smith'],
            ['area' => 'Prep Area', 'temperature' => '40°F', 'status' => 'Warning', 'last_check' => '2024-01-10 14:20', 'inspector' => 'John Smith'],
            ['area' => 'Dishwashing Station', 'temperature' => '180°F', 'status' => 'Good', 'last_check' => '2024-01-10 14:15', 'inspector' => 'John Smith'],
        ];
        
        return view('compliance.food', compact('foodSafety'));
    }
    
    public function reports()
    {
        $reports = [
            ['title' => 'Monthly Fire Safety Report', 'type' => 'Fire Safety', 'date' => '2024-01-01', 'status' => 'Completed', 'score' => 98],
            ['title' => 'Health Department Inspection', 'type' => 'Health & Safety', 'date' => '2023-12-15', 'status' => 'Completed', 'score' => 95],
            ['title' => 'Food Safety Audit', 'type' => 'Food Safety', 'date' => '2023-12-10', 'status' => 'Completed', 'score' => 92],
            ['title' => 'Weekly Compliance Check', 'type' => 'General', 'date' => '2024-01-08', 'status' => 'In Progress', 'score' => null],
        ];
        
        return view('compliance.reports', compact('reports'));
    }
    
    public function generateReport(Request $request)
    {
        // Logic to generate compliance report
        return redirect()->route('compliance.reports')->with('success', 'Compliance report generated successfully!');
    }

    public function exportExcel()
    {
        return Excel::download(new ComplianceExport, 'compliance_report.xlsx');
    }

    public function exportPdf()
    {
        $compliances = \App\Models\Compliance::with('document')->latest()->get();
        $pdf = Pdf::loadView('compliance.export_pdf', compact('compliances'));
        return $pdf->download('compliance_report.pdf');
    }

    // List all compliance records
    public function index()
    {
        $compliances = Compliance::with('document')->latest()->get();
        return view('compliance.index', compact('compliances'));
    }

    // Show form to create a new compliance record
    public function create()
    {
        $documents = \App\Models\Document::all();
        return view('compliance.create', compact('documents'));
    }

    // Store a new compliance record
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|string|max:255',
            'document_id' => 'nullable|exists:documents,id',
        ]);

        Compliance::create($request->all());
        return redirect()->route('compliance.index')->with('success', 'Compliance record created!');
    }

    // Show a single compliance record
    public function show($id)
    {
        $compliance = Compliance::with('document')->findOrFail($id);
        return view('compliance.show', compact('compliance'));
    }

    // Show form to edit a compliance record
    public function edit($id)
    {
        $compliance = Compliance::findOrFail($id);
        $documents = \App\Models\Document::all();
        return view('compliance.edit', compact('compliance', 'documents'));
    }

    // Update a compliance record
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|string|max:255',
            'document_id' => 'nullable|exists:documents,id',
        ]);

        $compliance = Compliance::findOrFail($id);
        $compliance->update($request->all());
        return redirect()->route('compliance.show', $id)->with('success', 'Compliance record updated!');
    }

    // Delete a compliance record
    public function destroy($id)
    {
        $compliance = Compliance::findOrFail($id);
        $compliance->delete();
        return redirect()->route('compliance.index')->with('success', 'Compliance record deleted!');
    }
}