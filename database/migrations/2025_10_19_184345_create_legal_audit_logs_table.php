<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safeguard if the table already exists from a previous setup
        if (Schema::hasTable('legal_audit_logs')) {
            return;
        }

        Schema::create('legal_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action_type'); // document_upload, complaint_filed, violation_reported, ai_analysis, etc.
            $table->timestamp('timestamp');
            $table->string('user_id');
            $table->string('user_name');
            $table->string('user_role');
            $table->string('module')->default('Legal Management');
            $table->string('entity_type')->nullable(); // document, complaint, violation_report, etc.
            $table->string('entity_id')->nullable(); // ID of the affected entity
            $table->json('ai_result')->nullable(); // AI analysis results
            $table->json('next_steps')->nullable(); // Recommended next steps
            $table->text('description')->nullable(); // Human-readable description
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['action_type', 'timestamp']);
            $table->index(['user_id', 'timestamp']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_audit_logs');
    }
};