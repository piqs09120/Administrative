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
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->string('employee_involved')->nullable()->after('assigned_to');
            $table->datetime('incident_date')->nullable()->after('employee_involved');
            $table->string('incident_location')->nullable()->after('incident_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropColumn(['employee_involved', 'incident_date', 'incident_location']);
        });
    }
};
