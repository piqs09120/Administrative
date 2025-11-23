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
        Schema::create('case_witnesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_case_id')->constrained()->onDelete('cascade');
            $table->string('witness_name');
            $table->string('witness_department')->nullable();
            $table->string('witness_position')->nullable();
            $table->string('witness_contact')->nullable();
            $table->string('witness_email')->nullable();
            $table->text('statement')->nullable(); // Witness statement
            $table->dateTime('statement_date')->nullable(); // When statement was given
            $table->enum('statement_type', ['written', 'verbal', 'video', 'other'])->default('written');
            $table->timestamps();
            
            // Indexes
            $table->index('legal_case_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_witnesses');
    }
};
