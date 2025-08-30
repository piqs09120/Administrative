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
        Schema::create('legal_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_title');
            $table->text('case_description')->nullable();
            $table->string('case_type')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'ongoing', 'completed', 'rejected'])->default('pending');
            $table->string('assigned_to')->nullable();
            $table->string('created_by')->nullable();
            $table->string('case_number')->unique();
            $table->date('filing_date')->nullable();
            $table->date('court_date')->nullable();
            $table->text('outcome')->nullable();
            $table->text('notes')->nullable();
            $table->string('linked_case_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_cases');
    }
};
