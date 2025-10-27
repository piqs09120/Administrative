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
            $table->string('id_validation_status')->default('pending')->after('id_document_size');
            $table->integer('id_validation_confidence')->default(0)->after('id_validation_status');
            $table->json('id_validation_details')->nullable()->after('id_validation_confidence');
            $table->text('id_validation_error')->nullable()->after('id_validation_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn([
                'id_validation_status',
                'id_validation_confidence', 
                'id_validation_details',
                'id_validation_error'
            ]);
        });
    }
};