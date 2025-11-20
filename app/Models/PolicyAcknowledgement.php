<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyAcknowledgement extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'user_id',
        'role',
        'required_by',
        'acknowledged_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'required_by' => 'date',
        'acknowledged_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function policy()
    {
        return $this->belongsTo(CompanyPolicy::class, 'policy_id');
    }
}


