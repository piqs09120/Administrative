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
        Schema::create('case_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_case_id')->constrained()->onDelete('cascade');
            $table->string('user_id')->nullable(); // Who performed the action
            $table->string('user_name')->nullable(); // Cache the user name
            $table->string('action_type'); // e.g., 'status_changed', 'stage_changed', 'evidence_added', 'note_added'
            $table->string('action_description'); // Human-readable description
            $table->json('changes')->nullable(); // Store old and new values
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('legal_case_id');
            $table->index('action_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_activities');
    }
};
