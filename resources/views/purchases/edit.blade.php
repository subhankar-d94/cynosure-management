@extends('layouts.app')

@section('title', 'Edit Purchase Order')

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

    .status-selector {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .status-option {
        flex: 1;
        text-align: center;
        padding: 10px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 120px;
    }

    .status-option.selected {
        border-color: #17a2b8;
        background: #e7f3ff;
    }

    .status-draft { border-color: #6c757d; }
    .status-draft.selected { border-color: #6c757d; background: #f8f9fa; }
    .status-pending { border-color: #007bff; }
    .status-pending.selected { border-color: #007bff; background: #cce7ff; }
    .status-approved { border-color: #28a745; }
    .status-approved.selected { border-color: #28a745; background: #d4edda; }
    .status-ordered { border-color: #17a2b8; }
    .status-ordered.selected { border-color: #17a2b8; background: #d1ecf1; }
    .status-received { border-color: #20c997; }
    .status-received.selected { border-color: #20c997; background: #c3e6cb; }
    .status-cancelled { border-color: #dc3545; }
    .status-cancelled.selected { border-color: #dc3545; background: #f8d7da; }

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

    .supplier-preview {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
    }

    @media (max-width: 768px) {
        .purchase-form {
            margin: 10px;
        }

        .form-section {
            padding: 15px;
        }

        .priority-selector, .status-selector {
            flex-direction: column;
        }

        .item-row {
            padding: 10px;
        }
    }

    .changes-indicator {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 20px;
        display: none;
    }

    .changes-indicator.show {
        display: block;
    }

    .version-history {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        margin-top: 15px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Purchase Order</h1>
            <p class="text-muted">Modify purchase order details and items</p>
        </div>
        <div>
            <a href="{{ route('purchases.show', $purchase->id ?? 1) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-eye"></i> View Order
            </a>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Changes Indicator -->
    <div class="changes-indicator" id="changesIndicator">
        <i class="fas fa-exclamation-triangle text-warning"></i>
        <strong>Unsaved Changes:</strong> You have made changes to this purchase order. Don't forget to save your changes.
    </div>

    <!-- Purchase Order Edit Form -->
    <form action="{{ route('purchases.update', $purchase->id ?? 1) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')

        <div class="purchase-form">
            <!-- PO Number Display -->
            <div class="form-section">
                <div class="po-number-display">
                    <div class="po-number">{{ $purchase->po_number ?? 'PO-2024-002' }}</div>
                    <small class="text-muted">Purchase Order Number (Read Only)</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Current Status</label>
                            <div class="status-selector">
                                <div class="status-option status-draft {{ ($purchase->status ?? 'ordered') == 'draft' ? 'selected' : '' }}" data-status="draft">
                                    <i class="fas fa-edit text-secondary"></i>
                                    <div><strong>Draft</strong></div>
                                </div>
                                <div class="status-option status-pending {{ ($purchase->status ?? 'ordered') == 'pending' ? 'selected' : '' }}" data-status="pending">
                                    <i class="fas fa-clock text-primary"></i>
                                    <div><strong>Pending</strong></div>
                                </div>
                                <div class="status-option status-approved {{ ($purchase->status ?? 'ordered') == 'approved' ? 'selected' : '' }}" data-status="approved">
                                    <i class="fas fa-check text-success"></i>
                                    <div><strong>Approved</strong></div>
                                </div>
                                <div class="status-option status-ordered {{ ($purchase->status ?? 'ordered') == 'ordered' ? 'selected' : '' }}" data-status="ordered">
                                    <i class="fas fa-shopping-cart text-info"></i>
                                    <div><strong>Ordered</strong></div>
                                </div>
                                <div class="status-option status-received {{ ($purchase->status ?? 'ordered') == 'received' ? 'selected' : '' }}" data-status="received">
                                    <i class="fas fa-box text-success"></i>
                                    <div><strong>Received</strong></div>
                                </div>
                                <div class="status-option status-cancelled {{ ($purchase->status ?? 'ordered') == 'cancelled' ? 'selected' : '' }}" data-status="cancelled">
                                    <i class="fas fa-times text-danger"></i>
                                    <div><strong>Cancelled</strong></div>
                                </div>
                            </div>
                            <input type="hidden" name="status" value="{{ $purchase->status ?? 'ordered' }}" id="statusInput">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Last Modified</label>
                            <input type="text" class="form-control" value="{{ $purchase->updated_at ? $purchase->updated_at->format('M d, Y g:i A') : 'Feb 05, 2024 10:30 AM' }}" readonly>
                        </div>
                    </div>
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
                            <select class="form-select" name="supplier_id" id="supplierSelect" required>
                                <option value="">Select Supplier</option>
                                <option value="1" {{ ($purchase->supplier_id ?? 2) == 1 ? 'selected' : '' }}>TechCorp Solutions Ltd.</option>
                                <option value="2" {{ ($purchase->supplier_id ?? 2) == 2 ? 'selected' : '' }}>Global Manufacturing Inc.</option>
                                <option value="3" {{ ($purchase->supplier_id ?? 2) == 3 ? 'selected' : '' }}>Premium Services Ltd.</option>
                                <option value="4" {{ ($purchase->supplier_id ?? 2) == 4 ? 'selected' : '' }}>Industrial Supplies Co.</option>
                                <option value="5" {{ ($purchase->supplier_id ?? 2) == 5 ? 'selected' : '' }}>Digital Systems Group</option>
                            </select>
                            <div class="supplier-preview" id="supplierPreview">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Contact:</strong> Sarah Wilson<br>
                                        <strong>Email:</strong> sarah.wilson@globalmanuf.com<br>
                                        <strong>Phone:</strong> +1 (555) 987-6543
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Payment Terms:</strong> Net 45<br>
                                        <strong>Rating:</strong> 4.0/5<br>
                                        <strong>Status:</strong> <span class="text-success">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Purchase Type <span class="required">*</span></label>
                            <select class="form-select" name="purchase_type" required>
                                <option value="">Select Type</option>
                                <option value="materials" {{ ($purchase->purchase_type ?? 'materials') == 'materials' ? 'selected' : '' }}>Raw Materials</option>
                                <option value="equipment" {{ ($purchase->purchase_type ?? 'materials') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                <option value="services" {{ ($purchase->purchase_type ?? 'materials') == 'services' ? 'selected' : '' }}>Services</option>
                                <option value="maintenance" {{ ($purchase->purchase_type ?? 'materials') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="software" {{ ($purchase->purchase_type ?? 'materials') == 'software' ? 'selected' : '' }}>Software/Licenses</option>
                                <option value="office_supplies" {{ ($purchase->purchase_type ?? 'materials') == 'office_supplies' ? 'selected' : '' }}>Office Supplies</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Order Date <span class="required">*</span></label>
                            <input type="date" class="form-control" name="order_date" value="{{ $purchase->order_date ?? '2024-02-01' }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control" name="expected_delivery_date" value="{{ $purchase->expected_delivery_date ?? '2024-02-20' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Currency</label>
                            <select class="form-select" name="currency">
                                <option value="USD" {{ ($purchase->currency ?? 'USD') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ ($purchase->currency ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ ($purchase->currency ?? 'USD') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="CAD" {{ ($purchase->currency ?? 'USD') == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                <option value="INR" {{ ($purchase->currency ?? 'USD') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
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
                                <option value="production" {{ ($purchase->department ?? 'production') == 'production' ? 'selected' : '' }}>Production</option>
                                <option value="it" {{ ($purchase->department ?? 'production') == 'it' ? 'selected' : '' }}>Information Technology</option>
                                <option value="hr" {{ ($purchase->department ?? 'production') == 'hr' ? 'selected' : '' }}>Human Resources</option>
                                <option value="finance" {{ ($purchase->department ?? 'production') == 'finance' ? 'selected' : '' }}>Finance</option>
                                <option value="marketing" {{ ($purchase->department ?? 'production') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="operations" {{ ($purchase->department ?? 'production') == 'operations' ? 'selected' : '' }}>Operations</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Requested By</label>
                            <input type="text" class="form-control" name="requested_by" value="{{ $purchase->requested_by ?? 'Sarah Johnson' }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Priority Level</label>
                            <div class="priority-selector">
                                <div class="priority-option priority-low {{ ($purchase->priority ?? 'medium') == 'low' ? 'selected' : '' }}" data-priority="low">
                                    <i class="fas fa-arrow-down text-success"></i>
                                    <div><strong>Low</strong></div>
                                    <small>Standard delivery</small>
                                </div>
                                <div class="priority-option priority-medium {{ ($purchase->priority ?? 'medium') == 'medium' ? 'selected' : '' }}" data-priority="medium">
                                    <i class="fas fa-minus text-warning"></i>
                                    <div><strong>Medium</strong></div>
                                    <small>Normal priority</small>
                                </div>
                                <div class="priority-option priority-high {{ ($purchase->priority ?? 'medium') == 'high' ? 'selected' : '' }}" data-priority="high">
                                    <i class="fas fa-arrow-up text-danger"></i>
                                    <div><strong>High</strong></div>
                                    <small>Urgent delivery</small>
                                </div>
                            </div>
                            <input type="hidden" name="priority" value="{{ $purchase->priority ?? 'medium' }}" id="priorityInput">
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
                    @if(isset($purchase->items) && $purchase->items->count() > 0)
                        @foreach($purchase->items as $index => $item)
                        <div class="item-row" data-item="{{ $index + 1 }}">
                            <button type="button" class="remove-item" onclick="removeItem({{ $index + 1 }})">
                                <i class="fas fa-times"></i>
                            </button>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Item Description <span class="required">*</span></label>
                                        <input type="text" class="form-control" name="items[{{ $index + 1 }}][description]" value="{{ $item->description }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Quantity <span class="required">*</span></label>
                                        <input type="number" class="form-control item-quantity" name="items[{{ $index + 1 }}][quantity]" min="1" value="{{ $item->quantity }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Unit Price <span class="required">*</span></label>
                                        <input type="number" class="form-control item-price" name="items[{{ $index + 1 }}][unit_price]" step="0.01" min="0" value="{{ $item->unit_price }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Tax (%)</label>
                                        <input type="number" class="form-control item-tax" name="items[{{ $index + 1 }}][tax_rate]" step="0.01" min="0" max="100" value="{{ $item->tax_rate ?? 8 }}">
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
                                        <select class="form-select" name="items[{{ $index + 1 }}][category]">
                                            <option value="">Select Category</option>
                                            <option value="raw_materials" {{ ($item->category ?? '') == 'raw_materials' ? 'selected' : '' }}>Raw Materials</option>
                                            <option value="equipment" {{ ($item->category ?? '') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                            <option value="software" {{ ($item->category ?? '') == 'software' ? 'selected' : '' }}>Software</option>
                                            <option value="services" {{ ($item->category ?? '') == 'services' ? 'selected' : '' }}>Services</option>
                                            <option value="supplies" {{ ($item->category ?? '') == 'supplies' ? 'selected' : '' }}>Office Supplies</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Unit of Measure</label>
                                        <select class="form-select" name="items[{{ $index + 1 }}][unit]">
                                            <option value="pcs" {{ ($item->unit ?? 'kg') == 'pcs' ? 'selected' : '' }}>Pieces</option>
                                            <option value="kg" {{ ($item->unit ?? 'kg') == 'kg' ? 'selected' : '' }}>Kilograms</option>
                                            <option value="lbs" {{ ($item->unit ?? 'kg') == 'lbs' ? 'selected' : '' }}>Pounds</option>
                                            <option value="meters" {{ ($item->unit ?? 'kg') == 'meters' ? 'selected' : '' }}>Meters</option>
                                            <option value="liters" {{ ($item->unit ?? 'kg') == 'liters' ? 'selected' : '' }}>Liters</option>
                                            <option value="hours" {{ ($item->unit ?? 'kg') == 'hours' ? 'selected' : '' }}>Hours</option>
                                            <option value="sets" {{ ($item->unit ?? 'kg') == 'sets' ? 'selected' : '' }}>Sets</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Item Notes</label>
                                        <textarea class="form-control" name="items[{{ $index + 1 }}][notes]" rows="2">{{ $item->notes ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <!-- Default items for demo -->
                        <div class="item-row" data-item="1">
                            <button type="button" class="remove-item" onclick="removeItem(1)">
                                <i class="fas fa-times"></i>
                            </button>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Item Description <span class="required">*</span></label>
                                        <input type="text" class="form-control" name="items[1][description]" value="Steel Components - Grade A" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Quantity <span class="required">*</span></label>
                                        <input type="number" class="form-control item-quantity" name="items[1][quantity]" min="1" value="500" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Unit Price <span class="required">*</span></label>
                                        <input type="number" class="form-control item-price" name="items[1][unit_price]" step="0.01" min="0" value="35.00" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Tax (%)</label>
                                        <input type="number" class="form-control item-tax" name="items[1][tax_rate]" step="0.01" min="0" max="100" value="8">
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
                                            <option value="raw_materials" selected>Raw Materials</option>
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
                                            <option value="kg" selected>Kilograms</option>
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
                                        <textarea class="form-control" name="items[1][notes]" rows="2">High-grade steel for manufacturing</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="item-row" data-item="2">
                            <button type="button" class="remove-item" onclick="removeItem(2)">
                                <i class="fas fa-times"></i>
                            </button>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Item Description <span class="required">*</span></label>
                                        <input type="text" class="form-control" name="items[2][description]" value="Plastic Components - Injection Molded" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Quantity <span class="required">*</span></label>
                                        <input type="number" class="form-control item-quantity" name="items[2][quantity]" min="1" value="200" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Unit Price <span class="required">*</span></label>
                                        <input type="number" class="form-control item-price" name="items[2][unit_price]" step="0.01" min="0" value="125.00" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Tax (%)</label>
                                        <input type="number" class="form-control item-tax" name="items[2][tax_rate]" step="0.01" min="0" max="100" value="8">
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
                                        <select class="form-select" name="items[2][category]">
                                            <option value="">Select Category</option>
                                            <option value="raw_materials" selected>Raw Materials</option>
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
                                        <select class="form-select" name="items[2][unit]">
                                            <option value="pcs" selected>Pieces</option>
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
                                        <textarea class="form-control" name="items[2][notes]" rows="2">Custom molded plastic parts</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
                        <span id="subtotalAmount">$42,500.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Tax:</span>
                        <span id="taxAmount">$3,400.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="shippingAmount">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount:</span>
                        <span id="totalAmount">$45,900.00</span>
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
                            <textarea class="form-control" name="shipping_address" rows="3">{{ $purchase->shipping_address ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Special Instructions</label>
                            <textarea class="form-control" name="special_instructions" rows="3">{{ $purchase->special_instructions ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Shipping Method</label>
                            <select class="form-select" name="shipping_method">
                                <option value="standard" {{ ($purchase->shipping_method ?? 'standard') == 'standard' ? 'selected' : '' }}>Standard Shipping</option>
                                <option value="express" {{ ($purchase->shipping_method ?? 'standard') == 'express' ? 'selected' : '' }}>Express Shipping</option>
                                <option value="overnight" {{ ($purchase->shipping_method ?? 'standard') == 'overnight' ? 'selected' : '' }}>Overnight</option>
                                <option value="pickup" {{ ($purchase->shipping_method ?? 'standard') == 'pickup' ? 'selected' : '' }}>Supplier Pickup</option>
                                <option value="custom" {{ ($purchase->shipping_method ?? 'standard') == 'custom' ? 'selected' : '' }}>Custom Arrangement</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Payment Terms</label>
                            <select class="form-select" name="payment_terms">
                                <option value="net_30" {{ ($purchase->payment_terms ?? 'net_45') == 'net_30' ? 'selected' : '' }}>Net 30 Days</option>
                                <option value="net_15" {{ ($purchase->payment_terms ?? 'net_45') == 'net_15' ? 'selected' : '' }}>Net 15 Days</option>
                                <option value="net_45" {{ ($purchase->payment_terms ?? 'net_45') == 'net_45' ? 'selected' : '' }}>Net 45 Days</option>
                                <option value="net_60" {{ ($purchase->payment_terms ?? 'net_45') == 'net_60' ? 'selected' : '' }}>Net 60 Days</option>
                                <option value="due_on_receipt" {{ ($purchase->payment_terms ?? 'net_45') == 'due_on_receipt' ? 'selected' : '' }}>Due on Receipt</option>
                                <option value="prepaid" {{ ($purchase->payment_terms ?? 'net_45') == 'prepaid' ? 'selected' : '' }}>Prepaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Budget Code</label>
                            <input type="text" class="form-control" name="budget_code" value="{{ $purchase->budget_code ?? 'PROD-2024-002' }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Internal Notes</label>
                            <textarea class="form-control" name="internal_notes" rows="3">{{ $purchase->internal_notes ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Version History -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-history"></i>
                    Revision History
                </h4>

                <div class="version-history">
                    <div class="mb-2">
                        <strong>Current Version:</strong> v2.1 (Your changes)
                        <span class="badge bg-warning ms-2">Unsaved</span>
                    </div>
                    <div class="mb-2">
                        <strong>v2.0:</strong> Updated quantities and added plastic components
                        <small class="text-muted">- Feb 05, 2024 by Sarah Johnson</small>
                    </div>
                    <div class="mb-2">
                        <strong>v1.0:</strong> Initial purchase order created
                        <small class="text-muted">- Feb 01, 2024 by Sarah Johnson</small>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetChanges()">
                            <i class="fas fa-undo"></i> Reset Changes
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="previewOrder()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('purchases.show', $purchase->id ?? 1) }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-info btn-action">
                            <i class="fas fa-save"></i> Update Purchase Order
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
let itemCounter = 2; // Start from 3 since we have 2 existing items
let hasChanges = false;

$(document).ready(function() {
    bindEventListeners();
    calculateTotals();
});

function bindEventListeners() {
    // Status selection
    $('.status-option').on('click', function() {
        $('.status-option').removeClass('selected');
        $(this).addClass('selected');
        $('#statusInput').val($(this).data('status'));
        markAsChanged();
    });

    // Priority selection
    $('.priority-option').on('click', function() {
        $('.priority-option').removeClass('selected');
        $(this).addClass('selected');
        $('#priorityInput').val($(this).data('priority'));
        markAsChanged();
    });

    // Track changes on form inputs
    $('#purchaseForm input, #purchaseForm select, #purchaseForm textarea').on('change input', function() {
        markAsChanged();
    });

    // Calculate totals when item values change
    $(document).on('input', '.item-quantity, .item-price, .item-tax', function() {
        calculateItemTotal($(this).closest('.item-row'));
        calculateTotals();
        markAsChanged();
    });

    // Form submission
    $('#purchaseForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });

    // Warn before leaving page with unsaved changes
    $(window).on('beforeunload', function(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
}

function markAsChanged() {
    hasChanges = true;
    $('#changesIndicator').addClass('show');
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
                        <input type="number" class="form-control item-tax" name="items[${itemCounter}][tax_rate]" step="0.01" min="0" max="100" value="8">
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
    markAsChanged();
}

function removeItem(itemId) {
    if ($('.item-row').length > 1) {
        $(`.item-row[data-item="${itemId}"]`).remove();
        calculateTotals();
        markAsChanged();
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

    const shipping = 0;
    const total = subtotal + totalTax + shipping;

    $('#subtotalAmount').text('$' + subtotal.toFixed(2));
    $('#taxAmount').text('$' + totalTax.toFixed(2));
    $('#shippingAmount').text('$' + shipping.toFixed(2));
    $('#totalAmount').text('$' + total.toFixed(2));
}

function validateForm() {
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

function resetChanges() {
    if (confirm('Are you sure you want to reset all changes? This will restore the original values.')) {
        location.reload();
    }
}

function previewOrder() {
    if (validateForm()) {
        alert('Preview functionality would open a new window showing the formatted purchase order.');
    }
}

// Auto-save functionality
setInterval(function() {
    if (hasChanges) {
        const formData = {};
        $('#purchaseForm input, #purchaseForm select, #purchaseForm textarea').each(function() {
            if (this.name && this.value) {
                formData[this.name] = this.value;
            }
        });

        localStorage.setItem('purchaseOrderEditDraft', JSON.stringify(formData));
    }
}, 30000);
</script>
@endpush