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
        Schema::table('customers', function (Blueprint $table) {
            // First modify the existing customer_type column to accept new values
            $table->string('customer_type', 20)->default('individual')->change();

            // Basic customer information
            $table->string('customer_code')->unique()->nullable()->after('id');
            $table->string('company_name')->nullable()->after('name');
            $table->string('gst_number')->nullable()->after('company_name');
            $table->string('status', 20)->default('active')->after('customer_type');

            // Financial settings
            $table->decimal('credit_limit', 15, 2)->default(0)->after('notes');
            $table->integer('payment_terms')->default(30)->after('credit_limit');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('payment_terms');

            // Communication preferences
            $table->boolean('email_notifications')->default(true)->after('discount_percentage');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            $table->boolean('marketing_emails')->default(false)->after('sms_notifications');
            $table->string('preferred_contact_method', 20)->default('email')->after('marketing_emails');

            // Add indexes for better performance
            $table->index('customer_code');
            $table->index('status');
            $table->index('gst_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['customer_code']);
            $table->dropIndex(['status']);
            $table->dropIndex(['gst_number']);

            $table->dropColumn([
                'customer_code',
                'company_name',
                'gst_number',
                'status',
                'credit_limit',
                'payment_terms',
                'discount_percentage',
                'email_notifications',
                'sms_notifications',
                'marketing_emails',
                'preferred_contact_method'
            ]);
        });
    }
};
