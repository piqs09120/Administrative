<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact', 'purpose', 'facility_id', 'time_in', 'time_out', 'company', 'host_employee', 'facility_reservation_id'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function facilityReservation()
    {
        return $this->belongsTo(FacilityReservation::class);
    }
}
