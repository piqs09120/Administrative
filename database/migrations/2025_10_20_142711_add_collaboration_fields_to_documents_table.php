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
            // Only add fields that don't already exist
            if (!Schema::hasColumn('documents', 'editing_history')) {
                $table->json('editing_history')->nullable()->after('disposal_reason');
            }
            if (!Schema::hasColumn('documents', 'collaborators')) {
                $table->json('collaborators')->nullable()->after('editing_history');
            }
            if (!Schema::hasColumn('documents', 'last_edited_by')) {
                $table->string('last_edited_by')->nullable()->after('collaborators');
            }
            if (!Schema::hasColumn('documents', 'last_edited_at')) {
                $table->timestamp('last_edited_at')->nullable()->after('last_edited_by');
            }
            if (!Schema::hasColumn('documents', 'access_log')) {
                $table->json('access_log')->nullable()->after('last_edited_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'editing_history',
                'collaborators',
                'last_edited_by',
                'last_edited_at',
                'access_log'
            ]);
        });
    }
};