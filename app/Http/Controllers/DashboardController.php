<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class DashboardController extends Controller
{
    public function index()
    {
        \Log::info('DashboardController: index method called');
        \Log::info('User authenticated: ' . (Auth::check() ? 'YES' : 'NO'));
        \Log::info('User ID: ' . (Auth::id() ?? 'NULL'));
        \Log::info('Session user_role: ' . Session::get('user_role', 'NOT_SET'));
        
        // Get key metrics for dashboard
        $totalRooms = 150; // This would come from Room::count() in real implementation
        $occupiedRooms = 128; // This would come from Room::where('status', 'occupied')->count()
        $occupancyRate = round(($occupiedRooms / $totalRooms) * 100);
        
        $revenueToday = 18450; // This would come from actual revenue calculations
        $todaysReservations = 24; // Today's reservations count
        $activeUsers = 18; // Active users count
        $inventoryAlerts = 3; // Low stock items
        
        // Recent activities (mock data - would come from activity logs)
        $recentActivities = [
            [
                'type' => 'checkout',
                'message' => 'Room 205 checked out',
                'time' => '2 minutes ago',
                'icon' => 'fas fa-check',
                'color' => 'success'
            ],
            [
                'type' => 'order',
                'message' => 'New order from Table 12',
                'time' => '5 minutes ago',
                'icon' => 'fas fa-utensils',
                'color' => 'info'
            ],
            [
                'type' => 'reservation',
                'message' => 'New reservation confirmed',
                'time' => '22 minutes ago',
                'icon' => 'fas fa-calendar',
                'color' => 'primary'
            ]
        ];
        
        // System alerts (mock data)
        $systemAlerts = [
            [
                'type' => 'success',
                'title' => 'Compliance Check Passed',
                'message' => 'All safety protocols are up to date',
                'icon' => 'fas fa-shield-alt'
            ],
            [
                'type' => 'warning',
                'title' => 'Temperature Alert',
                'message' => 'Kitchen freezer temperature slightly elevated',
                'icon' => 'fas fa-thermometer-half'
            ],
            [
                'type' => 'info',
                'title' => 'Peak Hours Approaching',
                'message' => 'Dinner rush expected in 30 minutes',
                'icon' => 'fas fa-chart-line'
            ]
        ];
        
        return view('UI', compact(
            'revenueToday',
            'todaysReservations',
            'activeUsers',
            'inventoryAlerts'
        ));
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