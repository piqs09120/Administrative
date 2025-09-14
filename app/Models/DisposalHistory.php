<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposalHistory extends Model
{
    use HasFactory;

    protected $table = 'disposal_history';

    protected $fillable = [
        'document_title',
        'document_description',
        'document_category',
        'document_department',
        'document_author',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'confidentiality_level',
        'retention_until',
        'retention_policy',
        'previous_status',
        'disposal_reason',
        'disposed_at',
        'disposed_by',
        'lifecycle_log',
        'ai_analysis',
        'metadata',
        'ip_address'
    ];

    protected $casts = [
        'retention_until' => 'date',
        'disposed_at' => 'datetime',
        'lifecycle_log' => 'array',
        'ai_analysis' => 'array',
        'metadata' => 'array',
        'file_size' => 'integer'
    ];

    /**
     * Get the user who disposed the document
     */
    public function disposer()
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return 'Unknown';
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get disposal reason display text
     */
    public function getDisposalReasonDisplayAttribute()
    {
        return match($this->disposal_reason) {
            'auto_expired' => 'Automatically Expired',
            'manually_disposed' => 'Manually Disposed',
            default => ucfirst(str_replace('_', ' ', $this->disposal_reason))
        };
    }

    /**
     * Get confidentiality level badge class
     */
    public function getConfidentialityBadgeClassAttribute()
    {
        return match($this->confidentiality_level) {
            'public' => 'badge-success',
            'internal' => 'badge-warning',
            'confidential' => 'badge-error',
            'restricted' => 'badge-error',
            default => 'badge-neutral'
        };
    }
}