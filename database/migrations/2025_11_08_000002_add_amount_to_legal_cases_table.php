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
        if (!Schema::hasTable('legal_cases')) {
            return;
        }

        if (!Schema::hasColumn('legal_cases', 'amount')) {
            Schema::table('legal_cases', function (Blueprint $table) {
                $table->decimal('amount', 15, 2)->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('legal_cases') && Schema::hasColumn('legal_cases', 'amount')) {
            Schema::table('legal_cases', function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }
    }
};






