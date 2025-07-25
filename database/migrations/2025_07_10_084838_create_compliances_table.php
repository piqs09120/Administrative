<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliances', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g. health, fire, food, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('status')->default('pending'); // pending, passed, failed, etc.
            $table->unsignedBigInteger('document_id')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliances');
    }
};
