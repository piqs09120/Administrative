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
        Schema::create('facility_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_type'); // maintenance, repair, cleaning, reservation, equipment_request
            $table->string('department');
            $table->string('priority'); // low, medium, high, urgent
            $table->string('location');
            $table->unsignedBigInteger('facility_id')->nullable(); // Only for reservation type
            $table->datetime('requested_datetime');
            $table->text('description');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('status')->default('pending'); // pending, approved, rejected, in_progress, completed
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();
            
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_requests');
    }
};
