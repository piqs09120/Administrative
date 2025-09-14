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
        Schema::create('disposal_history', function (Blueprint $table) {
            $table->id();
            $table->string('document_title');
            $table->text('document_description')->nullable();
            $table->string('document_category')->nullable();
            $table->string('document_department')->nullable();
            $table->string('document_author')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('confidentiality_level')->nullable();
            $table->date('retention_until')->nullable();
            $table->string('retention_policy')->nullable();
            $table->string('previous_status');
            $table->string('disposal_reason'); // 'auto_expired', 'manually_disposed'
            $table->timestamp('disposed_at');
            $table->unsignedBigInteger('disposed_by')->nullable(); // User ID who disposed it
            $table->json('lifecycle_log')->nullable(); // Full lifecycle log at time of disposal
            $table->json('ai_analysis')->nullable(); // AI analysis data
            $table->json('metadata')->nullable(); // Document metadata
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('disposed_at');
            $table->index('disposal_reason');
            $table->index('document_department');
            $table->index('confidentiality_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_history');
    }
};