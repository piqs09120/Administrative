<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class VisitorReportExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $exportData = [];
        
        // Add statistics summary
        if ($this->data['include_statistics']) {
            $exportData[] = ['VISITOR REPORT SUMMARY'];
            $exportData[] = ['Generated At', $this->data['generated_at']->format('Y-m-d H:i:s')];
            $exportData[] = ['Date Range', $this->data['date_range']['start']->format('Y-m-d') . ' to ' . $this->data['date_range']['end']->format('Y-m-d')];
            $exportData[] = ['Total Visitors', $this->data['statistics']['total_visitors']];
            $exportData[] = ['Currently In Building', $this->data['statistics']['currently_in']];
            $exportData[] = ['Completed Visits', $this->data['statistics']['completed_visits']];
            $exportData[] = ['Average Duration', $this->data['statistics']['average_duration']];
            $exportData[] = []; // Empty row
        }
        
        // Add visitor details
        if ($this->data['include_details']) {
            $exportData[] = ['VISITOR DETAILS'];
            $exportData[] = []; // Empty row
            
            foreach ($this->data['visitors'] as $visitor) {
                $duration = 'N/A';
                if ($visitor->time_out) {
                    $checkIn = Carbon::parse($visitor->time_in);
                    $checkOut = Carbon::parse($visitor->time_out);
                    $duration = $checkIn->diffForHumans($checkOut, true);
                } elseif ($visitor->time_in) {
                    $checkIn = Carbon::parse($visitor->time_in);
                    $duration = $checkIn->diffForHumans(now(), true) . ' (ongoing)';
                }

                $exportData[] = [
                    $visitor->name,
                    $visitor->company ?? 'N/A',
                    $visitor->purpose ?? 'N/A',
                    $visitor->facility->name ?? 'N/A',
                    $visitor->time_in ? Carbon::parse($visitor->time_in)->format('Y-m-d H:i:s') : 'N/A',
                    $visitor->time_out ? Carbon::parse($visitor->time_out)->format('Y-m-d H:i:s') : 'Still in building',
                    $duration,
                    $visitor->host_employee ?? 'N/A',
                    $visitor->contact ?? 'N/A'
                ];
            }
        }
        
        return $exportData;
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
            'Contact'
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
        ];
    }

    public function title(): string
    {
        return 'Visitor Report';
    }
}
