<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinanceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::select('id', 'user_id', 'table_number', 'total_amount', 'status', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'User ID', 'Table Number', 'Total Amount', 'Status', 'Created At'
        ];
    }
} 