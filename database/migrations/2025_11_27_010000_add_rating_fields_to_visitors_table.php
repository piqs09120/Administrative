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
            if (!Schema::hasColumn('visitors', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('profile_photo_url');
            }
            if (!Schema::hasColumn('visitors', 'rating_comment')) {
                $table->text('rating_comment')->nullable()->after('rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            if (Schema::hasColumn('visitors', 'rating_comment')) {
                $table->dropColumn('rating_comment');
            }
            if (Schema::hasColumn('visitors', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};


