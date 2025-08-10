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
            // Ensure auto_approved_at exists
            if (!Schema::hasColumn('facility_reservations', 'auto_approved_at')) {
                $table->timestamp('auto_approved_at')->nullable()->after('remarks');
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

            // Performance indexes (guard if missing)
            $table->index(['facility_id', 'start_time'], 'fr_facility_start_idx');
            $table->index(['facility_id', 'end_time'], 'fr_facility_end_idx');
            $table->index(['facility_id', 'status'], 'fr_facility_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('facility_reservations', 'legal_reviewed_by')) {
                $table->dropColumn('legal_reviewed_by');
            }
            if (Schema::hasColumn('facility_reservations', 'legal_reviewed_at')) {
                $table->dropColumn('legal_reviewed_at');
            }
            if (Schema::hasColumn('facility_reservations', 'legal_comment')) {
                $table->dropColumn('legal_comment');
            }
            $table->dropIndex('fr_facility_start_idx');
            $table->dropIndex('fr_facility_end_idx');
            $table->dropIndex('fr_facility_status_idx');
        });
    }
};
