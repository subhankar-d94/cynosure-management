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
            // Add issue_date (alias for invoice_date for compatibility)
            $table->date('issue_date')->nullable()->after('invoice_date');

            // Add customer info fields for cases where invoice is not linked to a customer record
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->text('customer_address')->nullable()->after('customer_email');
            $table->string('customer_phone')->nullable()->after('customer_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'issue_date',
                'customer_name',
                'customer_email',
                'customer_address',
                'customer_phone'
            ]);
        });
    }
};
