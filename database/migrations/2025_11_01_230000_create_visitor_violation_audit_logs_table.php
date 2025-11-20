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
        Schema::create('visitor_violation_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('violation_id');
            $table->string('action'); // created, updated, status_changed, escalated, closed, etc.
            $table->string('actor_id')->nullable(); // User who performed action
            $table->string('actor_name')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('notes')->nullable();
            $table->json('changes')->nullable(); // Track what changed
            $table->timestamps();
            
            $table->foreign('violation_id')->references('id')->on('visitor_violations')->onDelete('cascade');
            $table->index(['violation_id', 'action']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_violation_audit_logs');
    }
};



