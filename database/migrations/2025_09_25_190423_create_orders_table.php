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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('order_date');
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('delivery_address_id')->nullable()->constrained('customer_addresses')->onDelete('set null');
            $table->decimal('delivery_charges', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('delivery_date')->nullable();
            $table->timestamps();

            $table->index(['customer_id']);
            $table->index(['status']);
            $table->index(['order_date']);
            $table->index(['delivery_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
