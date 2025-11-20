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
        Schema::create('legal_case_policy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('legal_case_id');
            $table->unsignedBigInteger('policy_id');
            $table->timestamps();

            $table->foreign('legal_case_id')->references('id')->on('legal_cases')->onDelete('cascade');
            $table->foreign('policy_id')->references('id')->on('company_policies')->onDelete('cascade');
            $table->unique(['legal_case_id', 'policy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_case_policy');
    }
};


