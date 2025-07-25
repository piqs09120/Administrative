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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('purpose')->nullable();
            $table->unsignedBigInteger('facility_id')->nullable();
            $table->dateTime('time_in');
            $table->dateTime('time_out')->nullable();
            $table->timestamps();

            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
