<?php

namespace App\Exports;

use App\Models\Visitor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VisitorExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Visitor::select('id', 'name', 'contact', 'purpose', 'facility_id', 'time_in', 'time_out', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Contact', 'Purpose', 'Facility ID', 'Time In', 'Time Out', 'Created At'
        ];
    }
} 