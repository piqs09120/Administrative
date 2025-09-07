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
            $table->string('pass_type')->nullable()->after('host_employee');
            $table->string('pass_validity')->nullable()->after('pass_type');
            $table->datetime('pass_valid_from')->nullable()->after('pass_validity');
            $table->datetime('pass_valid_until')->nullable()->after('pass_valid_from');
            $table->string('access_level')->nullable()->after('pass_valid_until');
            $table->string('escort_required')->default('no')->after('access_level');
            $table->text('special_instructions')->nullable()->after('escort_required');
            $table->boolean('generate_digital_pass')->default(false)->after('special_instructions');
            $table->string('pass_id')->nullable()->unique()->after('generate_digital_pass');
            $table->json('pass_data')->nullable()->after('pass_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn([
                'pass_type',
                'pass_validity', 
                'pass_valid_from',
                'pass_valid_until',
                'access_level',
                'escort_required',
                'special_instructions',
                'generate_digital_pass',
                'pass_id',
                'pass_data'
            ]);
        });
    }
};
