@extends('layouts.app')

@section('title', 'Edit Inventory Settings - ' . $inventory->product->name)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inventory.show', $inventory) }}">{{ $inventory->product->name }}</a></li>
        <li class="breadcrumb-item active">Edit Settings</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Edit Inventory Settings</h2>
                    <p class="text-muted mb-0">Update inventory configuration for {{ $inventory->product->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="inventoryForm" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Main Settings -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Inventory Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Changing these settings will affect stock calculations and reorder alerts.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="reorder_level" class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="reorder_level" name="reorder_level"
                                       value="{{ old('reorder_level', $inventory->reorder_level) }}" min="0" required>
                                <div class="form-text">Alert when stock reaches this level</div>
                                <div class="invalid-feedback">Please provide a valid reorder level.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="cost_per_unit" class="form-label">Cost per Unit (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="cost_per_unit" name="cost_per_unit"
                                       value="{{ old('cost_per_unit', $inventory->cost_per_unit) }}" step="0.01" min="0" required>
                                <div class="form-text">Used for inventory valuation</div>
                                <div class="invalid-feedback">Please provide a valid cost per unit.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="supplier_id" class="form-label">Primary Supplier</label>
                                <select class="form-select" id="supplier_id" name="supplier_id">
                                    <option value="">Select Supplier</option>
                                    @if(isset($suppliers))
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                    {{ old('supplier_id', $inventory->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-text">Default supplier for this product</div>
                            </div>

                            <div class="col-md-6">
                                <label for="location" class="form-label">Storage Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                       value="{{ old('location', $inventory->location ?? '') }}"
                                       placeholder="e.g., Warehouse A, Shelf B-3">
                                <div class="form-text">Physical location of the inventory</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-sliders me-2"></i>Advanced Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="max_stock_level" class="form-label">Maximum Stock Level</label>
                                <input type="number" class="form-control" id="max_stock_level" name="max_stock_level"
                                       value="{{ old('max_stock_level', $inventory->max_stock_level ?? '') }}" min="0">
                                <div class="form-text">Maximum recommended stock quantity</div>
                            </div>

                            <div class="col-md-6">
                                <label for="lead_time_days" class="form-label">Lead Time (Days)</label>
                                <input type="number" class="form-control" id="lead_time_days" name="lead_time_days"
                                       value="{{ old('lead_time_days', $inventory->lead_time_days ?? '') }}" min="0">
                                <div class="form-text">Days to receive stock from supplier</div>
                            </div>

                            <div class="col-md-6">
                                <label for="safety_stock" class="form-label">Safety Stock</label>
                                <input type="number" class="form-control" id="safety_stock" name="safety_stock"
                                       value="{{ old('safety_stock', $inventory->safety_stock ?? '') }}" min="0">
                                <div class="form-text">Buffer stock for unexpected demand</div>
                            </div>

                            <div class="col-md-6">
                                <label for="abc_classification" class="form-label">ABC Classification</label>
                                <select class="form-select" id="abc_classification" name="abc_classification">
                                    <option value="">Not Classified</option>
                                    <option value="A" {{ old('abc_classification', $inventory->abc_classification ?? '') == 'A' ? 'selected' : '' }}>A - High Value</option>
                                    <option value="B" {{ old('abc_classification', $inventory->abc_classification ?? '') == 'B' ? 'selected' : '' }}>B - Medium Value</option>
                                    <option value="C" {{ old('abc_classification', $inventory->abc_classification ?? '') == 'C' ? 'selected' : '' }}>C - Low Value</option>
                                </select>
                                <div class="form-text">Inventory classification for prioritization</div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="track_serials" name="track_serials"
                                           value="1" {{ old('track_serials', $inventory->track_serials ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="track_serials">
                                        Track Serial Numbers
                                    </label>
                                    <div class="form-text">Enable tracking of individual item serial numbers</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="allow_negative" name="allow_negative"
                                           value="1" {{ old('allow_negative', $inventory->allow_negative ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_negative">
                                        Allow Negative Stock
                                    </label>
                                    <div class="form-text">Allow stock levels to go below zero</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auto_reorder" name="auto_reorder"
                                           value="1" {{ old('auto_reorder', $inventory->auto_reorder ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_reorder">
                                        Enable Auto-Reorder
                                    </label>
                                    <div class="form-text">Automatically create purchase orders when stock is low</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Tracking -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>Stock Tracking Preferences
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="valuation_method" class="form-label">Valuation Method</label>
                                <select class="form-select" id="valuation_method" name="valuation_method">
                                    <option value="fifo" {{ old('valuation_method', $inventory->valuation_method ?? 'fifo') == 'fifo' ? 'selected' : '' }}>FIFO (First In, First Out)</option>
                                    <option value="lifo" {{ old('valuation_method', $inventory->valuation_method ?? '') == 'lifo' ? 'selected' : '' }}>LIFO (Last In, First Out)</option>
                                    <option value="average" {{ old('valuation_method', $inventory->valuation_method ?? '') == 'average' ? 'selected' : '' }}>Weighted Average</option>
                                    <option value="standard" {{ old('valuation_method', $inventory->valuation_method ?? '') == 'standard' ? 'selected' : '' }}>Standard Cost</option>
                                </select>
                                <div class="form-text">Method for calculating inventory value</div>
                            </div>

                            <div class="col-md-6">
                                <label for="unit_of_measure" class="form-label">Unit of Measure</label>
                                <select class="form-select" id="unit_of_measure" name="unit_of_measure">
                                    <option value="piece" {{ old('unit_of_measure', $inventory->unit_of_measure ?? 'piece') == 'piece' ? 'selected' : '' }}>Piece</option>
                                    <option value="kg" {{ old('unit_of_measure', $inventory->unit_of_measure ?? '') == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                    <option value="liter" {{ old('unit_of_measure', $inventory->unit_of_measure ?? '') == 'liter' ? 'selected' : '' }}>Liter</option>
                                    <option value="meter" {{ old('unit_of_measure', $inventory->unit_of_measure ?? '') == 'meter' ? 'selected' : '' }}>Meter</option>
                                    <option value="box" {{ old('unit_of_measure', $inventory->unit_of_measure ?? '') == 'box' ? 'selected' : '' }}>Box</option>
                                    <option value="pack" {{ old('unit_of_measure', $inventory->unit_of_measure ?? '') == 'pack' ? 'selected' : '' }}>Pack</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                          placeholder="Additional notes about this inventory item...">{{ old('notes', $inventory->notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Update Settings
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="saveAndContinue()">
                                <i class="bi bi-check2-all me-1"></i>Save & Continue Editing
                            </button>
                            <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle me-1"></i>Current Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Current Stock</label>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Quantity:</span>
                                    <strong class="{{ $inventory->quantity_in_stock <= $inventory->reorder_level ? 'text-warning' : 'text-success' }}">
                                        {{ $inventory->quantity_in_stock }} units
                                    </strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Current Value:</span>
                                    <strong class="text-success">
                                        ₹{{ number_format($inventory->quantity_in_stock * $inventory->cost_per_unit, 2) }}
                                    </strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Last Updated:</span>
                                    <span>{{ $inventory->updated_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    @php
                                        $status = $inventory->quantity_in_stock == 0 ? 'Out of Stock' :
                                                 ($inventory->quantity_in_stock <= $inventory->reorder_level ? 'Low Stock' : 'In Stock');
                                        $statusClass = $inventory->quantity_in_stock == 0 ? 'danger' :
                                                      ($inventory->quantity_in_stock <= $inventory->reorder_level ? 'warning' : 'success');
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ $status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calculation Preview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-calculator me-1"></i>Calculation Preview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2" id="calculationPreview">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Stock Value:</span>
                                    <strong id="previewStockValue">₹{{ number_format($inventory->quantity_in_stock * $inventory->cost_per_unit, 2) }}</strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Reorder Value:</span>
                                    <strong id="previewReorderValue">₹{{ number_format($inventory->reorder_level * $inventory->cost_per_unit, 2) }}</strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Max Stock Value:</span>
                                    <strong id="previewMaxValue">₹0</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Information -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-box me-1"></i>Product Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2">{{ $inventory->product->name }}</h6>
                        <p class="text-muted small mb-2">SKU: {{ $inventory->product->sku }}</p>
                        <p class="text-muted small mb-3">
                            Category: {{ $inventory->product->category->name ?? 'Uncategorized' }}
                        </p>
                        <a href="{{ route('products.show', $inventory->product) }}" class="btn btn-sm btn-outline-primary">
                            View Product Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="text-success mb-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                </div>
                <h4 class="mb-3">Settings Updated Successfully!</h4>
                <p class="text-muted mb-4">Your inventory settings have been saved.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-primary">View Details</a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">Back to Inventory</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update calculations when values change
    $('#cost_per_unit, #reorder_level, #max_stock_level').on('input', updateCalculations);

    // Initial calculation
    updateCalculations();

    // Form submission
    $('#inventoryForm').on('submit', function(e) {
        e.preventDefault();

        if (this.checkValidity()) {
            updateInventory();
        } else {
            this.classList.add('was-validated');
        }
    });
});

function updateCalculations() {
    const currentStock = {{ $inventory->quantity_in_stock }};
    const costPerUnit = parseFloat($('#cost_per_unit').val()) || 0;
    const reorderLevel = parseInt($('#reorder_level').val()) || 0;
    const maxStockLevel = parseInt($('#max_stock_level').val()) || 0;

    const stockValue = currentStock * costPerUnit;
    const reorderValue = reorderLevel * costPerUnit;
    const maxValue = maxStockLevel * costPerUnit;

    $('#previewStockValue').text('₹' + stockValue.toLocaleString());
    $('#previewReorderValue').text('₹' + reorderValue.toLocaleString());
    $('#previewMaxValue').text('₹' + maxValue.toLocaleString());
}

function updateInventory() {
    const formData = new FormData($('#inventoryForm')[0]);

    // Show loading state
    const submitBtn = $('#inventoryForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Updating...');

    $.ajax({
        url: '{{ route("inventory.update", $inventory) }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            $('#successModal').modal('show');
        } else {
            showAlert(response.message || 'Error updating inventory', 'danger');
            if (response.errors) {
                displayValidationErrors(response.errors);
            }
        }
    })
    .fail(function(xhr) {
        if (xhr.status === 422) {
            const response = xhr.responseJSON;
            if (response.errors) {
                displayValidationErrors(response.errors);
            }
            showAlert('Please check the form for errors', 'danger');
        } else {
            showAlert('Error updating inventory. Please try again.', 'danger');
        }
    })
    .always(function() {
        submitBtn.prop('disabled', false).html(originalText);
    });
}

function saveAndContinue() {
    updateInventory();
    // Keep the form open for continued editing
    $('#successModal').on('hidden.bs.modal', function() {
        showAlert('Settings updated successfully! You can continue editing.', 'success');
    });
}

function displayValidationErrors(errors) {
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    // Display new errors
    Object.keys(errors).forEach(function(field) {
        const input = $(`[name="${field}"]`);
        if (input.length) {
            input.addClass('is-invalid');

            // Create or update error message
            let feedback = input.siblings('.invalid-feedback');
            if (feedback.length === 0) {
                feedback = $('<div class="invalid-feedback"></div>');
                input.after(feedback);
            }
            feedback.text(errors[field][0]).show();
        }
    });
}

function showAlert(message, type) {
    const alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.container-fluid').prepend(alert);

    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush