<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * WARNING: This migration is destructive and irreversible!
     * Make sure to:
     * 1. Create a database backup before running
     * 2. Verify data migration in migration 2 was successful
     * 3. Test on staging environment first
     */
    public function up(): void
    {
        // Drop inventories table
        Schema::dropIfExists('inventories');

        // Drop raw_materials table
        Schema::dropIfExists('raw_materials');
    }

    /**
     * Reverse the migrations.
     *
     * This migration cannot be reversed. If rollback is needed, restore from database backup.
     */
    public function down(): void
    {
        // This migration is not reversible
        // Restore from database backup if needed
    }
};
