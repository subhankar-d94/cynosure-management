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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->enum('status', ['generated', 'sent', 'paid'])->default('generated');
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['status']);
            $table->index(['invoice_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
