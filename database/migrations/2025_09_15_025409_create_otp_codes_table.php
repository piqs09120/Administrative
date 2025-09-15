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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('email');
            $table->string('otp_code', 6);
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['employee_id', 'otp_code']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
