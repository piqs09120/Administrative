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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('facility_reservations', 'requester_name')) {
                $table->string('requester_name')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'requester_department')) {
                $table->string('requester_department')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'requester_contact')) {
                $table->string('requester_contact')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'availability_checked')) {
                $table->boolean('availability_checked')->default(false);
            }
            if (!Schema::hasColumn('facility_reservations', 'availability_checked_at')) {
                $table->datetime('availability_checked_at')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'availability_conflicts')) {
                $table->text('availability_conflicts')->nullable();
            }
            if (!Schema::hasColumn('facility_reservations', 'workflow_stage')) {
                $table->string('workflow_stage')->default('submitted');
            }
            if (!Schema::hasColumn('facility_reservations', 'workflow_log')) {
                $table->json('workflow_log')->nullable();
            }
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
                $table->datetime('security_notified_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            //
        });
    }
};
