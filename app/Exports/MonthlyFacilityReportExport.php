<?php

namespace App\Exports;

use App\Models\FacilityReservation;
use App\Models\Facility;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class MonthlyFacilityReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $month;
    protected $year;
    protected $facilityId;

    public function __construct($month = null, $year = null, $facilityId = null)
    {
        $this->month = $month ?: now()->month;
        $this->year = $year ?: now()->year;
        $this->facilityId = $facilityId;
    }

    public function collection()
    {
        $query = FacilityReservation::with(['facility', 'reserver', 'approver'])
            ->whereMonth('start_time', $this->month)
            ->whereYear('start_time', $this->year);

        if ($this->facilityId) {
            $query->where('facility_id', $this->facilityId);
        }

        return $query->orderBy('start_time')->get();
    }

    public function headings(): array
    {
        return [
            'Reservation ID',
            'Facility Name',
            'Reserved By',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Duration (Hours)',
            'Purpose',
            'Status',
            'Approved By',
            'Approval Date',
            'Payment Status',
            'Payment Amount',
            'Created Date'
        ];
    }

    public function map($reservation): array
    {
        return [
            $reservation->id,
            $reservation->facility->name ?? 'N/A',
            $reservation->reserver->name ?? 'N/A',
            $reservation->start_time ? $reservation->start_time->format('Y-m-d') : 'N/A',
            $reservation->start_time ? $reservation->start_time->format('H:i') : 'N/A',
            $reservation->end_time ? $reservation->end_time->format('Y-m-d') : 'N/A',
            $reservation->end_time ? $reservation->end_time->format('H:i') : 'N/A',
            $reservation->start_time && $reservation->end_time ? 
                round($reservation->start_time->diffInHours($reservation->end_time), 2) : 0,
            $reservation->purpose ?? 'Not specified',
            ucfirst($reservation->status),
            $reservation->approver->name ?? 'N/A',
            $reservation->approved_by ? $reservation->updated_at->format('Y-m-d H:i') : 'N/A',
            ucfirst($reservation->payment_status ?? 'pending'),
            $reservation->payment_amount ?? 0,
            $reservation->created_at->format('Y-m-d H:i')
        ];
    }

    public function title(): string
    {
        $monthName = Carbon::createFromDate($this->year, $this->month)->format('F');
        return "Monthly Report - {$monthName} {$this->year}";
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Reservation ID
            'B' => 25,  // Facility Name
            'C' => 20,  // Reserved By
            'D' => 12,  // Start Date
            'E' => 10,  // Start Time
            'F' => 12,  // End Date
            'G' => 10,  // End Time
            'H' => 15,  // Duration
            'I' => 30,  // Purpose
            'J' => 12,  // Status
            'K' => 20,  // Approved By
            'L' => 18,  // Approval Date
            'M' => 15,  // Payment Status
            'N' => 15,  // Payment Amount
            'O' => 18,  // Created Date
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add borders to all cells with data
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                // Add summary section
                $summaryRow = $lastRow + 3;
                $sheet->setCellValue('A' . $summaryRow, 'MONTHLY SUMMARY');
                $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(14);
                
                $summaryRow++;
                $sheet->setCellValue('A' . $summaryRow, 'Total Reservations: ' . $lastRow);
                $summaryRow++;
                $sheet->setCellValue('A' . $summaryRow, 'Report Generated: ' . now()->format('Y-m-d H:i:s'));
                
                // Auto-fit columns
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
