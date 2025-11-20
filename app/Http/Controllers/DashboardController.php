<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\DashboardActivityService;


class DashboardController extends Controller
{
    public function index()
    {
        \Log::info('DashboardController: index method called');
        \Log::info('User authenticated: ' . (Auth::check() ? 'YES' : 'NO'));
        \Log::info('User ID: ' . (Auth::id() ?? 'NULL'));
        \Log::info('Session user_role: ' . Session::get('user_role', 'NOT_SET'));
        
        // Get real data for dashboard
        $todayDate = now()->toDateString();
        
        // Visitors
        $visitorsToday = \App\Models\Visitor::whereDate('time_in', $todayDate)->count();
        $visitorsCheckedIn = \App\Models\Visitor::whereNull('time_out')->count();
        $visitorsThisWeek = \App\Models\Visitor::whereBetween('time_in', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $visitorsThisMonth = \App\Models\Visitor::whereMonth('time_in', now()->month)->whereYear('time_in', now()->year)->count();
        
        // Reservations
        $approvedReservationsToday = \App\Models\FacilityRequest::where('status', 'approved')->where('request_type', 'reservation')->whereDate('created_at', $todayDate)->count();
        $pendingReservations = \App\Models\FacilityRequest::where('status', 'pending')->count();
        $upcomingReservations = \App\Models\FacilityRequest::where('status', 'approved')->where('requested_datetime', '>', now())->count();
        
        // Documents
        $pendingLegalDocs = \App\Models\Document::where('status', 'pending_review')->count();
        $expiringDocuments = \App\Models\Document::whereNotNull('retention_until')->where('retention_until', '<=', now()->addDays(30))->where('retention_until', '>=', now())->count();
        
        // Legal
        $legalCasesTotal = \App\Models\LegalCase::count();
        $legalCasesPending = \App\Models\LegalCase::where('status', 'pending')->count();
        
        // Users
        $totalUsers = \App\Models\User::count();
        $activeFacilities = \App\Models\Facility::where('status', 'active')->count();
        
        return view('UI', compact(
            'visitorsToday',
            'visitorsCheckedIn',
            'visitorsThisWeek',
            'visitorsThisMonth',
            'approvedReservationsToday',
            'pendingReservations',
            'upcomingReservations',
            'pendingLegalDocs',
            'expiringDocuments',
            'legalCasesTotal',
            'legalCasesPending',
            'totalUsers',
            'activeFacilities'
        ));
    }

    /**
     * Recent activity feed for dashboard widgets.
     */
    public function recentActivity(Request $request, DashboardActivityService $activityService)
    {
        try {
            $limit = (int) $request->get('limit', 10);
            $activities = $activityService->recent($limit);

            return response()->json([
                'success' => true,
                'data' => $activities,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Dashboard recentActivity error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load activity feed',
            ], 500);
        }
    }

    /** Simple active users count for dashboard metrics */
    public function activeUsersCount(Request $request)
    {
        try {
            $count = \App\Models\DeptAccount::where('status', 'active')->count();
            return response()->json(['success' => true, 'active_users' => (int) $count]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'active_users' => 0, 'error' => $e->getMessage()], 200);
        }
    }
    /** Facility Reservations stats for dashboard charts */
    public function facilityStats(Request $request)
    {
        // Last 6 months reservations count
        $months = collect(range(5, 0))->map(function($i){ return now()->subMonths($i)->startOfMonth(); });
        $labels = $months->map(fn($d) => $d->format('M Y'));
        $data = $months->map(function($start){
            $end = (clone $start)->copy()->endOfMonth();
            return (int) \App\Models\FacilityReservation::whereBetween('created_at', [$start, $end])->count();
        });

        // Status breakdown current month
        $cm = now();
        $status = [
            'approved' => (int) \App\Models\FacilityReservation::whereMonth('created_at', $cm->month)->where('status','approved')->count(),
            'pending' => (int) \App\Models\FacilityReservation::whereMonth('created_at', $cm->month)->where('status','pending')->count(),
            'denied' => (int) \App\Models\FacilityReservation::whereMonth('created_at', $cm->month)->where('status','denied')->count(),
        ];

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'data' => $data,
            'status' => $status,
        ]);
    }

    /** User Management stats for dashboard charts */
    public function userMgmtStats(Request $request)
    {
        // Count department accounts by role (top 6)
        $byRole = \App\Models\DeptAccount::query()
            ->select('role', \DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->orderByDesc('count')
            ->limit(6)
            ->get();

        // New users per month (last 6 months)
        $months = collect(range(5, 0))->map(function($i){ return now()->subMonths($i)->startOfMonth(); });
        $labels = $months->map(fn($d) => $d->format('M'));
        $registrations = $months->map(function($start){
            $end = (clone $start)->copy()->endOfMonth();
            return (int) \App\Models\DeptAccount::whereBetween('created_at', [$start, $end])->count();
        });

        return response()->json([
            'success' => true,
            'roles' => $byRole,
            'labels' => $labels,
            'registrations' => $registrations,
        ]);
    }
}