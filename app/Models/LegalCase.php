<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'title',
        'status', // open|closed
        'risk_score',
        'requires_legal_review',
        'memo',
        'created_by',
    ];

    protected $casts = [
        'requires_legal_review' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}


