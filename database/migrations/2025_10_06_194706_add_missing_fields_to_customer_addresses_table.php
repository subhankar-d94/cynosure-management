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
        Schema::table('customer_addresses', function (Blueprint $table) {
            // Add missing fields
            $table->string('label')->nullable()->after('type');
            $table->string('street_address')->nullable()->after('label');

            // Update type enum to include 'both' option
            $table->enum('type', ['billing', 'shipping', 'both'])->change();

            // Rename pincode to postal_code
            $table->renameColumn('pincode', 'postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropColumn(['label', 'street_address']);
            $table->enum('type', ['billing', 'shipping'])->change();
            $table->renameColumn('postal_code', 'pincode');
        });
    }
};
