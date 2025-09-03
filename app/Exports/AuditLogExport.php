<?php

namespace App\Exports;

use App\Models\AccessLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditLogExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'DEPARTMENT',
            'EMPLOYEE',
            'MODULES',
            'ACTION',
            'ACTIVITY',
            'DATE',
        ];
    }

    public function map($log): array
    {
        // Map modules based on action
        $moduleMap = [
            'Table added' => 'Table Management',
            'Login' => 'Authentication',
            'Logout' => 'Authentication',
            'Document_uploaded' => 'Document Management',
            'Access_control_check' => 'Security',
            'Profile_updated' => 'User Management'
        ];
        $module = $moduleMap[$log->action] ?? 'System';

        return [
            $log->id,
            $log->user->dept_name ?? 'Soliera Restaurant',
            $log->user->employee_name ?? 'Unknown User',
            $module,
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
            'B' => 20,  // DEPARTMENT
            'C' => 25,  // EMPLOYEE
            'D' => 20,  // MODULES
            'E' => 20,  // ACTION
            'F' => 40,  // ACTIVITY
            'G' => 20,  // DATE
        ];
    }
}
