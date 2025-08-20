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
            $table->boolean('requires_legal_review')->default(false)->after('category');
            $table->boolean('requires_visitor_coordination')->default(false)->after('requires_legal_review');
            $table->string('legal_risk_score')->default('Low')->after('requires_visitor_coordination');
            $table->json('workflow_log')->nullable()->after('legal_risk_score');
            $table->string('workflow_stage')->default('uploaded')->after('workflow_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'requires_legal_review',
                'requires_visitor_coordination', 
                'legal_risk_score',
                'workflow_log',
                'workflow_stage'
            ]);
        });
    }
};
