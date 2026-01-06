<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if inventories table exists
        if (!Schema::hasTable('inventories')) {
            return;
        }

        // Migrate data from inventories to products
        $inventories = DB::table('inventories')->get();

        foreach ($inventories as $inventory) {
            DB::table('products')
                ->where('id', $inventory->product_id)
                ->update([
                    'stock_quantity' => $inventory->quantity_in_stock ?? 0,
                    'reorder_level' => $inventory->reorder_level ?? 10,
                    'cost_per_unit' => $inventory->cost_per_unit,
                    'supplier_id' => $inventory->supplier_id,
                ]);
        }

        // Set default stock_quantity = 0 for products that don't have inventory records
        DB::table('products')
            ->whereNotIn('id', function ($query) {
                $query->select('product_id')
                    ->from('inventories');
            })
            ->update([
                'stock_quantity' => 0,
                'reorder_level' => 10,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we don't want to recreate inventory table
        // If rollback is needed, restore from database backup
    }
};
