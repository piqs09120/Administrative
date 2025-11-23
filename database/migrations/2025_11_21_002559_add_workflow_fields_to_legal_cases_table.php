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
        Schema::table('legal_cases', function (Blueprint $table) {
            // Workflow stages: filing -> investigation -> review -> resolution -> closed
            $table->enum('workflow_stage', ['filing', 'investigation', 'review', 'resolution', 'closed'])
                  ->default('filing')
                  ->after('status');
            
            // Investigation fields
            $table->string('investigator_id')->nullable()->after('assigned_to');
            $table->text('investigation_notes')->nullable()->after('notes');
            $table->dateTime('investigation_started_at')->nullable();
            $table->dateTime('investigation_completed_at')->nullable();
            $table->text('investigation_findings')->nullable();
            
            // Resolution fields
            $table->enum('resolution_decision', ['approved', 'rejected', 'dismissed', 'settled', 'pending'])
                  ->nullable();
            $table->text('resolution_notes')->nullable();
            $table->text('disciplinary_actions')->nullable();
            $table->text('preventive_measures')->nullable();
            $table->dateTime('resolved_at')->nullable();
            
            // Tracking
            $table->dateTime('stage_changed_at')->nullable();
            $table->integer('days_in_current_stage')->default(0);
            
            // Indexes for performance
            $table->index('workflow_stage');
            $table->index('investigator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropIndex(['workflow_stage']);
            $table->dropIndex(['investigator_id']);
            
            $table->dropColumn([
                'workflow_stage',
                'investigator_id',
                'investigation_notes',
                'investigation_started_at',
                'investigation_completed_at',
                'investigation_findings',
                'resolution_decision',
                'resolution_notes',
                'disciplinary_actions',
                'preventive_measures',
                'resolved_at',
                'stage_changed_at',
                'days_in_current_stage'
            ]);
        });
    }
};
