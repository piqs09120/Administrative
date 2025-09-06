<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Get the cover image URL for this facility
     * Ensures each facility gets its own unique image URL with cache busting
     */
    public function getCoverUrlAttribute()
    {
        // Check for cover image in facility-specific directory
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $storageRel = 'facilities/' . $this->id . '/cover.' . $ext;
            if (Storage::disk('public')->exists($storageRel)) {
                $abs = storage_path('app/public/' . $storageRel);
                $ver = file_exists($abs) ? filemtime($abs) : $this->updated_at->timestamp;
                return asset('storage/' . $storageRel) . '?v=' . $ver;
            }
        }

        // Return placeholder if no cover image found (with facility ID for uniqueness)
        return asset('images/placeholder-facility.jpg') . '?facility=' . $this->id;
    }
}
