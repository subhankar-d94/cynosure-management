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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('delhivery_waybill')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('status')->default('pending');
            $table->date('pickup_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->json('delhivery_response')->nullable();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['status']);
            $table->index(['tracking_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
