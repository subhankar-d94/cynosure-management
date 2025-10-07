<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->nullable()->after('id');
        });

        // Generate order numbers for existing orders
        $orders = DB::table('orders')->whereNull('order_number')->orWhere('order_number', '')->get();
        foreach ($orders as $order) {
            $orderNumber = 'ORD-' . date('Y') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
            DB::table('orders')->where('id', $order->id)->update(['order_number' => $orderNumber]);
        }

        // Now add the unique constraint
        Schema::table('orders', function (Blueprint $table) {
            $table->unique('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};
