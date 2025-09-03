<?php

namespace App\Exports;

use App\Models\DeptAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    protected $accounts;

    public function __construct($accounts)
    {
        $this->accounts = $accounts;
    }

    public function collection()
    {
        return $this->accounts;
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Email',
            'Department',
            'Role',
            'Status',
            'Phone',
            'Created At',
            'Last Login'
        ];
    }

    public function map($account): array
    {
        return [
            $account->employee_id,
            $account->employee_name,
            $account->email ?? '—',
            $account->dept_name,
            $account->role,
            ucfirst($account->status),
            $account->phone ?? '—',
            $account->created_at ? $account->created_at->format('Y-m-d H:i:s') : '—',
            $account->last_login ?? '—',
        ];
    }
}
