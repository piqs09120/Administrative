<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VisitorCheckinLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'checked_in_by',
        'action',
        'notes',
        'visitor_data',
        'action_time'
    ];

    protected $casts = [
        'visitor_data' => 'array',
        'action_time' => 'datetime'
    ];

    // Relationships
    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    // Scopes
    public function scopeCheckins($query)
    {
        return $query->where('action', 'checkin');
    }

    public function scopeCheckouts($query)
    {
        return $query->where('action', 'checkout');
    }

    public function scopeRegistrations($query)
    {
        return $query->where('action', 'register');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('action_time', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('action_time', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('action_time', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    // Helper methods
    public function getActionIconAttribute()
    {
        return match($this->action) {
            'checkin' => 'user-plus',
            'checkout' => 'user-minus',
            'register' => 'user-check',
            default => 'user'
        };
    }

    public function getActionColorAttribute()
    {
        return match($this->action) {
            'checkin' => 'text-green-600',
            'checkout' => 'text-red-600',
            'register' => 'text-blue-600',
            default => 'text-gray-600'
        };
    }

    public function getFormattedActionTimeAttribute()
    {
        return $this->action_time->format('M d, Y H:i:s');
    }
}
