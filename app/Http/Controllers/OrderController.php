<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = [
            [
                'id' => 1,
                'table_number' => 5,
                'customer_name' => 'John Doe',
                'items' => ['Grilled Salmon', 'Caesar Salad'],
                'total_amount' => 41.98,
                'status' => 'Preparing',
                'order_time' => '2024-01-10 19:30:00'
            ],
            [
                'id' => 2,
                'table_number' => 12,
                'customer_name' => 'Jane Smith',
                'items' => ['Pasta Carbonara', 'Wine'],
                'total_amount' => 35.50,
                'status' => 'Served',
                'order_time' => '2024-01-10 20:15:00'
            ]
        ];
        
        return view('orders.index', compact('orders'));
    }
    
    public function create()
    {
        return view('orders.create');
    }
    
    public function store(Request $request)
    {
        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }
    
    public function show($id)
    {
        $order = [
            'id' => $id,
            'table_number' => 5,
            'customer_name' => 'John Doe',
            'items' => ['Grilled Salmon', 'Caesar Salad'],
            'total_amount' => 41.98,
            'status' => 'Preparing',
            'order_time' => '2024-01-10 19:30:00'
        ];
        
        return view('orders.show', compact('order'));
    }
    
    public function edit($id)
    {
        $order = [
            'id' => $id,
            'table_number' => 5,
            'customer_name' => 'John Doe',
            'items' => ['Grilled Salmon', 'Caesar Salad'],
            'total_amount' => 41.98,
            'status' => 'Preparing',
            'order_time' => '2024-01-10 19:30:00'
        ];
        
        return view('orders.edit', compact('order'));
    }
    
    public function update(Request $request, $id)
    {
        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }
    
    public function destroy($id)
    {
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }
}