<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('energy_usages', function (Blueprint $table) {
            $table->id();
            $table->string('area');
            $table->decimal('usage', 10, 2); // kWh
            $table->decimal('cost', 10, 2);
            $table->dateTime('recorded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_usages');
    }
}; 