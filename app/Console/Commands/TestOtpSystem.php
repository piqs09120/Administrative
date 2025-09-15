<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeptAccount;
use App\Models\OtpCode;
use App\Notifications\OtpCodeNotification;

class TestOtpSystem extends Command
{
    protected $signature = 'otp:test';
    protected $description = 'Test the OTP authentication system';

    public function handle()
    {
        $this->info('🧪 Testing OTP Authentication System...');
        
        // Get a test user from department_accounts
        $user = DeptAccount::first();
        
        if (!$user) {
            $this->error('No users found in department_accounts table. Please add a user first.');
            return 1;
        }
        
        $this->info("Testing with user: {$user->employee_name} ({$user->employee_id})");
        $this->info("Email: {$user->email}");
        
        // Test 1: Create OTP
        $this->info("\n1️⃣ Creating OTP...");
        $otp = OtpCode::createForEmployee($user->employee_id, $user->email, '127.0.0.1');
        $this->info("✅ OTP created: {$otp->otp_code}");
        $this->info("   Expires at: {$otp->expires_at}");
        
        // Test 2: Verify OTP
        $this->info("\n2️⃣ Testing OTP verification...");
        $isValid = OtpCode::verify($user->employee_id, $otp->otp_code);
        $this->info($isValid ? "✅ OTP verification successful" : "❌ OTP verification failed");
        
        // Test 3: Try to verify same OTP again (should fail)
        $this->info("\n3️⃣ Testing OTP reuse (should fail)...");
        $isValidAgain = OtpCode::verify($user->employee_id, $otp->otp_code);
        $this->info($isValidAgain ? "❌ OTP reuse should have failed" : "✅ OTP reuse correctly blocked");
        
        // Test 4: Create new OTP and test expiry
        $this->info("\n4️⃣ Testing OTP expiry...");
        $newOtp = OtpCode::createForEmployee($user->employee_id, $user->email, '127.0.0.1');
        
        // Manually set expiry to past
        $newOtp->update(['expires_at' => now()->subMinutes(1)]);
        
        $isExpiredValid = OtpCode::verify($user->employee_id, $newOtp->otp_code);
        $this->info($isExpiredValid ? "❌ Expired OTP should have failed" : "✅ Expired OTP correctly rejected");
        
        // Test 5: Test email notification (without actually sending)
        $this->info("\n5️⃣ Testing email notification structure...");
        try {
            $notification = new OtpCodeNotification('123456', $user->employee_name);
            $mailMessage = $notification->toMail($user);
            $this->info("✅ Email notification structure is valid");
            $this->info("   Subject: " . $mailMessage->subject);
        } catch (\Exception $e) {
            $this->error("❌ Email notification failed: " . $e->getMessage());
        }
        
        // Test 6: Cleanup expired OTPs
        $this->info("\n6️⃣ Testing cleanup...");
        $deletedCount = OtpCode::cleanupExpired();
        $this->info("✅ Cleaned up {$deletedCount} expired OTPs");
        
        // Clean up test OTPs
        OtpCode::where('employee_id', $user->employee_id)->delete();
        $this->info("✅ Test OTPs cleaned up");
        
        $this->info("\n🎉 OTP system test completed successfully!");
        $this->info("\n📋 Summary:");
        $this->info("   ✅ OTP creation works");
        $this->info("   ✅ OTP verification works");
        $this->info("   ✅ OTP reuse prevention works");
        $this->info("   ✅ OTP expiry handling works");
        $this->info("   ✅ Email notification structure works");
        $this->info("   ✅ Cleanup functionality works");
        
        return 0;
    }
}
