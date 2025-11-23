<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseEvidence extends Model
{
    use HasFactory;

    protected $table = 'case_evidence';

    protected $fillable = [
        'legal_case_id',
        'evidence_type',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'uploaded_by',
        'collected_at',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
    ];

    /**
     * Get the legal case this evidence belongs to
     */
    public function legalCase()
    {
        return $this->belongsTo(LegalCase::class);
    }

    /**
     * Get human-readable file size
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
