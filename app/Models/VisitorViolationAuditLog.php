<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorViolationAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'violation_id',
        'action',
        'actor_id',
        'actor_name',
        'old_status',
        'new_status',
        'notes',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function violation()
    {
        return $this->belongsTo(VisitorViolation::class);
    }
}



