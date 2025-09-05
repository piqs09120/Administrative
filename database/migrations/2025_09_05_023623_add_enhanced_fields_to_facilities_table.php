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
        Schema::table('facilities', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('facilities', 'capacity')) {
                $table->integer('capacity')->nullable()->after('description');
            }
            if (!Schema::hasColumn('facilities', 'amenities')) {
                $table->text('amenities')->nullable()->after('capacity');
            }
            if (!Schema::hasColumn('facilities', 'rating')) {
                $table->decimal('rating', 3, 2)->nullable()->after('amenities');
            }
            if (!Schema::hasColumn('facilities', 'facility_type')) {
                $table->string('facility_type')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('facilities', 'images')) {
                $table->json('images')->nullable()->after('facility_type');
            }
            if (!Schema::hasColumn('facilities', 'hourly_rate')) {
                $table->decimal('hourly_rate', 10, 2)->nullable()->after('images');
            }
            if (!Schema::hasColumn('facilities', 'operating_hours_start')) {
                $table->time('operating_hours_start')->nullable()->after('hourly_rate');
            }
            if (!Schema::hasColumn('facilities', 'operating_hours_end')) {
                $table->time('operating_hours_end')->nullable()->after('operating_hours_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn([
                'capacity',
                'amenities', 
                'rating',
                'facility_type',
                'images',
                'hourly_rate',
                'operating_hours_start',
                'operating_hours_end'
            ]);
        });
    }
};
