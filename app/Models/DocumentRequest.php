<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 'requested_by', 'approved_by', 'status', 'remarks'
    ];

    public function document() {
        return $this->belongsTo(Document::class);
    }

    public function requester() {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
