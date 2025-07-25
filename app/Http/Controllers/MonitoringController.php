<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Order;
use App\Models\EnergyUsage;
use App\Models\Alert;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function occupancy()
    {
        $total_rooms = Room::count();
        $rooms_occupied = Room::where('status', 'occupied')->count();
        $current_rate = $total_rooms > 0 ? round(($rooms_occupied / $total_rooms) * 100) : 0;
        $target_rate = 90;
        $revenue_per_room = Reservation::where('status', 'completed')->avg('total_price') ?? 0;

        // Weekly data (last 7 days)
        $weekly_data = [];
        $start = Carbon::now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $day = $date->format('l');
            $day_reservations = Reservation::whereDate('check_in', $date)->get();
            $rate = $total_rooms > 0 ? round(($day_reservations->count() / $total_rooms) * 100) : 0;
            $revenue = $day_reservations->sum('total_price');
            $weekly_data[] = [
                'day' => $day,
                'rate' => $rate,
                'revenue' => $revenue
            ];
        }

        $occupancyData = [
            'current_rate' => $current_rate,
            'target_rate' => $target_rate,
            'rooms_occupied' => $rooms_occupied,
            'total_rooms' => $total_rooms,
            'revenue_per_room' => $revenue_per_room,
            'weekly_data' => $weekly_data
        ];
        return view('monitoring.occupancy', compact('occupancyData'));
    }

    public function revenue()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $this_month = Carbon::now()->format('Y-m');
        $last_month = Carbon::now()->subMonth()->format('Y-m');

        $today_revenue = Order::whereDate('created_at', $today)->sum('total_amount');
        $yesterday_revenue = Order::whereDate('created_at', $yesterday)->sum('total_amount');
        $this_month_revenue = Order::whereYear('created_at', substr($this_month, 0, 4))
            ->whereMonth('created_at', substr($this_month, 5, 2))->sum('total_amount');
        $last_month_revenue = Order::whereYear('created_at', substr($last_month, 0, 4))
            ->whereMonth('created_at', substr($last_month, 5, 2))->sum('total_amount');
        $growth_rate = $last_month_revenue > 0 ? round((($this_month_revenue - $last_month_revenue) / $last_month_revenue) * 100, 2) : 0;

        // Monthly data for the last 6 months
        $monthly_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $hotel = Reservation::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_price');
            $restaurant = Order::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_amount');
            $monthly_data[] = [
                'month' => $month->format('M'),
                'hotel' => $hotel,
                'restaurant' => $restaurant
            ];
        }

        $revenueData = [
            'today' => $today_revenue,
            'yesterday' => $yesterday_revenue,
            'this_month' => $this_month_revenue,
            'last_month' => $last_month_revenue,
            'growth_rate' => $growth_rate,
            'monthly_data' => $monthly_data
        ];
        return view('monitoring.revenue', compact('revenueData'));
    }

    public function energy()
    {
        $current_month = Carbon::now()->format('Y-m');
        $current_usage = EnergyUsage::whereYear('recorded_at', substr($current_month, 0, 4))
            ->whereMonth('recorded_at', substr($current_month, 5, 2))->sum('usage');
        $monthly_budget = 75000; // kWh (static, can be made dynamic)
        $cost_per_kwh = 0.12;
        $monthly_cost = $current_usage * $cost_per_kwh;
        $efficiency_score = $monthly_budget > 0 ? round(($current_usage / $monthly_budget) * 100) : 0;

        $areas = EnergyUsage::select('area')
            ->distinct()
            ->pluck('area');
        $area_data = [];
        foreach ($areas as $area) {
            $usage = EnergyUsage::where('area', $area)
                ->whereYear('recorded_at', substr($current_month, 0, 4))
                ->whereMonth('recorded_at', substr($current_month, 5, 2))
                ->sum('usage');
            $percentage = $current_usage > 0 ? round(($usage / $current_usage) * 100) : 0;
            $area_data[] = [
                'name' => $area,
                'usage' => $usage,
                'percentage' => $percentage
            ];
        }

        $energyData = [
            'current_usage' => $current_usage,
            'monthly_budget' => $monthly_budget,
            'cost_per_kwh' => $cost_per_kwh,
            'monthly_cost' => $monthly_cost,
            'efficiency_score' => $efficiency_score,
            'areas' => $area_data
        ];
        return view('monitoring.energy', compact('energyData'));
    }

    public function alerts()
    {
        $alerts = Alert::latest()->take(20)->get();
        return view('monitoring.alerts', compact('alerts'));
    }
}