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
        Schema::create('visitor_checkin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('checked_in_by')->constrained('users')->onDelete('cascade');
            $table->string('action'); // 'checkin', 'checkout', 'register'
            $table->text('notes')->nullable();
            $table->json('visitor_data')->nullable(); // Store visitor data at time of check-in
            $table->timestamp('action_time');
            $table->timestamps();
            
            $table->index(['visitor_id', 'action_time']);
            $table->index(['checked_in_by', 'action_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_checkin_logs');
    }
};
