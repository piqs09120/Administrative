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
            // Legal document retention policy fields (No Deletion, Archive Only)
            // Note: archived_at and disposal_date already exist from previous migrations
            if (!Schema::hasColumn('documents', 'retention_years')) {
                $table->integer('retention_years')->nullable()->after('disposal_date');
            }
            if (!Schema::hasColumn('documents', 'can_dispose')) {
                $table->boolean('can_dispose')->default(false)->after('retention_years');
            }
            if (!Schema::hasColumn('documents', 'disposal_reason')) {
                $table->text('disposal_reason')->nullable()->after('can_dispose');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Remove retention policy fields (disposal_date stays as it was added in previous migration)
            $table->dropColumn([
                'archived_at',
                'retention_years',
                'can_dispose',
                'disposal_reason'
            ]);
        });
    }
};
