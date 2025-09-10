<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('facility_requests', function (Blueprint $table) {
            $table->dateTime('requested_end_datetime')->nullable()->after('requested_datetime');
        });
    }

    public function down(): void
    {
        Schema::table('facility_requests', function (Blueprint $table) {
            $table->dropColumn('requested_end_datetime');
        });
    }
};
