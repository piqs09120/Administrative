<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'description', 'status', 'capacity', 'amenities', 'rating', 'facility_type', 'images', 'hourly_rate', 'operating_hours_start', 'operating_hours_end'
    ];

    protected $casts = [
        'images' => 'array',
        'rating' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'operating_hours_start' => 'datetime:H:i',
        'operating_hours_end' => 'datetime:H:i',
    ];

    public function reservations()
    {
        return $this->hasMany(FacilityReservation::class);
    }
}
