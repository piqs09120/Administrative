<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_reservation_id',
        'task_type',
        'status',
        'assigned_to_module',
        'details',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'details' => 'array',
        'completed_at' => 'datetime',
    ];

    public function facilityReservation()
    {
        return $this->belongsTo(FacilityReservation::class);
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}


