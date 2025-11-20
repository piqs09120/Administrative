<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'title', 'content', 'version', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    /**
     * Get all consents for this policy
     */
    public function consents()
    {
        return $this->hasMany(UserConsent::class);
    }
}





