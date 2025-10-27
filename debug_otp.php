<?php

require_once 'vendor/autoload.php';

use App\Models\OtpCode;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging OTP Verification...\n\n";

// Find the latest OTP
$otp = OtpCode::where('is_used', false)->latest()->first();

if (!$otp) {
    echo "No active OTP found\n";
    exit;
}

echo "OTP Details:\n";
echo "- Code: " . $otp->otp_code . "\n";
echo "- Employee ID: " . $otp->employee_id . "\n";
echo "- Expires at: " . $otp->expires_at . "\n";
echo "- Is used: " . ($otp->is_used ? 'Yes' : 'No') . "\n";
echo "- Created at: " . $otp->created_at . "\n\n";

echo "Time Comparison:\n";
echo "- Current time: " . Carbon::now() . "\n";
echo "- OTP expires at: " . $otp->expires_at . "\n";
echo "- Is future: " . ($otp->expires_at->isFuture() ? 'Yes' : 'No') . "\n";
echo "- Time difference: " . Carbon::now()->diffInMinutes($otp->expires_at) . " minutes\n\n";

// Test the exact query from verify method
$queryResult = OtpCode::where('employee_id', $otp->employee_id)
    ->where('otp_code', $otp->otp_code)
    ->where('is_used', false)
    ->where('expires_at', '>', Carbon::now())
    ->first();

echo "Query Result: " . ($queryResult ? 'Found' : 'Not Found') . "\n";

if ($queryResult) {
    echo "Query found OTP, verification should work\n";
} else {
    echo "Query did not find OTP, checking individual conditions:\n";
    
    $byEmployee = OtpCode::where('employee_id', $otp->employee_id)->first();
    echo "- By employee ID: " . ($byEmployee ? 'Found' : 'Not Found') . "\n";
    
    $byCode = OtpCode::where('otp_code', $otp->otp_code)->first();
    echo "- By OTP code: " . ($byCode ? 'Found' : 'Not Found') . "\n";
    
    $byNotUsed = OtpCode::where('is_used', false)->first();
    echo "- By not used: " . ($byNotUsed ? 'Found' : 'Not Found') . "\n";
    
    $byNotExpired = OtpCode::where('expires_at', '>', Carbon::now())->first();
    echo "- By not expired: " . ($byNotExpired ? 'Found' : 'Not Found') . "\n";
}

echo "\nDebug completed!\n";

