<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
} 