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
            if (!Schema::hasColumn('facility_reservations', 'document_path')) {
                $table->string('document_path')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'ai_classification')) {
                $table->text('ai_classification')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'ai_error')) {
                $table->text('ai_error')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'requires_legal_review')) {
                $table->boolean('requires_legal_review')->default(false);
            }
            if (!Schema::hasColumn('facility_reservations', 'requires_visitor_coordination')) {
                $table->boolean('requires_visitor_coordination')->default(false);
            }
            if (!Schema::hasColumn('facility_reservations', 'visitor_data')) {
                $table->json('visitor_data')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'auto_approved_at')) {
                $table->timestamp('auto_approved_at')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'legal_reviewed_by')) {
                $table->unsignedBigInteger('legal_reviewed_by')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'legal_reviewed_at')) {
                $table->timestamp('legal_reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'legal_comment')) {
                $table->text('legal_comment')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank to avoid dropping user data.
    }
};



