<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'area', 'usage', 'cost', 'recorded_at'
    ];
} 