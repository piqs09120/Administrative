<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_type',
        'department', 
        'priority',
        'location',
        'facility_id',
        'requested_datetime',
        'requested_end_datetime',
        'description',
        'contact_name',
        'contact_email',
        'status',
        'notes',
        'assigned_to'
    ];

    protected $casts = [
        'requested_datetime' => 'datetime',
        'requested_end_datetime' => 'datetime',
        'notes' => 'array',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
