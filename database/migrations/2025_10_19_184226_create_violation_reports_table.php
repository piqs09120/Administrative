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
        // Safeguard in case the table was created manually or by a previous migration
        if (Schema::hasTable('violation_reports')) {
            return;
        }

        Schema::create('violation_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_id')->unique(); // VIO-2025-XXXXX format
            $table->string('case_id')->nullable(); // Link to complaint if applicable
            $table->string('reporter_id'); // User who reported
            $table->string('reporter_name');
            $table->string('reporter_department');
            $table->string('violator_id')->nullable(); // Employee who violated
            $table->string('violator_name')->nullable();
            $table->string('violator_department')->nullable();
            $table->text('violation_description');
            $table->string('violation_type'); // Policy Violation, Legal Violation, etc.
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->enum('status', ['reported', 'under_investigation', 'confirmed', 'dismissed', 'resolved'])->default('reported');
            $table->string('assigned_to')->nullable(); // Legal Officer assigned
            $table->text('incident_details');
            $table->date('incident_date');
            $table->string('incident_location');
            $table->json('witnesses')->nullable();
            $table->json('evidence_documents')->nullable(); // Document IDs
            $table->json('ai_analysis')->nullable(); // AI violation analysis
            $table->json('detected_violations')->nullable(); // Specific violations found
            $table->json('applicable_laws')->nullable(); // Laws/policies violated
            $table->text('investigation_notes')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->string('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('workflow_log')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'severity']);
            $table->index(['reporter_department', 'status']);
            $table->index(['violator_department', 'status']);
            $table->index('incident_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violation_reports');
    }
};