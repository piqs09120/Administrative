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
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->string('id_type')->nullable()->after('host_employee');
            $table->string('id_number')->nullable()->after('id_type');
            $table->string('vehicle_plate')->nullable()->after('id_number');
            $table->string('status')->default('active')->after('vehicle_plate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['email', 'id_type', 'id_number', 'vehicle_plate', 'status']);
        });
    }
};
