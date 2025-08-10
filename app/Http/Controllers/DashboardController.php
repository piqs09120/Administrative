<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;


class DashboardController extends Controller
{
    public function index()
    {
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
}