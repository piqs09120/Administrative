<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role ?? null;
            if (!in_array($role, ['administrator', 'Manager', 'Staff'])) {
                abort(403, 'Only Administrators, Managers, and Staff can access inventory management.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $inventory = [
            [
                'id' => 1,
                'item_name' => 'Fresh Salmon',
                'category' => 'Seafood',
                'quantity' => 25,
                'unit' => 'kg',
                'cost_per_unit' => 15.50,
                'supplier' => 'Ocean Fresh Co.',
                'expiry_date' => '2024-01-15',
                'status' => 'In Stock'
            ],
            [
                'id' => 2,
                'item_name' => 'Bed Sheets',
                'category' => 'Linens',
                'quantity' => 150,
                'unit' => 'pieces',
                'cost_per_unit' => 25.00,
                'supplier' => 'Hotel Supplies Inc.',
                'expiry_date' => null,
                'status' => 'In Stock'
            ],
            [
                'id' => 3,
                'item_name' => 'Cleaning Supplies',
                'category' => 'Maintenance',
                'quantity' => 5,
                'unit' => 'bottles',
                'cost_per_unit' => 8.99,
                'supplier' => 'Clean Pro',
                'expiry_date' => '2024-12-31',
                'status' => 'Low Stock'
            ]
        ];
        
        return view('inventory.index', compact('inventory'));
    }
    
    public function create()
    {
        return view('inventory.create');
    }
    
    public function store(Request $request)
    {
        return redirect()->route('inventory.index')->with('success', 'Inventory item added successfully!');
    }
    
    public function show($id)
    {
        $item = [
            'id' => $id,
            'item_name' => 'Fresh Salmon',
            'category' => 'Seafood',
            'quantity' => 25,
            'unit' => 'kg',
            'cost_per_unit' => 15.50,
            'supplier' => 'Ocean Fresh Co.',
            'expiry_date' => '2024-01-15',
            'status' => 'In Stock'
        ];
        
        return view('inventory.show', compact('item'));
    }
    
    public function edit($id)
    {
        $item = [
            'id' => $id,
            'item_name' => 'Fresh Salmon',
            'category' => 'Seafood',
            'quantity' => 25,
            'unit' => 'kg',
            'cost_per_unit' => 15.50,
            'supplier' => 'Ocean Fresh Co.',
            'expiry_date' => '2024-01-15',
            'status' => 'In Stock'
        ];
        
        return view('inventory.edit', compact('item'));
    }
    
    public function update(Request $request, $id)
    {
        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully!');
    }
    
    public function destroy($id)
    {
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully!');
    }
}