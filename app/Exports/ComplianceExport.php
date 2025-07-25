<?php

namespace App\Exports;

use App\Models\Compliance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ComplianceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Compliance::select('id', 'type', 'title', 'description', 'date', 'status', 'document_id')->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Type', 'Title', 'Description', 'Date', 'Status', 'Document ID'
        ];
    }
} 