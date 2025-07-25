<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Order;
use App\Models\Guest;

class DashboardController extends Controller
{
    public function index()
    {
        // Get key metrics for dashboard
        $totalRooms = 150; // This would come from Room::count() in real implementation
        $occupiedRooms = 128; // This would come from Room::where('status', 'occupied')->count()
        $occupancyRate = round(($occupiedRooms / $totalRooms) * 100);
        
        $todayRevenue = 18450; // This would come from actual revenue calculations
        $activeReservations = 127; // This would come from Reservation::where('status', 'active')->count()
        $complianceScore = 96; // This would come from compliance calculations
        
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
                'type' => 'alert',
                'message' => 'Kitchen inventory alert',
                'time' => '15 minutes ago',
                'icon' => 'fas fa-exclamation',
                'color' => 'warning'
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
        
        return view('dashboard', compact(
            'occupancyRate',
            'todayRevenue',
            'activeReservations',
            'complianceScore',
            'recentActivities',
            'systemAlerts'
        ));
    }
}