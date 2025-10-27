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
        Schema::create('company_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_code')->unique(); // e.g., HR-001, LEGAL-002
            $table->string('title');
            $table->text('description');
            $table->longText('content');
            $table->string('category'); // HR, Legal, Finance, Operations, etc.
            $table->string('department')->nullable();
            $table->string('version')->default('1.0');
            $table->date('effective_date');
            $table->date('review_date')->nullable();
            $table->enum('status', ['active', 'draft', 'archived', 'superseded'])->default('active');
            $table->json('keywords')->nullable(); // For AI search and linking
            $table->json('related_laws')->nullable(); // Philippine laws referenced
            $table->json('applicable_roles')->nullable(); // Which roles this applies to
            $table->string('created_by');
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['category', 'status']);
            $table->index(['department', 'status']);
            $table->index('effective_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_policies');
    }
};