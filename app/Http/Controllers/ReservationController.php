<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = [
            [
                'id' => 1,
                'guest_name' => 'John Doe',
                'room_number' => '101',
                'check_in' => '2024-01-15',
                'check_out' => '2024-01-18',
                'status' => 'Confirmed',
                'total_amount' => 360.00
            ],
            [
                'id' => 2,
                'guest_name' => 'Jane Smith',
                'room_number' => '205',
                'check_in' => '2024-01-16',
                'check_out' => '2024-01-20',
                'status' => 'Checked In',
                'total_amount' => 720.00
            ]
        ];
        
        return view('reservations.index', compact('reservations'));
    }
    
    public function create()
    {
        return view('reservations.create');
    }
    
    public function store(Request $request)
    {
        return redirect()->route('reservations.index')->with('success', 'Reservation created successfully!');
    }
    
    public function show($id)
    {
        $reservation = [
            'id' => $id,
            'guest_name' => 'John Doe',
            'room_number' => '101',
            'check_in' => '2024-01-15',
            'check_out' => '2024-01-18',
            'status' => 'Confirmed',
            'total_amount' => 360.00
        ];
        
        return view('reservations.show', compact('reservation'));
    }
    
    public function edit($id)
    {
        $reservation = [
            'id' => $id,
            'guest_name' => 'John Doe',
            'room_number' => '101',
            'check_in' => '2024-01-15',
            'check_out' => '2024-01-18',
            'status' => 'Confirmed',
            'total_amount' => 360.00
        ];
        
        return view('reservations.edit', compact('reservation'));
    }
    
    public function update(Request $request, $id)
    {
        return redirect()->route('reservations.index')->with('success', 'Reservation updated successfully!');
    }
    
    public function destroy($id)
    {
        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully!');
    }
}