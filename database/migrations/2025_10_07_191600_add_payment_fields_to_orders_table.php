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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'failed', 'refunded'])
                  ->default('pending')
                  ->after('status');
            $table->enum('payment_method', ['cash', 'card', 'upi', 'bank_transfer', 'check'])
                  ->nullable()
                  ->after('payment_status');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_method', 'paid_amount']);
        });
    }
};
