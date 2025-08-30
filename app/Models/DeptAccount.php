<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DeptAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'department_accounts';
    protected $primaryKey = 'Dept_no';
  

    protected $fillable = [
        'Dept_no',
        'Dept_id',
        'dept_name',
        'employee_name',
        'employee_id',
        'role',
        'email',
        'status',
        'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the user's name (alias for employee_name)
     */
    public function getNameAttribute()
    {
        return $this->employee_name;
    }
}
