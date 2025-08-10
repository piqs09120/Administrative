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
            $table->dropColumn([
                'document_processed_at',
                'calendar_checked_at',
                'availability_checked_at',
                'availability_checked',
                'availability_conflicts'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->timestamp('document_processed_at')->nullable();
            $table->timestamp('calendar_checked_at')->nullable();
            $table->timestamp('availability_checked_at')->nullable();
            $table->boolean('availability_checked')->default(false);
            $table->text('availability_conflicts')->nullable();
        });
    }
};
