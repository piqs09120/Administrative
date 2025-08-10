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
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->string('document_path')->nullable();
            $table->text('ai_classification')->nullable();
            // Do NOT re-add status here; keep existing column type as defined in base migration
            $table->text('ai_error')->nullable();
            $table->boolean('requires_legal_review')->default(false);
            $table->boolean('requires_visitor_coordination')->default(false);
            $table->json('visitor_data')->nullable();
            $table->timestamp('auto_approved_at')->nullable();
            $table->unsignedBigInteger('legal_reviewed_by')->nullable();
            $table->timestamp('legal_reviewed_at')->nullable();
            $table->text('legal_comment')->nullable();
            $table->index(['facility_id', 'start_time']);
            $table->index(['facility_id', 'end_time']);
            $table->index(['facility_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'document_path', 
                'ai_classification', 
                'ai_error',
                'requires_legal_review',
                'requires_visitor_coordination',
                'visitor_data',
                'auto_approved_at',
                'legal_reviewed_by',
                'legal_reviewed_at',
                'legal_comment'
            ]);
            $table->dropIndex(['facility_id', 'start_time']);
            $table->dropIndex(['facility_id', 'end_time']);
            $table->dropIndex(['facility_id', 'status']);
        });
    }
};
