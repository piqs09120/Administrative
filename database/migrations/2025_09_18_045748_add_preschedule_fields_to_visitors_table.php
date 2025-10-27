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
            $table->date('scheduled_date')->nullable()->after('arrival_time');
            $table->time('scheduled_time')->nullable()->after('scheduled_date');
            $table->integer('expected_duration')->nullable()->after('scheduled_time');
            $table->string('phone')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['scheduled_date', 'scheduled_time', 'expected_duration', 'phone']);
        });
    }
};
