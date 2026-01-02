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
        Schema::table('suppliers', function (Blueprint $table) {
            // Add GST number field (optional)
            $table->string('gst_number')->nullable()->after('email');

            // Drop all unnecessary columns
            $table->dropColumn([
                // Contact fields not needed
                'mobile',
                'fax',
                'designation',

                // Extended address fields not needed
                'city',
                'state',
                'postal_code',
                'country',

                // Business registration fields not needed
                'registration_number',
                'tax_id',
                'description',

                // Payment and business terms not needed
                'payment_terms',
                'credit_limit',
                'currency',
                'lead_time',
                'min_order_value',
                'discount_terms',

                // Banking information not needed
                'bank_name',
                'account_number',
                'routing_number',
                'swift_code',
                'bank_address',

                // Additional information not needed
                'products_services',
                'notes',
                'certifications',
                'insurance',

                // File uploads not needed
                'logo',
                'business_license',
                'tax_certificate',
                'insurance_certificate',
                'quality_certificates',

                // Performance metrics not needed
                'rating',
                'total_reviews',
                'total_orders',
                'total_value',

                // Legacy field not needed
                'materials_supplied',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Remove GST number
            $table->dropColumn('gst_number');

            // Restore all columns
            $table->string('mobile', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('designation')->nullable();

            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country')->nullable();

            $table->string('registration_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('description')->nullable();

            $table->enum('payment_terms', [
                'net_15', 'net_30', 'net_45', 'net_60', 'due_on_receipt', 'prepaid'
            ])->default('net_30');
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->enum('currency', ['USD', 'EUR', 'GBP', 'CAD', 'INR', 'JPY'])->default('USD');
            $table->integer('lead_time')->nullable();
            $table->decimal('min_order_value', 15, 2)->nullable();
            $table->string('discount_terms')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('swift_code')->nullable();
            $table->text('bank_address')->nullable();

            $table->text('products_services')->nullable();
            $table->text('notes')->nullable();
            $table->string('certifications')->nullable();
            $table->string('insurance')->nullable();

            $table->string('logo')->nullable();
            $table->string('business_license')->nullable();
            $table->string('tax_certificate')->nullable();
            $table->string('insurance_certificate')->nullable();
            $table->string('quality_certificates')->nullable();

            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('total_value', 15, 2)->default(0.00);

            $table->text('materials_supplied')->nullable();
        });
    }
};
