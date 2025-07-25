<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        $guests = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@email.com',
                'phone' => '+1234567890',
                'address' => '123 Main St, City',
                'status' => 'Active',
                'total_stays' => 5
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@email.com',
                'phone' => '+0987654321',
                'address' => '456 Oak Ave, Town',
                'status' => 'VIP',
                'total_stays' => 12
            ]
        ];
        
        return view('guests.index', compact('guests'));
    }
    
    public function create()
    {
        return view('guests.create');
    }
    
    public function store(Request $request)
    {
        return redirect()->route('guests.index')->with('success', 'Guest created successfully!');
    }
    
    public function show($id)
    {
        $guest = [
            'id' => $id,
            'name' => 'John Doe',
            'email' => 'john.doe@email.com',
            'phone' => '+1234567890',
            'address' => '123 Main St, City',
            'status' => 'Active',
            'total_stays' => 5
        ];
        
        return view('guests.show', compact('guest'));
    }
    
    public function edit($id)
    {
        $guest = [
            'id' => $id,
            'name' => 'John Doe',
            'email' => 'john.doe@email.com',
            'phone' => '+1234567890',
            'address' => '123 Main St, City',
            'status' => 'Active',
            'total_stays' => 5
        ];
        
        return view('guests.edit', compact('guest'));
    }
    
    public function update(Request $request, $id)
    {
        return redirect()->route('guests.index')->with('success', 'Guest updated successfully!');
    }
    
    public function destroy($id)
    {
        return redirect()->route('guests.index')->with('success', 'Guest deleted successfully!');
    }
}