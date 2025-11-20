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
            if (!Schema::hasColumn('facility_reservations', 'damage_flag')) {
                $table->boolean('damage_flag')->default(false)->after('remarks');
            }

            if (!Schema::hasColumn('facility_reservations', 'damage_cost')) {
                $table->decimal('damage_cost', 12, 2)->nullable()->after('damage_flag');
            }

            if (!Schema::hasColumn('facility_reservations', 'inspection_notes')) {
                $table->text('inspection_notes')->nullable()->after('damage_cost');
            }

            if (!Schema::hasColumn('facility_reservations', 'damage_photos')) {
                $table->json('damage_photos')->nullable()->after('inspection_notes');
            }

            if (!Schema::hasColumn('facility_reservations', 'checked_out_at')) {
                $table->timestamp('checked_out_at')->nullable()->after('damage_photos');
            }

            if (!Schema::hasColumn('facility_reservations', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('checked_out_at');
            }

            if (!Schema::hasColumn('facility_reservations', 'inspected_by')) {
                $table->unsignedBigInteger('inspected_by')->nullable()->after('returned_at');
                $table->foreign('inspected_by')->references('id')->on('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('facility_reservations', 'inspected_at')) {
                $table->timestamp('inspected_at')->nullable()->after('inspected_by');
            }

            if (!Schema::hasColumn('facility_reservations', 'legal_case_id')) {
                $table->unsignedBigInteger('legal_case_id')->nullable()->after('inspected_at');
                $table->foreign('legal_case_id')->references('id')->on('legal_cases')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('facility_reservations', 'legal_case_id')) {
                $table->dropForeign(['legal_case_id']);
                $table->dropColumn('legal_case_id');
            }

            if (Schema::hasColumn('facility_reservations', 'inspected_at')) {
                $table->dropColumn('inspected_at');
            }

            if (Schema::hasColumn('facility_reservations', 'inspected_by')) {
                $table->dropForeign(['inspected_by']);
                $table->dropColumn('inspected_by');
            }

            if (Schema::hasColumn('facility_reservations', 'returned_at')) {
                $table->dropColumn('returned_at');
            }

            if (Schema::hasColumn('facility_reservations', 'checked_out_at')) {
                $table->dropColumn('checked_out_at');
            }

            if (Schema::hasColumn('facility_reservations', 'damage_photos')) {
                $table->dropColumn('damage_photos');
            }

            if (Schema::hasColumn('facility_reservations', 'inspection_notes')) {
                $table->dropColumn('inspection_notes');
            }

            if (Schema::hasColumn('facility_reservations', 'damage_cost')) {
                $table->dropColumn('damage_cost');
            }

            if (Schema::hasColumn('facility_reservations', 'damage_flag')) {
                $table->dropColumn('damage_flag');
            }
        });
    }
};
