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
        Schema::create('legal_ai_results', function (Blueprint $table) {
            $table->id();
            $table->string('document_id')->nullable(); // Link to document
            $table->string('case_id')->nullable(); // Link to case/complaint
            $table->string('report_id')->nullable(); // Link to violation report
            $table->string('analysis_type'); // document_classification, violation_analysis, compliance_check
            $table->json('ai_result'); // Full AI analysis result
            $table->string('document_type')->nullable(); // Classified document type
            $table->decimal('confidence', 5, 2)->nullable(); // AI confidence score
            $table->json('detected_violations')->nullable(); // Violations found
            $table->json('applicable_laws')->nullable(); // Laws referenced
            $table->string('compliance_status')->nullable(); // compliant, non_compliant, needs_review
            $table->string('risk_level')->nullable(); // low, medium, high, critical
            $table->text('summary')->nullable(); // AI summary
            $table->json('policy_links')->nullable(); // Linked company policies
            $table->json('recommendations')->nullable(); // AI recommendations
            $table->string('ai_model')->default('gemini-pro'); // AI model used
            $table->decimal('processing_time', 8, 3)->nullable(); // Processing time in seconds
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['document_id', 'analysis_type']);
            $table->index(['case_id', 'analysis_type']);
            $table->index(['report_id', 'analysis_type']);
            $table->index(['risk_level', 'compliance_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_ai_results');
    }
};