<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = [
            [
                'id' => 1,
                'number' => 1,
                'capacity' => 4,
                'status' => 'Available',
                'location' => 'Main Dining'
            ],
            [
                'id' => 2,
                'number' => 5,
                'capacity' => 6,
                'status' => 'Occupied',
                'location' => 'Main Dining'
            ],
            [
                'id' => 3,
                'number' => 12,
                'capacity' => 2,
                'status' => 'Reserved',
                'location' => 'Terrace'
            ]
        ];
        
        return view('tables.index', compact('tables'));
    }
    
    public function create()
    {
        return view('tables.create');
    }
    
    public function store(Request $request)
    {
        return redirect()->route('tables.index')->with('success', 'Table created successfully!');
    }
    
    public function show($id)
    {
        $table = [
            'id' => $id,
            'number' => 1,
            'capacity' => 4,
            'status' => 'Available',
            'location' => 'Main Dining'
        ];
        
        return view('tables.show', compact('table'));
    }
    
    public function edit($id)
    {
        $table = [
            'id' => $id,
            'number' => 1,
            'capacity' => 4,
            'status' => 'Available',
            'location' => 'Main Dining'
        ];
        
        return view('tables.edit', compact('table'));
    }
    
    public function update(Request $request, $id)
    {
        return redirect()->route('tables.index')->with('success', 'Table updated successfully!');
    }
    
    public function destroy($id)
    {
        return redirect()->route('tables.index')->with('success', 'Table deleted successfully!');
    }
}