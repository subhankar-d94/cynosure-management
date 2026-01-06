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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->after('sku');
            $table->integer('reorder_level')->default(10)->after('stock_quantity');
            $table->decimal('cost_per_unit', 10, 2)->nullable()->after('reorder_level');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null')->after('cost_per_unit');

            // Add indexes for better query performance
            $table->index('stock_quantity');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropIndex(['stock_quantity']);
            $table->dropIndex(['supplier_id']);
            $table->dropColumn(['stock_quantity', 'reorder_level', 'cost_per_unit', 'supplier_id']);
        });
    }
};
