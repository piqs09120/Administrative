<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'version',
        'change_notes',
        'content_snapshot',
        'attachments_snapshot',
        'editor_id',
    ];

    protected $casts = [
        'attachments_snapshot' => 'array',
    ];

    public function policy()
    {
        return $this->belongsTo(CompanyPolicy::class, 'policy_id');
    }
}


