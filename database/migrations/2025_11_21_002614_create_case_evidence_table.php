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
        Schema::create('case_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_case_id')->constrained()->onDelete('cascade');
            $table->string('evidence_type'); // 'document', 'photo', 'video', 'audio', 'other'
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable(); // MIME type
            $table->integer('file_size')->nullable(); // in bytes
            $table->string('uploaded_by')->nullable();
            $table->dateTime('collected_at')->nullable(); // When evidence was collected
            $table->timestamps();
            
            // Indexes
            $table->index('legal_case_id');
            $table->index('evidence_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_evidence');
    }
};
