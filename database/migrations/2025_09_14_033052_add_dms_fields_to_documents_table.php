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
            if (!Schema::hasColumn('documents', 'document_uid')) {
                $table->string('document_uid')->unique()->after('id');
            }
            if (!Schema::hasColumn('documents', 'confidentiality')) {
                $table->string('confidentiality')->default('internal')->after('department');
            }
            if (!Schema::hasColumn('documents', 'retention_until')) {
                $table->dateTime('retention_until')->nullable()->after('status');
            }
            if (!Schema::hasColumn('documents', 'retention_policy')) {
                $table->string('retention_policy')->nullable()->after('retention_until');
            }

            // Helpful indexes for fast search/filter in DMS only
            $table->index(['department']);
            $table->index(['category']);
            $table->index(['status']);
            $table->index(['retention_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'document_uid')) {
                $table->dropColumn('document_uid');
            }
            if (Schema::hasColumn('documents', 'confidentiality')) {
                $table->dropColumn('confidentiality');
            }
            if (Schema::hasColumn('documents', 'retention_until')) {
                $table->dropColumn('retention_until');
            }
            if (Schema::hasColumn('documents', 'retention_policy')) {
                $table->dropColumn('retention_policy');
            }

            // Drop indexes safely (if they exist)
            try { $table->dropIndex(['documents_department_index']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['documents_category_index']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['documents_status_index']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['documents_retention_until_index']); } catch (\Throwable $e) {}
        });
    }
};
