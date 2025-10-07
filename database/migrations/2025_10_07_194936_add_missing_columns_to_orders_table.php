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
            // Add priority column
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('delivery_date');

            // Add financial breakdown columns
            $table->decimal('subtotal', 10, 2)->default(0)->after('total_amount');
            $table->decimal('discount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('tax', 10, 2)->default(0)->after('discount');

            // Add expected delivery column (separate from actual delivery_date)
            $table->date('expected_delivery')->nullable()->after('delivery_date');

            // Add customer info columns for walk-in customers
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->text('customer_address')->nullable()->after('customer_email');

            // Make customer_id nullable for walk-in customers
            $table->foreignId('customer_id')->nullable()->change();

            // Add indexes for new columns
            $table->index(['priority']);
            $table->index(['expected_delivery']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['priority']);
            $table->dropIndex(['expected_delivery']);

            $table->dropColumn([
                'priority',
                'subtotal',
                'discount',
                'tax',
                'expected_delivery',
                'customer_name',
                'customer_phone',
                'customer_email',
                'customer_address'
            ]);

            // Revert customer_id to not nullable
            $table->foreignId('customer_id')->nullable(false)->change();
        });
    }
};
