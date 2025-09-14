<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('signature_status')->nullable()->after('workflow_stage');
            $table->timestamp('signed_at')->nullable()->after('signature_status');
            $table->json('signers')->nullable()->after('signed_at');
            $table->string('final_pdf_path')->nullable()->after('signers');
            $table->string('signature_provider_id')->nullable()->after('final_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['signature_status','signed_at','signers','final_pdf_path','signature_provider_id']);
        });
    }
};


