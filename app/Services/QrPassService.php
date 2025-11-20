<?php

namespace App\Services;

use App\Models\Visitor;
use App\Models\VisitorQrPass;
use App\Models\IdVerificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorQrPassMail;
use Carbon\Carbon;

class QrPassService
{
    /**
     * Generate QR Pass for approved visitor
     */
    public function generateQrPass(Visitor $visitor, IdVerificationLog $verificationLog = null): VisitorQrPass
    {
        try {
            // Generate unique pass code
            $passCode = VisitorQrPass::generatePassCode();

            // Determine validity period
            $validFrom = $visitor->arrival_date 
                ? Carbon::parse($visitor->arrival_date . ' ' . ($visitor->arrival_time ?? '00:00'))
                : now();

            $validUntil = $visitor->expected_date_out 
                ? Carbon::parse($visitor->expected_date_out . ' ' . ($visitor->expected_time_out ?? '23:59'))
                : $validFrom->copy()->addDays(1);

            // Generate HMAC signature for tamper detection
            $signature = $this->generateSignature($visitor, $passCode, $validFrom, $validUntil);

            // Create QR Pass
            $qrPass = VisitorQrPass::create([
                'visitor_id' => $visitor->id,
                'verification_log_id' => $verificationLog?->id,
                'pass_code' => $passCode,
                'valid_from' => $validFrom,
                'valid_until' => $validUntil,
                'is_active' => true,
                'status' => 'active',
                'hmac_signature' => $signature,
            ]);

            // Generate QR code image
            $qrPass->generateQrCode();

            // Log generation
            Log::info('QR Pass Generated', [
                'pass_code' => $passCode,
                'visitor_id' => $visitor->id,
                'visitor_name' => $visitor->name,
                'valid_from' => $validFrom->toDateTimeString(),
                'valid_until' => $validUntil->toDateTimeString(),
            ]);

            return $qrPass;

        } catch (\Exception $e) {
            Log::error('QR Pass Generation Error', [
                'error' => $e->getMessage(),
                'visitor_id' => $visitor->id,
            ]);
            throw $e;
        }
    }

    /**
     * Generate HMAC signature for pass
     */
    protected function generateSignature(Visitor $visitor, string $passCode, Carbon $validFrom, Carbon $validUntil): string
    {
        $data = implode('|', [
            $visitor->id,
            $visitor->name,
            $visitor->email,
            $passCode,
            $validFrom->toDateTimeString(),
            $validUntil->toDateTimeString(),
        ]);

        return hash_hmac('sha256', $data, config('app.key'));
    }

    /**
     * Verify pass signature
     */
    public function verifySignature(VisitorQrPass $pass): bool
    {
        $expectedSignature = $this->generateSignature(
            $pass->visitor,
            $pass->pass_code,
            $pass->valid_from,
            $pass->valid_until
        );

        return hash_equals($expectedSignature, $pass->hmac_signature);
    }

    /**
     * Send QR Pass via email
     */
    public function sendQrPassEmail(VisitorQrPass $qrPass): bool
    {
        try {
            $visitor = $qrPass->visitor;

            if (!$visitor->email) {
                Log::warning('Cannot send QR Pass: No email address', ['visitor_id' => $visitor->id]);
                return false;
            }

            Mail::to($visitor->email)->send(new VisitorQrPassMail($qrPass));

            $qrPass->update([
                'email_sent' => true,
                'email_sent_at' => now(),
                'email_send_attempts' => $qrPass->email_send_attempts + 1,
            ]);

            Log::info('QR Pass Email Sent', [
                'pass_code' => $qrPass->pass_code,
                'visitor_email' => $visitor->email,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('QR Pass Email Error', [
                'error' => $e->getMessage(),
                'pass_code' => $qrPass->pass_code,
            ]);

            $qrPass->increment('email_send_attempts');

            return false;
        }
    }

    /**
     * Validate QR Pass by pass code
     */
    public function validatePass(string $passCode): array
    {
        try {
            $qrPass = VisitorQrPass::where('pass_code', $passCode)->first();

            if (!$qrPass) {
                return [
                    'valid' => false,
                    'message' => 'Invalid pass code',
                    'status' => 'not_found',
                ];
            }

            // Check if revoked
            if ($qrPass->revoked) {
                return [
                    'valid' => false,
                    'message' => 'Pass has been revoked: ' . ($qrPass->revocation_reason ?? 'No reason provided'),
                    'status' => 'revoked',
                    'pass' => $qrPass,
                ];
            }

            // Check validity period
            if ($qrPass->valid_from > now()) {
                return [
                    'valid' => false,
                    'message' => 'Pass not yet valid. Valid from: ' . $qrPass->valid_from->format('Y-m-d H:i'),
                    'status' => 'not_yet_valid',
                    'pass' => $qrPass,
                ];
            }

            if ($qrPass->valid_until < now()) {
                // Mark as expired
                $qrPass->markExpired();
                
                return [
                    'valid' => false,
                    'message' => 'Pass has expired on: ' . $qrPass->valid_until->format('Y-m-d H:i'),
                    'status' => 'expired',
                    'pass' => $qrPass,
                ];
            }

            // Check signature
            if (!$this->verifySignature($qrPass)) {
                return [
                    'valid' => false,
                    'message' => 'Invalid pass signature. Possible tampering detected.',
                    'status' => 'signature_invalid',
                    'pass' => $qrPass,
                ];
            }

            // Pass is valid
            return [
                'valid' => true,
                'message' => 'Pass is valid',
                'status' => 'valid',
                'pass' => $qrPass,
                'visitor' => $qrPass->visitor,
            ];

        } catch (\Exception $e) {
            Log::error('Pass Validation Error', [
                'error' => $e->getMessage(),
                'pass_code' => $passCode,
            ]);

            return [
                'valid' => false,
                'message' => 'Error validating pass',
                'status' => 'error',
            ];
        }
    }

    /**
     * Scan QR Pass at entry
     */
    public function scanPass(string $passCode, string $scannedBy = null, string $location = null): array
    {
        // First validate the pass
        $validation = $this->validatePass($passCode);

        if (!$validation['valid']) {
            return $validation;
        }

        $qrPass = $validation['pass'];
        $visitor = $validation['visitor'];

        // Record the scan
        $scanned = $qrPass->recordScan($scannedBy, $location);

        if (!$scanned) {
            return [
                'success' => false,
                'message' => 'Failed to record scan',
                'pass' => $qrPass,
                'visitor' => $visitor,
            ];
        }

        // Update visitor time_in if not already set
        if (!$visitor->time_in) {
            $visitor->update([
                'time_in' => now(),
                'status' => 'checked_in',
            ]);
        }

        Log::info('QR Pass Scanned', [
            'pass_code' => $passCode,
            'visitor_id' => $visitor->id,
            'visitor_name' => $visitor->name,
            'scanned_by' => $scannedBy,
            'location' => $location,
            'scan_count' => $qrPass->scan_count,
        ]);

        return [
            'success' => true,
            'message' => 'Pass scanned successfully',
            'pass' => $qrPass,
            'visitor' => $visitor->fresh(),
            'scan_count' => $qrPass->scan_count,
        ];
    }

    /**
     * Rescan physical ID at arrival (optional verification)
     */
    public function rescanPhysicalId(VisitorQrPass $qrPass, string $physicalIdData): array
    {
        try {
            $visitor = $qrPass->visitor;
            $verificationLog = $qrPass->verificationLog;

            if (!$verificationLog) {
                return [
                    'matched' => false,
                    'message' => 'No verification log found for comparison',
                ];
            }

            // Parse physical ID data (MRZ/QR/Barcode)
            // Compare with stored verification data
            $stored_id_number = $verificationLog->extracted_id_number;
            
            // Simple comparison (in production, use full verification)
            $matched = (stripos($physicalIdData, $stored_id_number) !== false);

            Log::info('Physical ID Rescan', [
                'pass_code' => $qrPass->pass_code,
                'matched' => $matched,
                'visitor_id' => $visitor->id,
            ]);

            return [
                'matched' => $matched,
                'message' => $matched 
                    ? 'Physical ID matches QR Pass records' 
                    : 'Physical ID does not match QR Pass records',
                'stored_id' => $stored_id_number,
            ];

        } catch (\Exception $e) {
            Log::error('Physical ID Rescan Error', [
                'error' => $e->getMessage(),
                'pass_code' => $qrPass->pass_code,
            ]);

            return [
                'matched' => false,
                'message' => 'Error comparing physical ID',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Expire old passes
     */
    public function expireOldPasses(): int
    {
        $expiredCount = VisitorQrPass::expired()->count();
        
        VisitorQrPass::expired()->update([
            'status' => 'expired',
            'is_active' => false,
        ]);

        Log::info('Expired Old Passes', ['count' => $expiredCount]);

        return $expiredCount;
    }
}



