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
            // Rename 'name' to 'company_name' and make it required
            $table->renameColumn('name', 'company_name');

            // Contact Information
            $table->string('mobile', 20)->nullable()->after('phone');
            $table->string('fax', 20)->nullable()->after('mobile');
            $table->string('designation')->nullable()->after('contact_person');

            // Address fields (expand from single address field)
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');
            $table->string('website')->nullable()->after('country');

            // Business Information
            $table->enum('category', [
                'raw_materials', 'manufacturing', 'technology', 'services',
                'logistics', 'packaging', 'maintenance', 'consulting'
            ])->default('technology')->after('website');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active')->after('category');
            $table->string('registration_number')->nullable()->after('status');
            $table->string('tax_id')->nullable()->after('registration_number');
            $table->text('description')->nullable()->after('tax_id');

            // Business Terms
            $table->enum('payment_terms', [
                'net_15', 'net_30', 'net_45', 'net_60', 'due_on_receipt', 'prepaid'
            ])->default('net_30')->after('description');
            $table->decimal('credit_limit', 15, 2)->nullable()->after('payment_terms');
            $table->enum('currency', ['USD', 'EUR', 'GBP', 'CAD', 'INR', 'JPY'])->default('USD')->after('credit_limit');
            $table->integer('lead_time')->nullable()->comment('Lead time in days')->after('currency');
            $table->decimal('min_order_value', 15, 2)->nullable()->after('lead_time');
            $table->string('discount_terms')->nullable()->after('min_order_value');

            // Banking Information
            $table->string('bank_name')->nullable()->after('discount_terms');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('routing_number')->nullable()->after('account_number');
            $table->string('swift_code')->nullable()->after('routing_number');
            $table->text('bank_address')->nullable()->after('swift_code');

            // Additional Information
            $table->text('products_services')->nullable()->after('bank_address');
            $table->text('notes')->nullable()->after('products_services');
            $table->string('certifications')->nullable()->after('notes');
            $table->string('insurance')->nullable()->after('certifications');

            // File Storage
            $table->string('logo')->nullable()->after('insurance');
            $table->string('business_license')->nullable()->after('logo');
            $table->string('tax_certificate')->nullable()->after('business_license');
            $table->string('insurance_certificate')->nullable()->after('tax_certificate');
            $table->string('quality_certificates')->nullable()->after('insurance_certificate');

            // Performance Metrics
            $table->decimal('rating', 3, 2)->default(0.00)->after('quality_certificates');
            $table->integer('total_reviews')->default(0)->after('rating');
            $table->integer('total_orders')->default(0)->after('total_reviews');
            $table->decimal('total_value', 15, 2)->default(0.00)->after('total_orders');

            // Add indexes for performance
            $table->index(['status']);
            $table->index(['category']);
            $table->index(['rating']);
            $table->index(['company_name']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['status']);
            $table->dropIndex(['category']);
            $table->dropIndex(['rating']);
            $table->dropIndex(['company_name']);
            $table->dropIndex(['email']);

            // Drop all new columns
            $table->dropColumn([
                'mobile', 'fax', 'designation',
                'city', 'state', 'postal_code', 'country', 'website',
                'category', 'status', 'registration_number', 'tax_id', 'description',
                'payment_terms', 'credit_limit', 'currency', 'lead_time', 'min_order_value', 'discount_terms',
                'bank_name', 'account_number', 'routing_number', 'swift_code', 'bank_address',
                'products_services', 'notes', 'certifications', 'insurance',
                'logo', 'business_license', 'tax_certificate', 'insurance_certificate', 'quality_certificates',
                'rating', 'total_reviews', 'total_orders', 'total_value'
            ]);

            // Rename 'company_name' back to 'name'
            $table->renameColumn('company_name', 'name');
        });
    }
};
