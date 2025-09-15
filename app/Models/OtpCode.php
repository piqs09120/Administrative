<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'email',
        'otp_code',
        'expires_at',
        'is_used',
        'ip_address'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    /**
     * Check if OTP is valid and not expired
     */
    public function isValid()
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed()
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Generate a 6-digit OTP code
     */
    public static function generateCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create OTP for employee
     */
    public static function createForEmployee($employeeId, $email, $ipAddress = null)
    {
        // Invalidate any existing OTPs for this employee
        self::where('employee_id', $employeeId)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Create new OTP
        return self::create([
            'employee_id' => $employeeId,
            'email' => $email,
            'otp_code' => self::generateCode(),
            'expires_at' => Carbon::now()->addMinutes(10), // 10 minutes expiry
            'ip_address' => $ipAddress
        ]);
    }

    /**
     * Verify OTP code
     */
    public static function verify($employeeId, $otpCode)
    {
        $otp = self::where('employee_id', $employeeId)
            ->where('otp_code', $otpCode)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otp) {
            $otp->markAsUsed();
            return true;
        }

        return false;
    }

    /**
     * Clean up expired OTPs
     */
    public static function cleanupExpired()
    {
        return self::where('expires_at', '<', Carbon::now())->delete();
    }
}
