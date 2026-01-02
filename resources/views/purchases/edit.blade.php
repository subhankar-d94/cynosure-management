@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Purchase Order</h1>
            <p class="text-muted">{{ $purchase->purchase_order_number }}</p>
        </div>
        <div>
            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Details
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Purchase Order Form -->
    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror"
                                    name="supplier_id" id="supplierSelect" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Order Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('order_date') is-invalid @enderror"
                                   name="order_date"
                                   value="{{ old('order_date', date('Y-m-d', strtotime($purchase->purchase_date))) }}"
                                   required>
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    name="status" required>
                                <option value="draft" {{ old('status', $purchase->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending" {{ old('status', $purchase->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $purchase->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="completed" {{ old('status', $purchase->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $purchase->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Items to Purchase</h5>
                <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
            <div class="card-body">
                <div id="itemsContainer">
                    @foreach($purchase->items as $index => $item)
                    <div class="item-row card mb-3" data-index="{{ $index }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label">Item Description <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control item-description"
                                           name="items[{{ $index }}][description]"
                                           value="{{ $item->description ?? $item->material_name }}"
                                           required placeholder="Enter item description">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control item-quantity"
                                           name="items[{{ $index }}][quantity]"
                                           value="{{ $item->quantity }}"
                                           min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control item-price"
                                           name="items[{{ $index }}][unit_price]"
                                           value="{{ $item->unit_price ?? $item->unit_cost }}"
                                           min="0" step="0.01" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-control item-total" readonly
                                           value="₹{{ number_format($item->quantity * ($item->unit_price ?? $item->unit_cost), 2) }}">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-item" title="Remove Item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table">
                            <tr>
                                <td class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end" id="subtotalDisplay">₹{{ number_format($purchase->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>Tax (0%):</strong></td>
                                <td class="text-end" id="taxDisplay">₹{{ number_format($purchase->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="table-active">
                                <td class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><h5 class="mb-0" id="totalDisplay">₹{{ number_format($purchase->total_amount, 2) }}</h5></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Purchase Order
            </button>
        </div>
    </form>
</div>

<!-- Item Row Template -->
<template id="itemTemplate">
    <div class="item-row card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <label class="form-label">Item Description <span class="text-danger">*</span></label>
                    <input type="text" class="form-control item-description" name="items[INDEX][description]" required placeholder="Enter item description">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control item-quantity" name="items[INDEX][quantity]" min="1" value="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                    <input type="number" class="form-control item-price" name="items[INDEX][unit_price]" min="0" step="0.01" value="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="text" class="form-control item-total" readonly value="₹0.00">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-item" title="Remove Item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let itemIndex = {{ $purchase->items->count() }};

    // Add item button
    $('#addItemBtn').click(function() {
        addItem();
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotals();
        } else {
            alert('You must have at least one item.');
        }
    });

    // Calculate item total on quantity or price change
    $(document).on('input', '.item-quantity, .item-price', function() {
        const row = $(this).closest('.item-row');
        calculateItemTotal(row);
        calculateTotals();
    });

    function addItem() {
        const template = $('#itemTemplate').html();
        const newItem = template.replace(/INDEX/g, itemIndex);
        $('#itemsContainer').append(newItem);
        itemIndex++;
    }

    function calculateItemTotal(row) {
        const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const total = quantity * price;
        row.find('.item-total').val('₹' + total.toFixed(2));
    }

    function calculateTotals() {
        let subtotal = 0;

        $('.item-row').each(function() {
            const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            subtotal += quantity * price;
        });

        const tax = 0; // No tax calculation
        const total = subtotal + tax;

        $('#subtotalDisplay').text('₹' + subtotal.toFixed(2));
        $('#taxDisplay').text('₹' + tax.toFixed(2));
        $('#totalDisplay').text('₹' + total.toFixed(2));
    }

    // Form validation
    $('#purchaseForm').submit(function(e) {
        if ($('.item-row').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the purchase order.');
            return false;
        }
    });

    // Calculate totals on page load
    calculateTotals();
});
</script>
@endpush

@push('styles')
<style>
.item-row {
    border: 1px solid #dee2e6;
}

.item-row .card-body {
    padding: 1rem;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table td {
    padding: 0.5rem;
}
</style>
@endpush
