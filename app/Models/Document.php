<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'department', 'author', 'file_path', 'status', 'uploaded_by', 'ai_analysis', 'category', 'source'
    ];

    protected $casts = [
        'ai_analysis' => 'array'
    ];

    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function documentRequests() {
        return $this->hasMany(DocumentRequest::class);
    }
}
