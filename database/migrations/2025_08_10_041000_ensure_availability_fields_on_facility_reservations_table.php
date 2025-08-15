<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('facility_reservations', 'availability_checked')) {
                $table->boolean('availability_checked')->default(false);
            }
            if (!Schema::hasColumn('facility_reservations', 'availability_checked_at')) {
                $table->dateTime('availability_checked_at')->nullable();
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
        });
    }

    public function down(): void
    {
        // Do not drop columns to preserve data
    }
};



