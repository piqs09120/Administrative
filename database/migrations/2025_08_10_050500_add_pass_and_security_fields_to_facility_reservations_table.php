<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('facility_reservations', 'digital_passes_generated')) {
                $table->boolean('digital_passes_generated')->default(false);
            }
            if (!Schema::hasColumn('facility_reservations', 'digital_pass_data')) {
                $table->json('digital_pass_data')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'security_notified')) {
                $table->boolean('security_notified')->default(false);
            }
            if (!Schema::hasColumn('facility_reservations', 'security_notified_at')) {
                $table->timestamp('security_notified_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('facility_reservations', 'digital_passes_generated')) {
                $table->dropColumn('digital_passes_generated');
            }
            if (Schema::hasColumn('facility_reservations', 'digital_pass_data')) {
                $table->dropColumn('digital_pass_data');
            }
            if (Schema::hasColumn('facility_reservations', 'security_notified')) {
                $table->dropColumn('security_notified');
            }
            if (Schema::hasColumn('facility_reservations', 'security_notified_at')) {
                $table->dropColumn('security_notified_at');
            }
        });
    }
};


