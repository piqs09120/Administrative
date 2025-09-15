<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Visitor extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'contact', 'purpose', 'facility_id', 'time_in', 'time_out', 'company', 'department', 'host_employee', 'facility_reservation_id',
        'pass_type', 'pass_validity', 'pass_valid_from', 'pass_valid_until', 'access_level', 'escort_required',
        'special_instructions', 'generate_digital_pass', 'pass_id', 'pass_data', 'id_type', 'id_number', 'vehicle_plate', 'status', 'expected_time_out', 'expected_date_out', 'arrival_date', 'arrival_time', 'pending_exit', 'pending_exit_at'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function facilityReservation()
    {
        return $this->belongsTo(FacilityReservation::class);
    }

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'pass_valid_from' => 'datetime',
        'pass_valid_until' => 'datetime',
        'pass_data' => 'array',
        'expected_time_out' => 'datetime',
        'expected_date_out' => 'date',
        'arrival_date' => 'date',
        'arrival_time' => 'datetime',
        'pending_exit' => 'boolean',
        'pending_exit_at' => 'datetime',
    ];
}
