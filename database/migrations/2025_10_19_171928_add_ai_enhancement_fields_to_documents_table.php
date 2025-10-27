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
        Schema::table('documents', function (Blueprint $table) {
            // AI Classification Enhancement Fields
            $table->string('ai_classification')->nullable()->after('category')->comment('AI-detected document category');
            $table->decimal('ai_confidence', 3, 2)->nullable()->after('ai_classification')->comment('AI classification confidence score (0.00-1.00)');
            
            // Violation Analysis Fields
            $table->string('violation_score')->default('Low')->after('legal_risk_score')->comment('Violation risk score: Low, Medium, High, Critical');
            $table->json('violation_details')->nullable()->after('violation_score')->comment('Detailed violation analysis results');
            $table->json('flagged_issues')->nullable()->after('violation_details')->comment('Specific issues flagged by AI analysis');
            
            // Compliance Analysis Fields
            $table->string('compliance_status')->default('unknown')->after('flagged_issues')->comment('Compliance status: compliant, non_compliant, review_required, unknown');
            $table->json('compliance_details')->nullable()->after('compliance_status')->comment('Detailed compliance analysis results');
            $table->json('regulatory_standards')->nullable()->after('compliance_details')->comment('Applicable regulatory standards identified');
            
            // Enhanced AI Analysis Fields
            $table->json('ai_tags')->nullable()->after('regulatory_standards')->comment('AI-generated tags for document');
            $table->json('ai_insights')->nullable()->after('ai_tags')->comment('AI-generated insights and recommendations');
            $table->boolean('ai_analysis_completed')->default(false)->after('ai_insights')->comment('Flag indicating if AI analysis is complete');
            $table->timestamp('ai_analysis_date')->nullable()->after('ai_analysis_completed')->comment('Timestamp of last AI analysis');
            
            // Alert System Fields
            $table->boolean('requires_immediate_review')->default(false)->after('ai_analysis_date')->comment('Flag for high-risk documents requiring immediate attention');
            $table->json('alert_reasons')->nullable()->after('requires_immediate_review')->comment('Reasons why document requires immediate review');
            
            // Indexes for performance
            $table->index(['ai_classification']);
            $table->index(['violation_score']);
            $table->index(['compliance_status']);
            $table->index(['requires_immediate_review']);
            $table->index(['ai_analysis_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['ai_classification']);
            $table->dropIndex(['violation_score']);
            $table->dropIndex(['compliance_status']);
            $table->dropIndex(['requires_immediate_review']);
            $table->dropIndex(['ai_analysis_completed']);
            
            $table->dropColumn([
                'ai_classification',
                'ai_confidence',
                'violation_score',
                'violation_details',
                'flagged_issues',
                'compliance_status',
                'compliance_details',
                'regulatory_standards',
                'ai_tags',
                'ai_insights',
                'ai_analysis_completed',
                'ai_analysis_date',
                'requires_immediate_review',
                'alert_reasons'
            ]);
        });
    }
};