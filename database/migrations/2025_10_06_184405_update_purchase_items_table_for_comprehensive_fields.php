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
        Schema::table('purchase_items', function (Blueprint $table) {
            // Add new fields for comprehensive purchase item management
            $table->text('description')->nullable()->after('purchase_id');
            $table->string('sku', 100)->nullable()->after('description');
            $table->string('unit', 50)->default('PCS')->after('sku');
            $table->decimal('unit_price', 15, 2)->nullable()->after('unit_cost');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('unit_price');
            $table->decimal('received_quantity', 15, 3)->default(0)->after('quantity');
            $table->text('notes')->nullable()->after('subtotal');

            // Add indexes for better performance
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropIndex(['sku']);
            $table->dropColumn([
                'description',
                'sku',
                'unit',
                'unit_price',
                'tax_rate',
                'received_quantity',
                'notes'
            ]);
        });
    }
};
