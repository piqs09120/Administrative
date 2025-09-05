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
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('current_workflow_status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_method');
            $table->string('payment_transaction_id')->nullable()->after('payment_amount');
            $table->timestamp('payment_processed_at')->nullable()->after('payment_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'payment_amount',
                'payment_transaction_id',
                'payment_processed_at'
            ]);
        });
    }
};
