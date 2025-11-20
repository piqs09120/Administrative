<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_qr_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('verification_log_id')->nullable()->constrained('id_verification_logs')->onDelete('set null');
            
            // QR Pass details
            $table->string('pass_code')->unique(); // Unique alphanumeric code
            $table->string('qr_code_path')->nullable(); // Path to QR code image
            $table->text('qr_data')->nullable(); // Encrypted QR data
            
            // Pass validity
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('active'); // active, used, expired, revoked
            
            // Usage tracking
            $table->timestamp('scanned_at')->nullable();
            $table->string('scanned_by')->nullable(); // Security personnel who scanned
            $table->ipAddress('scan_ip')->nullable();
            $table->text('scan_location')->nullable();
            $table->integer('scan_count')->default(0);
            $table->json('scan_history')->nullable(); // Array of scan timestamps
            
            // Security
            $table->string('hmac_signature')->nullable(); // HMAC for tamper detection
            $table->boolean('revoked')->default(false);
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_by')->nullable();
            $table->text('revocation_reason')->nullable();
            
            // Email delivery
            $table->boolean('email_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->integer('email_send_attempts')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('pass_code');
            $table->index(['status', 'valid_from', 'valid_until']);
            $table->index('scanned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_qr_passes');
    }
};



