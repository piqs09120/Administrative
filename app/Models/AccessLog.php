<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'description', 'ip_address'
    ];

    /**
     * Get the user that owns the access log.
     */
    public function user()
    {
        return $this->belongsTo(DeptAccount::class, 'user_id', 'Dept_no');
    }

    /**
     * Ensure user_id always points to DeptAccount->Dept_no when creating logs.
     */
    protected static function booted(): void
    {
        static::creating(function (AccessLog $log) {
            // If ip_address column was dropped, ensure we don't try to insert it
            if (!Schema::hasColumn('access_logs', 'ip_address')) {
                unset($log->ip_address);
            }

            // If user_id is missing or does not correspond to an existing Dept_no,
            // attempt to resolve the current DeptAccount and use its Dept_no.
            $needsMapping = false;

            if (empty($log->user_id)) {
                $needsMapping = true;
            } else {
                $exists = DeptAccount::where('Dept_no', $log->user_id)->exists();
                if (!$exists) {
                    $needsMapping = true;
                }
            }

            if ($needsMapping) {
                $resolvedDeptNo = self::resolveCurrentDeptNo();
                if ($resolvedDeptNo !== null) {
                    $log->user_id = $resolvedDeptNo;
                } else {
                    // Ensure the insert does not fail if no DeptAccount mapping exists yet
                    $log->user_id = '0';
                }
            }
        });
    }

    /**
     * Resolve the current DeptAccount Dept_no from session or auth.
     */
    private static function resolveCurrentDeptNo(): ?int
    {
        try {
            $empId = session('emp_id');
            if ($empId) {
                $deptNo = optional(DeptAccount::where('employee_id', $empId)->first())->Dept_no;
                if ($deptNo) { return (int) $deptNo; }
            }

            if (Auth::check()) {
                $email = Auth::user()->email ?? '';
                $empFromEmail = $email ? strstr($email, '@', true) : null;
                if ($empFromEmail) {
                    $deptNo = optional(DeptAccount::where('employee_id', $empFromEmail)->first())->Dept_no;
                    if ($deptNo) { return (int) $deptNo; }
                }
            }
        } catch (\Throwable $e) {
            // Fail silently; logging should not break app flow
        }
        return null;
    }
} 