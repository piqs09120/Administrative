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
            if (!Schema::hasColumn('facility_reservations', 'facility_request_id')) {
                $table->unsignedBigInteger('facility_request_id')->nullable()->after('facility_id');
                $table->foreign('facility_request_id')
                    ->references('id')
                    ->on('facility_requests')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('facility_reservations', 'damage_case_closed_at')) {
                $table->timestamp('damage_case_closed_at')->nullable()->after('legal_case_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('facility_reservations', 'facility_request_id')) {
                $table->dropForeign(['facility_request_id']);
                $table->dropColumn('facility_request_id');
            }

            if (Schema::hasColumn('facility_reservations', 'damage_case_closed_at')) {
                $table->dropColumn('damage_case_closed_at');
            }
        });
    }
};
