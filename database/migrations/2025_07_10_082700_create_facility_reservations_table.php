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
        Schema::create('facility_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->unsignedBigInteger('reserved_by'); // user id
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('purpose')->nullable();
            $table->enum('status', ['pending', 'approved', 'denied', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable(); // legal approver
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            $table->foreign('reserved_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_reservations');
    }
};
