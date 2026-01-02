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
            // Drop the old foreign key constraint that references 'purchases' table
            $table->dropForeign(['purchase_id']);

            // Add new foreign key constraint that references 'purchase_orders' table
            $table->foreign('purchase_id')
                  ->references('id')
                  ->on('purchase_orders')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['purchase_id']);

            // Restore the old foreign key that references 'purchases' table
            $table->foreign('purchase_id')
                  ->references('id')
                  ->on('purchases')
                  ->onDelete('cascade');
        });
    }
};
