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
        Schema::create('employee_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('case_id')->unique(); // CASE-2025-XXXXX format
            $table->string('complainant_id'); // Employee ID
            $table->string('complainant_name');
            $table->string('complainant_department');
            $table->string('complainant_email');
            $table->string('complainant_contact');
            $table->text('complaint_description');
            $table->string('complaint_type'); // Harassment, Discrimination, Policy Violation, etc.
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->enum('status', ['submitted', 'under_review', 'investigation', 'resolved', 'dismissed', 'escalated'])->default('submitted');
            $table->string('assigned_to')->nullable(); // Legal Officer assigned
            $table->text('incident_details')->nullable();
            $table->date('incident_date')->nullable();
            $table->string('incident_location')->nullable();
            $table->json('witnesses')->nullable(); // Witness information
            $table->json('supporting_documents')->nullable(); // Document IDs
            $table->json('ai_analysis')->nullable(); // AI violation analysis
            $table->json('applicable_laws')->nullable(); // Detected laws/policies
            $table->text('resolution_notes')->nullable();
            $table->string('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('workflow_log')->nullable(); // Track all actions
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'priority']);
            $table->index(['complainant_department', 'status']);
            $table->index('incident_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_complaints');
    }
};