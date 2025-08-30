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
        Schema::table('documents', function (Blueprint $table) {
            $table->json('legal_case_data')->nullable()->after('lifecycle_log')->comment('Legal case information for high-risk documents');
            $table->unsignedBigInteger('linked_reservation_id')->nullable()->after('legal_case_data')->comment('Link to auto-created facility reservation');
            
            $table->index(['linked_reservation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['linked_reservation_id']);
            $table->dropColumn(['legal_case_data', 'linked_reservation_id']);
        });
    }
};
