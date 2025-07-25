<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinanceExport;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role ?? null;
            if (!in_array($role, ['Administrator', 'Manager'])) {
                abort(403, 'Only Administrators and Managers can access financial reports.');
            }
            return $next($request);
        })->only(['billing', 'payments', 'reports', 'exportExcel', 'exportPdf']);
    }

    public function billing()
    {
        $bills = [
            [
                'id' => 1,
                'guest_name' => 'John Doe',
                'room_number' => '101',
                'check_in' => '2024-01-15',
                'check_out' => '2024-01-18',
                'room_charges' => 360.00,
                'restaurant_charges' => 85.50,
                'other_charges' => 25.00,
                'total_amount' => 470.50,
                'status' => 'Paid'
            ],
            [
                'id' => 2,
                'guest_name' => 'Jane Smith',
                'room_number' => '205',
                'check_in' => '2024-01-16',
                'check_out' => '2024-01-20',
                'room_charges' => 720.00,
                'restaurant_charges' => 125.75,
                'other_charges' => 15.00,
                'total_amount' => 860.75,
                'status' => 'Pending'
            ]
        ];
        
        return view('finance.billing', compact('bills'));
    }
    
    public function payments()
    {
        $payments = [
            [
                'id' => 1,
                'bill_id' => 1,
                'guest_name' => 'John Doe',
                'amount' => 470.50,
                'payment_method' => 'Credit Card',
                'payment_date' => '2024-01-18',
                'status' => 'Completed',
                'transaction_id' => 'TXN123456789'
            ],
            [
                'id' => 2,
                'bill_id' => 2,
                'guest_name' => 'Jane Smith',
                'amount' => 860.75,
                'payment_method' => 'Cash',
                'payment_date' => '2024-01-20',
                'status' => 'Pending',
                'transaction_id' => null
            ]
        ];
        
        return view('finance.payments', compact('payments'));
    }
    
    public function reports()
    {
        $reports = [
            'daily_revenue' => 2450.75,
            'monthly_revenue' => 68500.25,
            'yearly_revenue' => 785000.00,
            'room_revenue' => 45000.00,
            'restaurant_revenue' => 23500.25,
            'outstanding_payments' => 5250.50,
            'monthly_expenses' => 25000.00
        ];
        
        return view('finance.reports', compact('reports'));
    }

    public function exportExcel()
    {
        return Excel::download(new FinanceExport, 'financial_report.xlsx');
    }

    public function exportPdf()
    {
        $billings = \App\Models\Order::latest()->get();
        $pdf = Pdf::loadView('finance.export_pdf', compact('billings'));
        return $pdf->download('financial_report.pdf');
    }
}