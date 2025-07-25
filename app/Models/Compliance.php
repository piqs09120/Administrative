<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'title', 'description', 'date', 'status', 'document_id'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
