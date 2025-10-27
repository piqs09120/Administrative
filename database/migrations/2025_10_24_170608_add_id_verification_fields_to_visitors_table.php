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
        Schema::table('visitors', function (Blueprint $table) {
            // ID Document fields
            $table->string('id_document_path')->nullable()->after('id_number');
            $table->string('id_document_original_name')->nullable()->after('id_document_path');
            $table->string('id_document_mime_type')->nullable()->after('id_document_original_name');
            $table->integer('id_document_size')->nullable()->after('id_document_mime_type');
            
            // ID Verification fields
            $table->boolean('id_verified')->default(false)->after('id_document_size');
            $table->timestamp('id_verified_at')->nullable()->after('id_verified');
            $table->unsignedBigInteger('id_verified_by')->nullable()->after('id_verified_at');
            $table->text('id_verification_notes')->nullable()->after('id_verified_by');
            $table->string('id_verification_method')->nullable()->after('id_verification_notes'); // 'upload', 'scan', 'manual'
            
            // Scanned data from ID
            $table->json('id_scanned_data')->nullable()->after('id_verification_method');
            
            // Foreign key for verifier
            $table->foreign('id_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropForeign(['id_verified_by']);
            $table->dropColumn([
                'id_document_path',
                'id_document_original_name', 
                'id_document_mime_type',
                'id_document_size',
                'id_verified',
                'id_verified_at',
                'id_verified_by',
                'id_verification_notes',
                'id_verification_method',
                'id_scanned_data'
            ]);
        });
    }
};
