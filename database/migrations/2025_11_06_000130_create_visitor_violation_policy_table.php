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
        Schema::create('visitor_violation_policy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visitor_violation_id');
            $table->unsignedBigInteger('policy_id');
            $table->timestamps();

            $table->foreign('visitor_violation_id')->references('id')->on('visitor_violations')->onDelete('cascade');
            $table->foreign('policy_id')->references('id')->on('company_policies')->onDelete('cascade');
            $table->unique(['visitor_violation_id', 'policy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_violation_policy');
    }
};


