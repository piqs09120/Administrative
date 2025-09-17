<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'description', 'status', 'capacity', 'amenities', 'rating', 'facility_type', 'images', 'hourly_rate', 'operating_hours_start', 'operating_hours_end'
    ];

    // Ensure computed cover image URL is always available on arrays/JSON
    protected $appends = ['cover_url'];

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
        // Public path using facility name only (preferred behavior)
        // public/facilities/{Facility Name}/cover.{ext}
        $nameSlug = $this->name ? Str::slug($this->name, '-') : (string) $this->id;
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $publicRel = 'facilities/' . $nameSlug . '/cover.' . $ext;
            $publicAbs = public_path($publicRel);
            if (file_exists($publicAbs)) {
                $ver = filemtime($publicAbs);
                return asset($publicRel) . '?v=' . $ver;
            }
        }

        // Return placeholder if no cover image found (with facility ID for uniqueness)
        return asset('images/placeholder-facility.jpg') . '?facility=' . $this->id;
    }
}
