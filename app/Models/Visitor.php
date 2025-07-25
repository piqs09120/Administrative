<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact', 'purpose', 'facility_id', 'time_in', 'time_out', 'company', 'host_employee'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
