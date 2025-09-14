<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VisitorLogController extends Controller
{
    public function index(Request $request)
    {
        // Get basic statistics
        $stats = $this->getBasicStats();
        
        // Get all visitors for the logs table
        $visitors = Visitor::with('facility')
            ->latest()
            ->paginate(20);
            
        // Get facilities for filters
        $facilities = Facility::all();
        
        // Get active tab from request parameter
        $validTabs = ['logs', 'reports'];
        $tabParam = $request->get('tab');
        $activeTab = in_array($tabParam, $validTabs) ? $tabParam : 'logs';
        
        return view('visitor.logs', compact('stats', 'visitors', 'facilities', 'activeTab'));
    }

    public function getAnalytics(Request $request): JsonResponse
    {
        $timeRange = $request->get('time_range', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Set date range based on time range
        $dates = $this->getDateRange($timeRange, $startDate, $endDate);
        
        $analytics = [
            'daily_trends' => $this->getDailyTrends($dates['start'], $dates['end']),
            'visitor_types' => $this->getVisitorTypes($dates['start'], $dates['end']),
            'hosts_departments' => $this->getHostsDepartments($dates['start'], $dates['end']),
            'peak_hours' => $this->getPeakHours($dates['start'], $dates['end']),
            'most_visited_facility' => $this->getMostVisitedFacility($dates['start'], $dates['end']),
            'return_visitors' => $this->getReturnVisitors($dates['start'], $dates['end']),
            'statistics' => $this->getDetailedStats($dates['start'], $dates['end'])
        ];
        
        return response()->json($analytics);
    }

    public function getLogs(Request $request): JsonResponse
    {
        $query = Visitor::with('facility');
        
        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('time_in', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('time_in', '<=', $request->end_date);
        }
        
        if ($request->has('facility_id') && $request->facility_id) {
            $query->where('facility_id', $request->facility_id);
        }
        
        if ($request->has('status') && $request->status) {
            if ($request->status === 'checked_in') {
                $query->whereNull('time_out');
            } elseif ($request->status === 'checked_out') {
                $query->whereNotNull('time_out');
            }
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('host_employee', 'like', "%{$search}%");
            });
        }
        
        $visitors = $query->latest()->paginate(20);
        
        return response()->json($visitors);
    }

    public function search(Request $request): JsonResponse
    {
        $query = Visitor::with('facility');
        
        // Apply search criteria
        if ($request->has('visitor_name') && $request->visitor_name) {
            $query->where('name', 'like', "%{$request->visitor_name}%");
        }
        
        if ($request->has('company') && $request->company) {
            $query->where('company', 'like', "%{$request->company}%");
        }
        
        if ($request->has('host_employee') && $request->host_employee) {
            $query->where('host_employee', 'like', "%{$request->host_employee}%");
        }
        
        if ($request->has('purpose') && $request->purpose) {
            $query->where('purpose', $request->purpose);
        }
        
        if ($request->has('start_time') && $request->start_time) {
            $query->where('time_in', '>=', $request->start_time);
        }
        
        if ($request->has('end_time') && $request->end_time) {
            $query->where('time_in', '<=', $request->end_time);
        }
        
        // Duration filter
        if ($request->has('duration') && $request->duration) {
            $this->applyDurationFilter($query, $request->duration);
        }
        
        $visitors = $query->latest()->get();
        
        return response()->json($visitors);
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily,weekly,monthly,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel,csv'
        ]);
        
        $dates = $this->getDateRange($request->report_type, $request->start_date, $request->end_date);
        
        // Core datasets used in Reports & Analytics
        $visitors = Visitor::with('facility')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->get();

        $statistics = $this->getDetailedStats($dates['start'], $dates['end']);
        $visitorTypes = $this->getVisitorTypes($dates['start'], $dates['end']);
        $peakHours = $this->getPeakHours($dates['start'], $dates['end']);
        $departments = $this->getHostsDepartments($dates['start'], $dates['end']);

        // Overstays: expected_time_out in range and still not checked out beyond expected
        $overstays = Visitor::whereBetween('created_at', [$dates['start'], $dates['end']])
            ->whereNotNull('expected_time_out')
            ->whereNull('time_out')
            ->where('expected_time_out', '<', now())
            ->with('facility')
            ->get();

        // Security incidents placeholder (no incidents table yet)
        $securityIncidents = collect();

        // Build a normalized export payload for multi-sheet Excel
        $exportPayload = [
            'Overview' => [
                ['Metric', 'Value'],
                ['Total Visitors', $statistics['total_visitors']],
                ['Currently In', $statistics['currently_in']],
                ['Completed Visits', $statistics['completed_visits']],
                ['Average Duration', $statistics['average_duration']],
            ],
            'Visitors' => array_merge([
                ['Name', 'Company', 'Purpose', 'Department', 'Facility', 'Check In', 'Check Out']
            ], $visitors->map(function ($v) {
                return [
                    $v->name,
                    $v->company ?? 'N/A',
                    $v->purpose ?? 'N/A',
                    $v->department ?? 'N/A',
                    optional($v->facility)->name ?? 'N/A',
                    $v->time_in ? \Carbon\Carbon::parse($v->time_in)->format('Y-m-d H:i') : 'N/A',
                    $v->time_out ? \Carbon\Carbon::parse($v->time_out)->format('Y-m-d H:i') : '—',
                ];
            })->toArray()),
            'Visitors by Purpose' => array_merge([
                ['Purpose', 'Count']
            ], collect($visitorTypes)->map(function ($count, $purpose) {
                return [$purpose ?? 'N/A', (int) $count];
            })->values()->toArray()),
            'Peak Visiting Hours' => array_merge([
                ['Hour', 'Count']
            ], collect($peakHours)->map(function ($h) {
                return [sprintf('%02d:00', $h['hour']), (int) $h['count']];
            })->toArray()),
            'Departments' => array_merge([
                ['Department', 'Count']
            ], collect($departments)->map(function ($d) {
                return [$d['name'], (int) $d['count']];
            })->toArray()),
            'Overstays' => array_merge([
                ['Name', 'Department', 'Facility', 'Expected Time Out']
            ], $overstays->map(function ($v) {
                return [
                    $v->name,
                    $v->department ?? 'N/A',
                    optional($v->facility)->name ?? 'N/A',
                    $v->expected_time_out ? \Carbon\Carbon::parse($v->expected_time_out)->format('Y-m-d H:i') : 'N/A',
                ];
            })->toArray()),
            'Security Incidents' => [
                ['Incident ID', 'Type', 'Date', 'Notes'],
                // Empty until incidents are implemented in DB
            ],
        ];

        $reportData = [
            'export_sheets' => $exportPayload,
            'visitors' => $visitors,
            'statistics' => $statistics,
            'visitor_types' => $visitorTypes,
            'peak_hours' => $peakHours,
            'departments' => $departments,
            'overstays' => $overstays,
            'security_incidents' => $securityIncidents,
            'date_range' => $dates,
            'generated_at' => now(),
        ];
        
        $filename = 'visitor_report_' . $request->report_type . '_' . now()->format('Y-m-d_H-i-s');
        
        switch ($request->input('format')) {
            case 'pdf':
                return $this->generatePdfReport($reportData, $filename);
            case 'excel':
                // Export as multi-sheet workbook containing all sections
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\VisitorReportExport($exportPayload), $filename . '.xlsx');
            case 'csv':
                return $this->generateCsvReport($reportData, $filename);
        }
    }

    public function exportLogs(Request $request)
    {
        $visitors = Visitor::with('facility')->latest()->get();
        
        $filename = 'visitor_logs_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new \App\Exports\VisitorLogExport($visitors), $filename);
    }

    private function getBasicStats(): array
    {
        $today = now()->startOfDay();
        $endOfDay = now()->endOfDay();
        
        return [
            'today' => Visitor::whereBetween('created_at', [$today, $endOfDay])->count(),
            'currently_in' => Visitor::whereNotNull('time_in')->whereNull('time_out')->count(),
            'avg_duration' => $this->getAverageDuration(),
            'peak_hours' => $this->getPeakHoursString()
        ];
    }

    private function getDateRange(string $timeRange, ?string $startDate = null, ?string $endDate = null): array
    {
        switch ($timeRange) {
            case 'today':
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => now()->startOfWeek(),
                    'end' => now()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
            case 'custom':
                return [
                    'start' => Carbon::parse($startDate)->startOfDay(),
                    'end' => Carbon::parse($endDate)->endOfDay()
                ];
            default:
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay()
                ];
        }
    }

    private function getDailyTrends(Carbon $start, Carbon $end): array
    {
        $trends = [];
        $current = $start->copy();
        
        while ($current->lte($end)) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();
            
            // Count visitors created on this day (both registered and checked in)
            $count = Visitor::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            
            $trends[] = [
                'date' => $current->format('Y-m-d'),
                'label' => $current->format('M d'),
                'count' => $count
            ];
            
            $current->addDay();
        }
        
        return $trends;
    }

    private function getVisitorTypes(Carbon $start, Carbon $end): array
    {
        $types = Visitor::whereBetween('created_at', [$start, $end])
            ->select('purpose', DB::raw('count(*) as count'))
            ->groupBy('purpose')
            ->get()
            ->pluck('count', 'purpose')
            ->toArray();
            
        return $types;
    }

    private function getHostsDepartments(Carbon $start, Carbon $end): array
    {
        // Departments should mirror the Host Department dropdown on the visitor landing page
        $dropdownDepartments = [
            'Human Resources',
            'Information Technology',
            'Finance',
            'Operations',
            'Marketing',
            'Legal',
            'Other',
        ];

        // Get counts per department within range
        $counts = Visitor::whereBetween('created_at', [$start, $end])
            ->whereIn('department', $dropdownDepartments)
            ->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->pluck('count', 'department');

        // Build full list including zeros and sort desc
        $result = collect($dropdownDepartments)
            ->map(function ($dept) use ($counts) {
                return [
                    'name' => $dept,
                    'type' => 'department',
                    'count' => (int) ($counts[$dept] ?? 0),
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->toArray();

        return $result;
    }

    private function getPeakHours(Carbon $start, Carbon $end): array
    {
        $hours = [];
        
        for ($i = 0; $i < 24; $i++) {
            $count = Visitor::whereBetween('created_at', [$start, $end])
                ->whereRaw('HOUR(created_at) = ?', [$i])
                ->count();
                
            $hours[] = [
                'hour' => $i,
                'label' => sprintf('%02d:00', $i),
                'count' => $count
            ];
        }
        
        return $hours;
    }

    private function getMostVisitedFacility(Carbon $start, Carbon $end): string
    {
        $facility = Visitor::whereBetween('visitors.created_at', [$start, $end])
            ->join('facilities', 'visitors.facility_id', '=', 'facilities.id')
            ->select('facilities.name', DB::raw('count(*) as count'))
            ->groupBy('facilities.id', 'facilities.name')
            ->orderBy('count', 'desc')
            ->first();
            
        return $facility ? $facility->name : 'N/A';
    }

    private function getReturnVisitors(Carbon $start, Carbon $end): float
    {
        $totalVisitors = Visitor::whereBetween('created_at', [$start, $end])->count();
        
        if ($totalVisitors === 0) {
            return 0;
        }
        
        $returnVisitors = Visitor::whereBetween('created_at', [$start, $end])
            ->select('name', 'company')
            ->groupBy('name', 'company')
            ->havingRaw('count(*) > 1')
            ->count();
            
        return round(($returnVisitors / $totalVisitors) * 100, 1);
    }

    private function getDetailedStats(Carbon $start, Carbon $end): array
    {
        $totalVisitors = Visitor::whereBetween('created_at', [$start, $end])->count();
        $currentlyIn = Visitor::whereBetween('created_at', [$start, $end])
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->count();
        $completedVisits = Visitor::whereBetween('created_at', [$start, $end])
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->count();
            
        return [
            'total_visitors' => $totalVisitors,
            'currently_in' => $currentlyIn,
            'completed_visits' => $completedVisits,
            'average_duration' => $this->getAverageDuration($start, $end)
        ];
    }

    private function getAverageDuration(?Carbon $start = null, ?Carbon $end = null): string
    {
        $query = Visitor::whereNotNull('time_out')->whereNotNull('time_in');
        
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }
        
        $avgMinutes = $query->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, time_in, time_out)) as avg_duration')
            ->value('avg_duration');
            
        if (!$avgMinutes) {
            return '0h';
        }
        
        $hours = floor($avgMinutes / 60);
        $minutes = $avgMinutes % 60;
        
        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    private function getPeakHoursString(): string
    {
        $peakHours = $this->getPeakHours(now()->startOfMonth(), now()->endOfMonth());
        $maxCount = max(array_column($peakHours, 'count'));
        $peakHour = collect($peakHours)->firstWhere('count', $maxCount);
        
        if ($peakHour && $maxCount > 0) {
            $hour = $peakHour['hour'];
            return sprintf('%02d:00 - %02d:00', $hour, $hour + 1);
        }
        
        return '9:00 - 11:00';
    }

    private function applyDurationFilter($query, string $duration): void
    {
        switch ($duration) {
            case 'less_than_1h':
                $query->whereRaw('TIMESTAMPDIFF(MINUTE, time_in, time_out) < 60');
                break;
            case '1h_to_2h':
                $query->whereRaw('TIMESTAMPDIFF(MINUTE, time_in, time_out) BETWEEN 60 AND 120');
                break;
            case '2h_to_4h':
                $query->whereRaw('TIMESTAMPDIFF(MINUTE, time_in, time_out) BETWEEN 120 AND 240');
                break;
            case 'more_than_4h':
                $query->whereRaw('TIMESTAMPDIFF(MINUTE, time_in, time_out) > 240');
                break;
        }
    }

    private function generatePdfReport(array $data, string $filename)
    {
        $pdf = Pdf::loadView('visitor.reports.pdf', $data);
        return $pdf->download($filename . '.pdf');
    }

    private function generateExcelReport(array $data, string $filename)
    {
        return Excel::download(new \App\Exports\VisitorReportExport($data), $filename . '.xlsx');
    }

    private function generateCsvReport(array $data, string $filename)
    {
        $csv = fopen('php://temp', 'w');
        
        // Add headers
        fputcsv($csv, ['Name', 'Company', 'Purpose', 'Facility', 'Check In', 'Check Out', 'Duration', 'Host']);
        
        // Add data
        foreach ($data['visitors'] as $visitor) {
            $duration = $visitor->time_out 
                ? Carbon::parse($visitor->time_in)->diffForHumans(Carbon::parse($visitor->time_out), true)
                : 'Still in';
                
            fputcsv($csv, [
                $visitor->name,
                $visitor->company ?? 'N/A',
                $visitor->purpose ?? 'N/A',
                $visitor->facility->name ?? 'N/A',
                $visitor->time_in ? Carbon::parse($visitor->time_in)->format('Y-m-d H:i:s') : 'N/A',
                $visitor->time_out ? Carbon::parse($visitor->time_out)->format('Y-m-d H:i:s') : 'Still in',
                $duration,
                $visitor->host_employee ?? 'N/A'
            ]);
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
    }
}
