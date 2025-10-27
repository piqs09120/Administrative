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
        Schema::table('access_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable()->after('user_id');
            $table->json('metadata')->nullable()->after('description');
            $table->index(['document_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_logs', function (Blueprint $table) {
            $table->dropIndex(['document_id', 'action']);
            $table->dropColumn(['document_id', 'metadata']);
        });
    }
};
