<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class VisitorQrPass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'visitor_id',
        'verification_log_id',
        'pass_code',
        'qr_code_path',
        'qr_data',
        'valid_from',
        'valid_until',
        'is_active',
        'status',
        'scanned_at',
        'scanned_by',
        'scan_ip',
        'scan_location',
        'scan_count',
        'scan_history',
        'hmac_signature',
        'revoked',
        'revoked_at',
        'revoked_by',
        'revocation_reason',
        'email_sent',
        'email_sent_at',
        'email_send_attempts',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'scanned_at' => 'datetime',
        'scan_history' => 'array',
        'revoked' => 'boolean',
        'revoked_at' => 'datetime',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];

    // Relationships
    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function verificationLog()
    {
        return $this->belongsTo(IdVerificationLog::class, 'verification_log_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('revoked', false)
                    ->where('status', 'active')
                    ->where('valid_from', '<=', now())
                    ->where('valid_until', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now())
                    ->where('status', '!=', 'expired');
    }

    // Accessors
    public function getIsValidAttribute()
    {
        return $this->is_active 
            && !$this->revoked 
            && $this->status === 'active'
            && $this->valid_from <= now()
            && $this->valid_until >= now();
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
    }

    // Methods
    public static function generatePassCode(): string
    {
        do {
            $code = strtoupper(Str::random(3) . '-' . rand(1000, 9999) . '-' . Str::random(3));
        } while (self::where('pass_code', $code)->exists());

        return $code;
    }

    public function generateQrCode(): string
    {
        $qrData = [
            'pass_code' => $this->pass_code,
            'visitor_id' => $this->visitor_id,
            'visitor_name' => $this->visitor->name ?? 'Unknown',
            'valid_from' => $this->valid_from->toDateTimeString(),
            'valid_until' => $this->valid_until->toDateTimeString(),
            'signature' => $this->hmac_signature,
        ];

        $qrDataString = json_encode($qrData);
        $this->qr_data = encrypt($qrDataString);
        
        // Generate QR code image
        $qrCode = QrCode::format('png')
                       ->size(400)
                       ->errorCorrection('H')
                       ->generate($qrDataString);

        // Save QR code
        $filename = 'qr-passes/' . $this->pass_code . '.png';
        Storage::disk('public')->put($filename, $qrCode);
        
        $this->qr_code_path = $filename;
        $this->save();

        return $filename;
    }

    public function recordScan(string $scannedBy = null, string $location = null): bool
    {
        if (!$this->is_valid) {
            return false;
        }

        $scanHistory = $this->scan_history ?? [];
        $scanHistory[] = [
            'timestamp' => now()->toDateTimeString(),
            'scanned_by' => $scannedBy,
            'location' => $location,
            'ip' => request()->ip(),
        ];

        $this->update([
            'scanned_at' => now(),
            'scanned_by' => $scannedBy,
            'scan_location' => $location,
            'scan_ip' => request()->ip(),
            'scan_count' => $this->scan_count + 1,
            'scan_history' => $scanHistory,
            'status' => 'used',
        ]);

        return true;
    }

    public function revoke(string $reason, string $revokedBy = null): void
    {
        $this->update([
            'revoked' => true,
            'revoked_at' => now(),
            'revoked_by' => $revokedBy ?? auth()->user()->name ?? 'System',
            'revocation_reason' => $reason,
            'is_active' => false,
            'status' => 'revoked',
        ]);
    }

    public function markExpired(): void
    {
        if ($this->valid_until < now()) {
            $this->update([
                'status' => 'expired',
                'is_active' => false,
            ]);
        }
    }
}



