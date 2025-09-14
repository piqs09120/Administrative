<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to avoid requiring doctrine/dbal for column type changes
        // Convert enum to VARCHAR(50) and default to 'draft' so Legal module can save drafts
        DB::statement("ALTER TABLE `documents` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Best-effort rollback to the original enum used by legacy code
        DB::statement("ALTER TABLE `documents` MODIFY `status` ENUM('archived','pending_release','released') NOT NULL DEFAULT 'archived'");
    }
};


