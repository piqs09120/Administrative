<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = [
            [
                'id' => 1,
                'name' => 'Grilled Salmon',
                'category' => 'Main Course',
                'price' => 28.99,
                'description' => 'Fresh Atlantic salmon with herbs',
                'status' => 'Available'
            ],
            [
                'id' => 2,
                'name' => 'Caesar Salad',
                'category' => 'Appetizer',
                'price' => 12.99,
                'description' => 'Classic Caesar with croutons',
                'status' => 'Available'
            ]
        ];
        
        return view('menu.index', compact('menuItems'));
    }
    
    public function create()
    {
        return view('menu.create');
    }
    
    public function store(Request $request)
    {
        return redirect()->route('menu.index')->with('success', 'Menu item created successfully!');
    }
    
    public function show($id)
    {
        $menuItem = [
            'id' => $id,
            'name' => 'Grilled Salmon',
            'category' => 'Main Course',
            'price' => 28.99,
            'description' => 'Fresh Atlantic salmon with herbs',
            'status' => 'Available'
        ];
        
        return view('menu.show', compact('menuItem'));
    }
    
    public function edit($id)
    {
        $menuItem = [
            'id' => $id,
            'name' => 'Grilled Salmon',
            'category' => 'Main Course',
            'price' => 28.99,
            'description' => 'Fresh Atlantic salmon with herbs',
            'status' => 'Available'
        ];
        
        return view('menu.edit', compact('menuItem'));
    }
    
    public function update(Request $request, $id)
    {
        return redirect()->route('menu.index')->with('success', 'Menu item updated successfully!');
    }
    
    public function destroy($id)
    {
        return redirect()->route('menu.index')->with('success', 'Menu item deleted successfully!');
    }
}