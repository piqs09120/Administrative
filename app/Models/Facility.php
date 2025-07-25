<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'description', 'status'
    ];

    public function reservations()
    {
        return $this->hasMany(FacilityReservation::class);
    }
}
