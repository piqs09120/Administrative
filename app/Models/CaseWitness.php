<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseWitness extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_case_id',
        'witness_name',
        'witness_department',
        'witness_position',
        'witness_contact',
        'witness_email',
        'statement',
        'statement_date',
        'statement_type',
    ];

    protected $casts = [
        'statement_date' => 'datetime',
    ];

    /**
     * Get the legal case this witness belongs to
     */
    public function legalCase()
    {
        return $this->belongsTo(LegalCase::class);
    }
}
