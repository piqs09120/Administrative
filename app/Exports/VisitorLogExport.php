<?php

namespace App\Exports;

use App\Models\Visitor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class VisitorLogExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $visitors;

    public function __construct($visitors)
    {
        $this->visitors = $visitors;
    }

    public function collection()
    {
        return $this->visitors;
    }

    public function headings(): array
    {
        return [
            'Visitor Name',
            'Company',
            'Purpose',
            'Facility',
            'Check In Time',
            'Check Out Time',
            'Duration',
            'Host Employee',
            'Contact',
            'Created At'
        ];
    }

    public function map($visitor): array
    {
        $duration = 'N/A';
        if ($visitor->time_out) {
            $checkIn = Carbon::parse($visitor->time_in);
            $checkOut = Carbon::parse($visitor->time_out);
            $duration = $checkIn->diffForHumans($checkOut, true);
        } elseif ($visitor->time_in) {
            $checkIn = Carbon::parse($visitor->time_in);
            $duration = $checkIn->diffForHumans(now(), true) . ' (ongoing)';
        }

        return [
            $visitor->name,
            $visitor->company ?? 'N/A',
            $visitor->purpose ?? 'N/A',
            $visitor->facility->name ?? 'N/A',
            $visitor->time_in ? Carbon::parse($visitor->time_in)->format('Y-m-d H:i:s') : 'N/A',
            $visitor->time_out ? Carbon::parse($visitor->time_out)->format('Y-m-d H:i:s') : 'Still in building',
            $duration,
            $visitor->host_employee ?? 'N/A',
            $visitor->contact ?? 'N/A',
            $visitor->created_at ? Carbon::parse($visitor->created_at)->format('Y-m-d H:i:s') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Visitor Name
            'B' => 20, // Company
            'C' => 15, // Purpose
            'D' => 20, // Facility
            'E' => 20, // Check In Time
            'F' => 20, // Check Out Time
            'G' => 15, // Duration
            'H' => 20, // Host Employee
            'I' => 20, // Contact
            'J' => 20, // Created At
        ];
    }
}
