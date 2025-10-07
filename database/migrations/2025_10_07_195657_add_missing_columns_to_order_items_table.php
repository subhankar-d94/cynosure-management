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
        Schema::table('order_items', function (Blueprint $table) {
            // Add product_name to store product name at time of order
            $table->string('product_name')->after('product_id');

            // Add price column (alias for unit_price for consistency with frontend)
            $table->decimal('price', 10, 2)->after('quantity');

            // Add discount column for item-level discounts
            $table->decimal('discount', 10, 2)->default(0)->after('price');

            // Add total column (alias for subtotal for consistency with frontend)
            $table->decimal('total', 10, 2)->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'product_name',
                'price',
                'discount',
                'total'
            ]);
        });
    }
};
