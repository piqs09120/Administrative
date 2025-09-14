<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing documents with invalid status values
        DB::table('documents')
            ->whereNotIn('status', ['archived', 'pending_release', 'released'])
            ->update(['status' => 'active']);
        
        // Update the status enum to include new values for retention workflow
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('archived', 'pending_release', 'released', 'active', 'expired', 'disposed') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('archived', 'pending_release', 'released') DEFAULT 'archived'");
    }
};