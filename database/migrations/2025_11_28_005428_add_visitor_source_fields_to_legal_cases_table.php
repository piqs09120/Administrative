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
            if (!Schema::hasColumn('legal_cases', 'source')) {
                $table->string('source')->nullable()->after('status');
            }
            if (!Schema::hasColumn('legal_cases', 'visitor_id')) {
                $table->unsignedBigInteger('visitor_id')->nullable()->after('source');
                $table->foreign('visitor_id')->references('id')->on('visitors')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            if (Schema::hasColumn('legal_cases', 'visitor_id')) {
                $table->dropForeign(['visitor_id']);
                $table->dropColumn('visitor_id');
            }
            if (Schema::hasColumn('legal_cases', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
