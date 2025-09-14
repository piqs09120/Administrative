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
            // Add disposal tracking fields
            $table->dateTime('disposal_date')->nullable()->after('retention_until');
            $table->string('previous_status')->nullable()->after('disposal_date');
            $table->boolean('is_in_disposal_review')->default(false)->after('previous_status');
            $table->dateTime('final_deletion_date')->nullable()->after('is_in_disposal_review');
            
            // Add index for disposal queries
            $table->index(['is_in_disposal_review', 'disposal_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'disposal_date',
                'previous_status', 
                'is_in_disposal_review',
                'final_deletion_date'
            ]);
            
            // Drop index
            try { 
                $table->dropIndex(['documents_is_in_disposal_review_disposal_date_index']); 
            } catch (\Throwable $e) {}
        });
    }
};