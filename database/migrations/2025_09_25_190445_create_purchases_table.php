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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('purchase_date');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['ordered', 'received', 'paid'])->default('ordered');
            $table->timestamps();

            $table->index(['supplier_id']);
            $table->index(['status']);
            $table->index(['purchase_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
