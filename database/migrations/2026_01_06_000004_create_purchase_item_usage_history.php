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
        Schema::create('purchase_item_usage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_item_id')->constrained()->onDelete('cascade');
            $table->date('usage_date');
            $table->decimal('quantity_used', 10, 3);
            $table->decimal('quantity_remaining', 10, 3);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['purchase_item_id', 'usage_date']);
            $table->index('usage_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_item_usage_history');
    }
};
