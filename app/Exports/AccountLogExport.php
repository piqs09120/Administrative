<?php

namespace App\Exports;

use App\Models\AccessLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountLogExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $logs;

    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    public function collection()
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return [
            'LOG ID',
            'USER',
            'ACTION',
            'DESCRIPTION',
            'DATE',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->user->employee_name ?? 'Unknown User',
            $log->action,
            $log->description,
            $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i:s') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // LOG ID
            'B' => 25,  // USER
            'C' => 20,  // ACTION
            'D' => 40,  // DESCRIPTION
            'E' => 20,  // DATE
        ];
    }
}
