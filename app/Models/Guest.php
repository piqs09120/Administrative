<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guest extends Authenticatable
{
    use Notifiable;

    protected $table = 'core1_guest';
    protected $primaryKey = 'id';

    protected $fillable = [
        'guest_name',
        'guest_email',
        'guest_address',
        'guest_mobile',
        'guest_password',
        'guest_birthday',
        'guest_photo',
    ];

    protected $hidden = [
        'guest_password',
        'remember_token',
    ];

    protected $casts = [
        'guest_birthday' => 'date',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->guest_password;
    }
}
