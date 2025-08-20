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
        Schema::table('facility_reservations', function (Blueprint $table) {
            // Add new column for overall workflow status
            $table->string('current_workflow_status')->default('submitted')->after('status');

            // Remove old columns
            $table->dropColumn(['requires_legal_review', 'requires_visitor_coordination', 'legal_reviewed_by', 'legal_reviewed_at', 'legal_comment', 'digital_passes_generated', 'digital_pass_data', 'security_notified', 'security_notified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            // Re-add columns if rolling back
            $table->boolean('requires_legal_review')->default(false);
            $table->boolean('requires_visitor_coordination')->default(false);
            $table->unsignedBigInteger('legal_reviewed_by')->nullable();
            $table->timestamp('legal_reviewed_at')->nullable();
            $table->text('legal_comment')->nullable();
            $table->boolean('digital_passes_generated')->default(false);
            $table->json('digital_pass_data')->nullable();
            $table->boolean('security_notified')->default(false);
            $table->timestamp('security_notified_at')->nullable();

            // Drop the new column
            $table->dropColumn('current_workflow_status');
        });
    }
};
