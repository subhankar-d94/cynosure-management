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
        Schema::table('invoices', function (Blueprint $table) {
            // Add missing columns
            $table->date('due_date')->nullable()->after('invoice_date');
            $table->decimal('total', 10, 2)->default(0)->after('total_amount');
            $table->decimal('subtotal', 10, 2)->default(0)->after('total');
            $table->decimal('discount', 8, 2)->default(0)->after('subtotal');
            $table->text('notes')->nullable()->after('pdf_path');

            // Add payment status for invoices
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending')->after('status');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'due_date',
                'total',
                'subtotal',
                'discount',
                'notes',
                'payment_status',
                'paid_amount'
            ]);
        });
    }
};
