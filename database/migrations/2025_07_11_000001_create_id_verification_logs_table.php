<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('id_verification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->nullable()->constrained()->onDelete('cascade');
            
            // Form data
            $table->string('form_name')->nullable();
            $table->string('form_email')->nullable();
            $table->string('form_id_number')->nullable();
            $table->string('form_id_type')->nullable();
            $table->date('form_date_of_birth')->nullable();
            
            // Extracted data
            $table->string('extracted_name')->nullable();
            $table->string('extracted_id_number')->nullable();
            $table->date('extracted_date_of_birth')->nullable();
            $table->string('extracted_nationality')->nullable();
            $table->text('extracted_raw_data')->nullable();
            
            // Verification results
            $table->string('parse_method')->nullable(); // mrz, qr, pdf417, ocr
            $table->decimal('extraction_confidence', 5, 2)->default(0);
            $table->decimal('match_score', 5, 2)->default(0);
            $table->decimal('overall_confidence', 5, 2)->default(0);
            $table->string('verification_status')->default('pending'); // approved, review, rejected
            
            // Quality checks
            $table->boolean('quality_passed')->default(false);
            $table->json('quality_metrics')->nullable();
            $table->json('quality_issues')->nullable();
            
            // Match details
            $table->json('component_scores')->nullable();
            $table->json('mismatch_reasons')->nullable();
            
            // PhilID specific
            $table->boolean('philid_verified')->default(false);
            $table->json('philid_verification_data')->nullable();
            
            // File references
            $table->string('id_document_path')->nullable();
            $table->string('id_document_hash')->nullable();
            
            // Audit trail
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reviewer_notes')->nullable();
            
            // DPA Compliance
            $table->boolean('consent_given')->default(false);
            $table->timestamp('consent_timestamp')->nullable();
            $table->timestamp('data_retention_until')->nullable();
            $table->boolean('data_encrypted')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['verification_status', 'created_at']);
            $table->index('form_email');
            $table->index('verified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('id_verification_logs');
    }
};



