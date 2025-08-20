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
        Schema::create('reservation_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_reservation_id')->constrained()->onDelete('cascade');
            $table->string('task_type', 50);
            $table->string('status', 20)->default('pending');
            $table->string('assigned_to_module', 10);
            $table->json('details')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_tasks');
    }
};
