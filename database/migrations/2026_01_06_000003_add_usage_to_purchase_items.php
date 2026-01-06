<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('used_quantity', 10, 3)->default(0)->after('received_quantity');
            $table->decimal('remaining_quantity', 10, 3)->default(0)->after('used_quantity');

            $table->index('used_quantity');
        });

        // Update existing records: remaining_quantity = received_quantity - used_quantity
        DB::table('purchase_items')->update([
            'remaining_quantity' => DB::raw('received_quantity - used_quantity')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropIndex(['used_quantity']);
            $table->dropColumn(['used_quantity', 'remaining_quantity']);
        });
    }
};
