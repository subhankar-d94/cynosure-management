@extends('layouts.app')

@section('title', 'Create Purchase Order')

@push('styles')
<style>
    .purchase-form {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .form-section {
        padding: 25px;
        border-bottom: 1px solid #e9ecef;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 10px;
        color: #17a2b8;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23,162,184,0.25);
    }

    .required {
        color: #dc3545;
    }

    .btn-action {
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .items-table {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .item-row {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }

    .remove-item {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .add-item-btn {
        background: #28a745;
        border-color: #28a745;
        color: white;
        border-radius: 6px;
        padding: 10px 20px;
    }

    .add-item-btn:hover {
        background: #218838;
        border-color: #1e7e34;
        color: white;
    }

    .summary-card {
        background: #17a2b8;
        color: white;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .summary-row:last-child {
        margin-bottom: 0;
        border-top: 1px solid rgba(255,255,255,0.3);
        padding-top: 10px;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .supplier-preview {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        display: none;
    }

    .priority-selector {
        display: flex;
        gap: 10px;
    }

    .priority-option {
        flex: 1;
        text-align: center;
        padding: 10px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .priority-option.selected {
        border-color: #17a2b8;
        background: #e7f3ff;
    }

    .priority-high { border-color: #dc3545; }
    .priority-high.selected { border-color: #dc3545; background: #f8d7da; }
    .priority-medium { border-color: #ffc107; }
    .priority-medium.selected { border-color: #ffc107; background: #fff3cd; }
    .priority-low { border-color: #28a745; }
    .priority-low.selected { border-color: #28a745; background: #d4edda; }

    @media (max-width: 768px) {
        .purchase-form {
            margin: 10px;
        }

        .form-section {
            padding: 15px;
        }

        .priority-selector {
            flex-direction: column;
        }

        .item-row {
            padding: 10px;
        }
    }

    .po-number-display {
        background: #e7f3ff;
        border: 1px solid #17a2b8;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
        margin-bottom: 20px;
    }

    .po-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: #17a2b8;
    }

    .auto-suggest {
        position: relative;
    }

    .suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 6px 6px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    .suggestion-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }

    .suggestion-item:hover {
        background: #f8f9fa;
    }

    .suggestion-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Create Purchase Order</h1>
            <p class="text-muted">Create a new purchase order for supplier procurement</p>
        </div>
        <div>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Purchase Order Form -->
    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf

        <div class="purchase-form">
            <!-- PO Number Display -->
            <div class="form-section">
                <div class="po-number-display">
                    <div class="po-number" id="poNumber">PO-{{ date('Y') }}-{{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}</div>
                    <small class="text-muted">Purchase Order Number</small>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Basic Information
                </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Supplier <span class="required">*</span></label>
                            <div class="auto-suggest">
                                <select class="form-select" name="supplier_id" id="supplierSelect" required>
                                    <option value="">Select Supplier</option>
                                    <option value="1">TechCorp Solutions Ltd.</option>
                                    <option value="2">Global Manufacturing Inc.</option>
                                    <option value="3">Premium Services Ltd.</option>
                                    <option value="4">Industrial Supplies Co.</option>
                                    <option value="5">Digital Systems Group</option>
                                </select>
                            </div>
                            <div class="supplier-preview" id="supplierPreview">
                                <!-- Supplier details will be loaded here -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Purchase Type <span class="required">*</span></label>
                            <select class="form-select" name="purchase_type" required>
                                <option value="">Select Type</option>
                                <option value="materials">Raw Materials</option>
                                <option value="equipment">Equipment</option>
                                <option value="services">Services</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="software">Software/Licenses</option>
                                <option value="office_supplies">Office Supplies</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Order Date <span class="required">*</span></label>
                            <input type="date" class="form-control" name="order_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control" name="expected_delivery_date" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Currency</label>
                            <select class="form-select" name="currency">
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="CAD">CAD - Canadian Dollar</option>
                                <option value="INR">INR - Indian Rupee</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department">
                                <option value="">Select Department</option>
                                <option value="production">Production</option>
                                <option value="it">Information Technology</option>
                                <option value="hr">Human Resources</option>
                                <option value="finance">Finance</option>
                                <option value="marketing">Marketing</option>
                                <option value="operations">Operations</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Requested By</label>
                            <input type="text" class="form-control" name="requested_by" value="{{ auth()->user()->name ?? 'Current User' }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Priority Level</label>
                            <div class="priority-selector">
                                <div class="priority-option priority-low" data-priority="low">
                                    <i class="fas fa-arrow-down text-success"></i>
                                    <div><strong>Low</strong></div>
                                    <small>Standard delivery</small>
                                </div>
                                <div class="priority-option priority-medium selected" data-priority="medium">
                                    <i class="fas fa-minus text-warning"></i>
                                    <div><strong>Medium</strong></div>
                                    <small>Normal priority</small>
                                </div>
                                <div class="priority-option priority-high" data-priority="high">
                                    <i class="fas fa-arrow-up text-danger"></i>
                                    <div><strong>High</strong></div>
                                    <small>Urgent delivery</small>
                                </div>
                            </div>
                            <input type="hidden" name="priority" value="medium" id="priorityInput">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-shopping-cart"></i>
                    Items to Purchase
                </h4>

                <div class="items-table" id="itemsContainer">
                    <div class="item-row" data-item="1">
                        <button type="button" class="remove-item" onclick="removeItem(1)">
                            <i class="fas fa-times"></i>
                        </button>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Item Description <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="items[1][description]" placeholder="Enter item description" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Quantity <span class="required">*</span></label>
                                    <input type="number" class="form-control item-quantity" name="items[1][quantity]" min="1" value="1" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Unit Price <span class="required">*</span></label>
                                    <input type="number" class="form-control item-price" name="items[1][unit_price]" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Tax (%)</label>
                                    <input type="number" class="form-control item-tax" name="items[1][tax_rate]" step="0.01" min="0" max="100" value="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-control item-total" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" name="items[1][category]">
                                        <option value="">Select Category</option>
                                        <option value="raw_materials">Raw Materials</option>
                                        <option value="equipment">Equipment</option>
                                        <option value="software">Software</option>
                                        <option value="services">Services</option>
                                        <option value="supplies">Office Supplies</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Unit of Measure</label>
                                    <select class="form-select" name="items[1][unit]">
                                        <option value="pcs">Pieces</option>
                                        <option value="kg">Kilograms</option>
                                        <option value="lbs">Pounds</option>
                                        <option value="meters">Meters</option>
                                        <option value="liters">Liters</option>
                                        <option value="hours">Hours</option>
                                        <option value="sets">Sets</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Item Notes</label>
                                    <textarea class="form-control" name="items[1][notes]" rows="2" placeholder="Additional specifications or notes for this item"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn add-item-btn" onclick="addItem()">
                        <i class="fas fa-plus"></i> Add Another Item
                    </button>
                </div>

                <!-- Order Summary -->
                <div class="summary-card">
                    <h5 class="mb-3">Order Summary</h5>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotalAmount">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Tax:</span>
                        <span id="taxAmount">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="shippingAmount">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount:</span>
                        <span id="totalAmount">$0.00</span>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-clipboard-list"></i>
                    Additional Information
                </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Shipping Address</label>
                            <textarea class="form-control" name="shipping_address" rows="3" placeholder="Enter delivery address if different from company address"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Special Instructions</label>
                            <textarea class="form-control" name="special_instructions" rows="3" placeholder="Any special delivery or handling instructions"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Shipping Method</label>
                            <select class="form-select" name="shipping_method">
                                <option value="standard">Standard Shipping</option>
                                <option value="express">Express Shipping</option>
                                <option value="overnight">Overnight</option>
                                <option value="pickup">Supplier Pickup</option>
                                <option value="custom">Custom Arrangement</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Payment Terms</label>
                            <select class="form-select" name="payment_terms">
                                <option value="net_30">Net 30 Days</option>
                                <option value="net_15">Net 15 Days</option>
                                <option value="net_45">Net 45 Days</option>
                                <option value="net_60">Net 60 Days</option>
                                <option value="due_on_receipt">Due on Receipt</option>
                                <option value="prepaid">Prepaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Budget Code</label>
                            <input type="text" class="form-control" name="budget_code" placeholder="e.g., DEPT-2024-001">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Internal Notes</label>
                            <textarea class="form-control" name="internal_notes" rows="3" placeholder="Internal notes (not visible to supplier)"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="saveDraft()">
                            <i class="fas fa-save"></i> Save as Draft
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="previewOrder()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-info btn-action">
                            <i class="fas fa-paper-plane"></i> Submit Purchase Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let itemCounter = 1;

$(document).ready(function() {
    bindEventListeners();
    calculateTotals();
});

function bindEventListeners() {
    // Priority selection
    $('.priority-option').on('click', function() {
        $('.priority-option').removeClass('selected');
        $(this).addClass('selected');
        $('#priorityInput').val($(this).data('priority'));
    });

    // Supplier selection
    $('#supplierSelect').on('change', function() {
        const supplierId = $(this).val();
        if (supplierId) {
            loadSupplierDetails(supplierId);
        } else {
            $('#supplierPreview').hide();
        }
    });

    // Calculate totals when item values change
    $(document).on('input', '.item-quantity, .item-price, .item-tax', function() {
        calculateItemTotal($(this).closest('.item-row'));
        calculateTotals();
    });

    // Form submission
    $('#purchaseForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
}

function addItem() {
    itemCounter++;

    const newItem = `
        <div class="item-row" data-item="${itemCounter}">
            <button type="button" class="remove-item" onclick="removeItem(${itemCounter})">
                <i class="fas fa-times"></i>
            </button>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Item Description <span class="required">*</span></label>
                        <input type="text" class="form-control" name="items[${itemCounter}][description]" placeholder="Enter item description" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Quantity <span class="required">*</span></label>
                        <input type="number" class="form-control item-quantity" name="items[${itemCounter}][quantity]" min="1" value="1" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Unit Price <span class="required">*</span></label>
                        <input type="number" class="form-control item-price" name="items[${itemCounter}][unit_price]" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Tax (%)</label>
                        <input type="number" class="form-control item-tax" name="items[${itemCounter}][tax_rate]" step="0.01" min="0" max="100" value="0">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control item-total" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="items[${itemCounter}][category]">
                            <option value="">Select Category</option>
                            <option value="raw_materials">Raw Materials</option>
                            <option value="equipment">Equipment</option>
                            <option value="software">Software</option>
                            <option value="services">Services</option>
                            <option value="supplies">Office Supplies</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Unit of Measure</label>
                        <select class="form-select" name="items[${itemCounter}][unit]">
                            <option value="pcs">Pieces</option>
                            <option value="kg">Kilograms</option>
                            <option value="lbs">Pounds</option>
                            <option value="meters">Meters</option>
                            <option value="liters">Liters</option>
                            <option value="hours">Hours</option>
                            <option value="sets">Sets</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Item Notes</label>
                        <textarea class="form-control" name="items[${itemCounter}][notes]" rows="2" placeholder="Additional specifications or notes for this item"></textarea>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#itemsContainer').append(newItem);
}

function removeItem(itemId) {
    if ($('.item-row').length > 1) {
        $(`.item-row[data-item="${itemId}"]`).remove();
        calculateTotals();
    } else {
        alert('At least one item is required.');
    }
}

function calculateItemTotal(itemRow) {
    const quantity = parseFloat(itemRow.find('.item-quantity').val()) || 0;
    const price = parseFloat(itemRow.find('.item-price').val()) || 0;
    const taxRate = parseFloat(itemRow.find('.item-tax').val()) || 0;

    const subtotal = quantity * price;
    const tax = subtotal * (taxRate / 100);
    const total = subtotal + tax;

    itemRow.find('.item-total').val('$' + total.toFixed(2));
}

function calculateTotals() {
    let subtotal = 0;
    let totalTax = 0;

    $('.item-row').each(function() {
        const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
        const price = parseFloat($(this).find('.item-price').val()) || 0;
        const taxRate = parseFloat($(this).find('.item-tax').val()) || 0;

        const itemSubtotal = quantity * price;
        const itemTax = itemSubtotal * (taxRate / 100);

        subtotal += itemSubtotal;
        totalTax += itemTax;
    });

    const shipping = 0; // For now, assuming no shipping cost
    const total = subtotal + totalTax + shipping;

    $('#subtotalAmount').text('$' + subtotal.toFixed(2));
    $('#taxAmount').text('$' + totalTax.toFixed(2));
    $('#shippingAmount').text('$' + shipping.toFixed(2));
    $('#totalAmount').text('$' + total.toFixed(2));
}

function loadSupplierDetails(supplierId) {
    // Simulate loading supplier details
    const suppliers = {
        '1': {
            name: 'TechCorp Solutions Ltd.',
            contact: 'John Anderson',
            email: 'john.anderson@techcorp.com',
            phone: '+1 (555) 123-4567',
            terms: 'Net 30',
            rating: '4.2/5'
        },
        '2': {
            name: 'Global Manufacturing Inc.',
            contact: 'Sarah Wilson',
            email: 'sarah.wilson@globalmanuf.com',
            phone: '+1 (555) 987-6543',
            terms: 'Net 45',
            rating: '4.0/5'
        }
    };

    const supplier = suppliers[supplierId];
    if (supplier) {
        $('#supplierPreview').html(`
            <div class="row">
                <div class="col-md-6">
                    <strong>Contact:</strong> ${supplier.contact}<br>
                    <strong>Email:</strong> ${supplier.email}<br>
                    <strong>Phone:</strong> ${supplier.phone}
                </div>
                <div class="col-md-6">
                    <strong>Payment Terms:</strong> ${supplier.terms}<br>
                    <strong>Rating:</strong> ${supplier.rating}<br>
                    <strong>Status:</strong> <span class="text-success">Active</span>
                </div>
            </div>
        `).show();
    }
}

function validateForm() {
    // Basic validation
    let isValid = true;

    // Check if at least one item is added
    if ($('.item-row').length === 0) {
        alert('Please add at least one item to the purchase order.');
        isValid = false;
    }

    // Check if all required fields are filled
    $('#purchaseForm [required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    return isValid;
}

function saveDraft() {
    // Save form data as draft
    const formData = new FormData(document.getElementById('purchaseForm'));
    formData.append('status', 'draft');

    alert('Purchase order saved as draft.');
}

function previewOrder() {
    // Open preview in new window/modal
    if (validateForm()) {
        alert('Preview functionality would open a new window showing the formatted purchase order.');
    }
}

// Auto-save functionality
setInterval(function() {
    const formData = {};
    $('#purchaseForm input, #purchaseForm select, #purchaseForm textarea').each(function() {
        if (this.name && this.value) {
            formData[this.name] = this.value;
        }
    });

    localStorage.setItem('purchaseOrderDraft', JSON.stringify(formData));
}, 30000); // Auto-save every 30 seconds
</script>
@endpush