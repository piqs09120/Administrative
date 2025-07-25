<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role ?? null;
            if (!in_array($role, ['Administrator', 'Manager'])) {
                abort(403, 'Only Administrators and Managers can access staff management.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $staff = [
            [
                'id' => 1,
                'name' => 'Alice Johnson',
                'position' => 'Front Desk Manager',
                'department' => 'Reception',
                'email' => 'alice.johnson@hotel.com',
                'phone' => '+1234567890',
                'status' => 'Active',
                'hire_date' => '2023-03-15'
            ],
            [
                'id' => 2,
                'name' => 'Bob Wilson',
                'position' => 'Chef',
                'department' => 'Kitchen',
                'email' => 'bob.wilson@hotel.com',
                'phone' => '+0987654321',
                'status' => 'Active',
                'hire_date' => '2023-01-20'
            ]
        ];
        
        return view('staff.index', compact('staff'));
    }
    
    public function create()
    {
        return view('staff.create');
    }
    
    public function store(Request $request)
    {
        return redirect()->route('staff.index')->with('success', 'Staff member added successfully!');
    }
    
    public function show($id)
    {
        $staff = [
            'id' => $id,
            'name' => 'Alice Johnson',
            'position' => 'Front Desk Manager',
            'department' => 'Reception',
            'email' => 'alice.johnson@hotel.com',
            'phone' => '+1234567890',
            'status' => 'Active',
            'hire_date' => '2023-03-15'
        ];
        
        return view('staff.show', compact('staff'));
    }
    
    public function edit($id)
    {
        $staff = [
            'id' => $id,
            'name' => 'Alice Johnson',
            'position' => 'Front Desk Manager',
            'department' => 'Reception',
            'email' => 'alice.johnson@hotel.com',
            'phone' => '+1234567890',
            'status' => 'Active',
            'hire_date' => '2023-03-15'
        ];
        
        return view('staff.edit', compact('staff'));
    }
    
    public function update(Request $request, $id)
    {
        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully!');
    }
    
    public function destroy($id)
    {
        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully!');
    }
}