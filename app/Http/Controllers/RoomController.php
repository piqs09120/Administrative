<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        // Mock room data - in real implementation, this would come from database
        $rooms = [
            [
                'id' => 101,
                'number' => '101',
                'type' => 'Standard',
                'status' => 'Available',
                'price' => 120.00,
                'floor' => 1,
                'amenities' => ['WiFi', 'AC', 'TV'],
                'last_cleaned' => '2024-01-10 10:30:00'
            ],
            [
                'id' => 102,
                'number' => '102',
                'type' => 'Standard',
                'status' => 'Occupied',
                'price' => 120.00,
                'floor' => 1,
                'amenities' => ['WiFi', 'AC', 'TV'],
                'last_cleaned' => '2024-01-09 14:15:00'
            ],
            [
                'id' => 201,
                'number' => '201',
                'type' => 'Deluxe',
                'status' => 'Maintenance',
                'price' => 180.00,
                'floor' => 2,
                'amenities' => ['WiFi', 'AC', 'TV', 'Mini Bar', 'Balcony'],
                'last_cleaned' => '2024-01-08 09:00:00'
            ],
            [
                'id' => 301,
                'number' => '301',
                'type' => 'Suite',
                'status' => 'Available',
                'price' => 350.00,
                'floor' => 3,
                'amenities' => ['WiFi', 'AC', 'TV', 'Mini Bar', 'Balcony', 'Jacuzzi', 'Living Room'],
                'last_cleaned' => '2024-01-10 11:45:00'
            ]
        ];
        
        return view('rooms.index', compact('rooms'));
    }
    
    public function create()
    {
        return view('rooms.create');
    }
    
    public function store(Request $request)
    {
        // Validation and storage logic would go here
        return redirect()->route('rooms.index')->with('success', 'Room created successfully!');
    }
    
    public function show($id)
    {
        // Mock room data - would fetch from database
        $room = [
            'id' => $id,
            'number' => '101',
            'type' => 'Standard',
            'status' => 'Available',
            'price' => 120.00,
            'floor' => 1,
            'amenities' => ['WiFi', 'AC', 'TV'],
            'last_cleaned' => '2024-01-10 10:30:00',
            'description' => 'Comfortable standard room with modern amenities and city view.'
        ];
        
        return view('rooms.show', compact('room'));
    }
    
    public function edit($id)
    {
        // Mock room data - would fetch from database
        $room = [
            'id' => $id,
            'number' => '101',
            'type' => 'Standard',
            'status' => 'Available',
            'price' => 120.00,
            'floor' => 1,
            'amenities' => ['WiFi', 'AC', 'TV'],
            'description' => 'Comfortable standard room with modern amenities and city view.'
        ];
        
        return view('rooms.edit', compact('room'));
    }
    
    public function update(Request $request, $id)
    {
        // Validation and update logic would go here
        return redirect()->route('rooms.index')->with('success', 'Room updated successfully!');
    }
    
    public function destroy($id)
    {
        // Delete logic would go here
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully!');
    }
}