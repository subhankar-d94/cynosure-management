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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('purchase_order_number')->unique();
            $table->string('reference_number')->nullable();
            $table->date('purchase_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();

            // Status and Priority
            $table->string('status', 50)->default('draft');
            $table->string('priority', 20)->default('medium');

            // Financial Information
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('INR');

            // Payment Information
            $table->string('payment_terms')->default('Net 30');
            $table->string('payment_status', 20)->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('bank_reference')->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->date('payment_due_date')->nullable();

            // Delivery Information
            $table->string('delivery_terms')->default('Ex-Works');
            $table->text('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state')->nullable();
            $table->string('delivery_pincode', 10)->nullable();
            $table->string('delivery_country')->default('India');

            // Notes and Instructions
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('terms_conditions')->nullable();

            // Approval Workflow
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->decimal('approval_limit', 15, 2)->nullable();

            // User Tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            // Organization Information
            $table->string('purchase_type', 50)->default('standard');
            $table->string('department')->nullable();
            $table->string('project_code')->nullable();
            $table->string('budget_code')->nullable();

            // Flags
            $table->boolean('urgent')->default(false);

            // Version Control
            $table->integer('version')->default(1);
            $table->foreignId('parent_id')->nullable()->constrained('purchase_orders')->onDelete('set null');

            // Cancellation Information
            $table->text('cancelled_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('cancelled_at')->nullable();

            // Receiving Information
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('received_at')->nullable();

            // Invoice Information
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_amount', 15, 2)->nullable();

            // Quality Control
            $table->string('quality_check_status', 20)->default('pending');
            $table->foreignId('quality_checked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('quality_checked_at')->nullable();
            $table->text('quality_notes')->nullable();

            // File Attachments and Tags
            $table->json('attachments')->nullable();
            $table->json('tags')->nullable();

            $table->timestamps();

            // Indexes for better performance
            $table->index(['supplier_id', 'status']);
            $table->index(['purchase_date', 'status']);
            $table->index(['expected_delivery_date', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['created_by', 'status']);
            $table->index('purchase_order_number');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
