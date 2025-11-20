<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConsent extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'policy_id', 'version', 'ip_address', 'user_agent', 'accepted_at'];

    protected $casts = [
        'accepted_at' => 'datetime',
        'version' => 'integer',
    ];

    /**
     * Get the user who gave consent
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the policy that was consented to
     */
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }
}





