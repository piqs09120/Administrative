<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'reserved_by', 'start_time', 'end_time', 'purpose', 'status', 'approved_by', 'remarks'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function reserver()
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
