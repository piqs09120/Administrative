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
            // Requester information
            $table->string('requester_name')->nullable();
            $table->string('requester_department')->nullable();
            $table->string('requester_contact')->nullable();
            
            // Calendar availability check status
            $table->boolean('availability_checked')->default(false);
            $table->datetime('availability_checked_at')->nullable();
            $table->text('availability_conflicts')->nullable();
            
            // Auto-approval workflow status
            $table->string('workflow_stage')->default('submitted'); // submitted, document_processed, availability_checked, legal_reviewed, visitor_processed, approved, denied
            $table->json('workflow_log')->nullable(); // Track workflow progression
            
            // Digital pass generation
            $table->boolean('digital_passes_generated')->default(false);
            $table->json('digital_pass_data')->nullable();
            
            // Security notification
            $table->boolean('security_notified')->default(false);
            $table->datetime('security_notified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'requester_name',
                'requester_department', 
                'requester_contact',
                'availability_checked',
                'availability_checked_at',
                'availability_conflicts',
                'workflow_stage',
                'workflow_log',
                'digital_passes_generated',
                'digital_pass_data',
                'security_notified',
                'security_notified_at'
            ]);
        });
    }
};
