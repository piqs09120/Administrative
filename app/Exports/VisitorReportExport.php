<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitorReportExport implements FromArray, WithMultipleSheets, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [];
    }

    public function sheets(): array
    {
        $sheets = [];
        
        foreach ($this->data as $sheetName => $sheetData) {
            $sheets[] = new VisitorReportSheet($sheetName, $sheetData);
        }
        
        return $sheets;
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
            'A' => 20,
            'B' => 20,
            'C' => 15,
        ];
    }
}

class VisitorReportSheet implements FromArray, WithStyles, WithColumnWidths
{
    protected $sheetName;
    protected $data;

    public function __construct($sheetName, $data)
    {
        $this->sheetName = $sheetName;
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
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
            'A' => 20,
            'B' => 20,
            'C' => 15,
        ];
    }
}
