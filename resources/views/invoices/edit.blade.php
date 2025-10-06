@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Invoice #{{ $invoice->invoice_number ?? 'INV-2024-12345' }}</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoices.show', $invoice->id ?? 1) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye"></i> View Invoice
                        </a>
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="invoiceEditForm" action="{{ route('invoices.update', $invoice->id ?? 1) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-lg-8">
                                <!-- Invoice Information Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Invoice Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="invoice_number" class="form-label">Invoice Number *</label>
                                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                                                           value="{{ $invoice->invoice_number ?? 'INV-2024-12345' }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="issue_date" class="form-label">Issue Date *</label>
                                                    <input type="date" class="form-control" id="issue_date" name="issue_date"
                                                           value="{{ $invoice->issue_date ?? '2024-01-15' }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="due_date" class="form-label">Due Date *</label>
                                                    <input type="date" class="form-control" id="due_date" name="due_date"
                                                           value="{{ $invoice->due_date ?? '2024-02-15' }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="payment_terms" class="form-label">Payment Terms</label>
                                                    <select class="form-select" id="payment_terms" name="payment_terms">
                                                        <option value="net_15" {{ ($invoice->payment_terms ?? 'net_30') === 'net_15' ? 'selected' : '' }}>Net 15</option>
                                                        <option value="net_30" {{ ($invoice->payment_terms ?? 'net_30') === 'net_30' ? 'selected' : '' }}>Net 30</option>
                                                        <option value="net_45" {{ ($invoice->payment_terms ?? 'net_30') === 'net_45' ? 'selected' : '' }}>Net 45</option>
                                                        <option value="net_60" {{ ($invoice->payment_terms ?? 'net_30') === 'net_60' ? 'selected' : '' }}>Net 60</option>
                                                        <option value="due_on_receipt" {{ ($invoice->payment_terms ?? 'net_30') === 'due_on_receipt' ? 'selected' : '' }}>Due on Receipt</option>
                                                        <option value="custom" {{ ($invoice->payment_terms ?? 'net_30') === 'custom' ? 'selected' : '' }}>Custom</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="po_number" class="form-label">PO Number</label>
                                                    <input type="text" class="form-control" id="po_number" name="po_number"
                                                           value="{{ $invoice->po_number ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" id="status" name="status">
                                                        <option value="draft" {{ ($invoice->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                                        <option value="sent" {{ ($invoice->status ?? 'draft') === 'sent' ? 'selected' : '' }}>Sent</option>
                                                        <option value="viewed" {{ ($invoice->status ?? 'draft') === 'viewed' ? 'selected' : '' }}>Viewed</option>
                                                        <option value="paid" {{ ($invoice->status ?? 'draft') === 'paid' ? 'selected' : '' }}>Paid</option>
                                                        <option value="overdue" {{ ($invoice->status ?? 'draft') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                                        <option value="cancelled" {{ ($invoice->status ?? 'draft') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Information Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Bill To</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="customer_type" class="form-label">Customer Type</label>
                                                    <select class="form-select" id="customer_type" name="customer_type">
                                                        <option value="existing" {{ isset($invoice->customer_id) ? 'selected' : '' }}>Existing Customer</option>
                                                        <option value="custom" {{ !isset($invoice->customer_id) ? 'selected' : '' }}>Custom Customer</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6" id="customer_select_wrapper" style="{{ isset($invoice->customer_id) ? '' : 'display: none;' }}">
                                                <div class="mb-3">
                                                    <label for="customer_id" class="form-label">Select Customer</label>
                                                    <select class="form-select" id="customer_id" name="customer_id">
                                                        <option value="">Choose a customer...</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Details (for custom customers) -->
                                        <div id="customer_details" style="{{ !isset($invoice->customer_id) ? '' : 'display: none;' }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="customer_name" class="form-label">Customer Name *</label>
                                                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                                                               value="{{ $invoice->customer_name ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="customer_email" class="form-label">Email *</label>
                                                        <input type="email" class="form-control" id="customer_email" name="customer_email"
                                                               value="{{ $invoice->customer_email ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="customer_phone" class="form-label">Phone</label>
                                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                                               value="{{ $invoice->customer_phone ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="customer_company" class="form-label">Company</label>
                                                        <input type="text" class="form-control" id="customer_company" name="customer_company"
                                                               value="{{ $invoice->customer_company ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label for="billing_address" class="form-label">Billing Address</label>
                                                        <textarea class="form-control" id="billing_address" name="billing_address" rows="3">{{ $invoice->billing_address ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Invoice Items Section -->
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Invoice Items</h5>
                                        <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="itemsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="35%">Description</th>
                                                        <th width="10%">Qty</th>
                                                        <th width="15%">Rate</th>
                                                        <th width="15%">Tax (%)</th>
                                                        <th width="15%">Amount</th>
                                                        <th width="10%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemsTableBody">
                                                    @if(isset($invoice->items) && $invoice->items->count() > 0)
                                                        @foreach($invoice->items as $index => $item)
                                                        <tr data-item="{{ $index }}">
                                                            <td>
                                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                                <input type="text" class="form-control item-description" name="items[{{ $index }}][description]"
                                                                       value="{{ $item->description ?? 'Sample Service' }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]"
                                                                       value="{{ $item->quantity ?? 1 }}" min="1" step="0.01" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" class="form-control item-rate" name="items[{{ $index }}][rate]"
                                                                       value="{{ $item->rate ?? 0 }}" min="0" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" class="form-control item-tax" name="items[{{ $index }}][tax_rate]"
                                                                       value="{{ $item->tax_rate ?? 0 }}" min="0" max="100">
                                                            </td>
                                                            <td>
                                                                <span class="item-amount">${{ number_format($item->amount ?? 0, 2) }}</span>
                                                                <input type="hidden" class="item-amount-input" name="items[{{ $index }}][amount]" value="{{ $item->amount ?? 0 }}">
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @else
                                                        <tr data-item="0">
                                                            <td>
                                                                <input type="text" class="form-control item-description" name="items[0][description]"
                                                                       value="Professional Services" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control item-quantity" name="items[0][quantity]"
                                                                       value="10" min="1" step="0.01" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" class="form-control item-rate" name="items[0][rate]"
                                                                       value="125.00" min="0" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" class="form-control item-tax" name="items[0][tax_rate]"
                                                                       value="8" min="0" max="100">
                                                            </td>
                                                            <td>
                                                                <span class="item-amount">$1,350.00</span>
                                                                <input type="hidden" class="item-amount-input" name="items[0][amount]" value="1350.00">
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Additional Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="notes" class="form-label">Notes</label>
                                                    <textarea class="form-control" id="notes" name="notes" rows="4"
                                                              placeholder="Any additional notes or instructions...">{{ $invoice->notes ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="terms" class="form-label">Terms & Conditions</label>
                                                    <textarea class="form-control" id="terms" name="terms" rows="4"
                                                              placeholder="Payment terms and conditions...">{{ $invoice->terms ?? 'Payment is due within 30 days of invoice date. Late payments may incur additional charges.' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Summary -->
                            <div class="col-lg-4">
                                <div class="card sticky-top" style="top: 1rem;">
                                    <div class="card-header">
                                        <h5 class="mb-0">Invoice Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Subtotal:</span>
                                                <span id="subtotal">${{ number_format($invoice->subtotal ?? 1850.00, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="discount_type" class="form-label">Discount</label>
                                            <div class="input-group">
                                                <select class="form-select" id="discount_type" name="discount_type" style="flex: 0 0 auto; width: auto;">
                                                    <option value="fixed" {{ ($invoice->discount_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>$</option>
                                                    <option value="percentage" {{ ($invoice->discount_type ?? 'fixed') === 'percentage' ? 'selected' : '' }}>%</option>
                                                </select>
                                                <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value"
                                                       value="{{ $invoice->discount_value ?? 0 }}" min="0">
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <span>Discount Amount:</span>
                                                <span id="discount_amount">${{ number_format($invoice->discount ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Tax Total:</span>
                                                <span id="tax_total">${{ number_format($invoice->tax ?? 108.00, 2) }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="h5">Total:</span>
                                                <span class="h5 text-primary" id="grand_total">${{ number_format($invoice->total ?? 1958.00, 2) }}</span>
                                            </div>
                                        </div>

                                        <!-- Hidden fields for totals -->
                                        <input type="hidden" id="subtotal_input" name="subtotal" value="{{ $invoice->subtotal ?? 1850.00 }}">
                                        <input type="hidden" id="discount_input" name="discount" value="{{ $invoice->discount ?? 0 }}">
                                        <input type="hidden" id="tax_input" name="tax" value="{{ $invoice->tax ?? 108.00 }}">
                                        <input type="hidden" id="total_input" name="total" value="{{ $invoice->total ?? 1958.00 }}">

                                        <hr>

                                        <!-- Action Buttons -->
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary" name="action" value="save">
                                                <i class="fas fa-save"></i> Update Invoice
                                            </button>
                                            <button type="submit" class="btn btn-success" name="action" value="save_and_send">
                                                <i class="fas fa-paper-plane"></i> Update & Send
                                            </button>
                                            <button type="button" class="btn btn-info" onclick="previewInvoice()">
                                                <i class="fas fa-eye"></i> Preview
                                            </button>
                                            <a href="{{ route('invoices.show', $invoice->id ?? 1) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product/Service Selection Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Quick Add</h6>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="quickDescription" placeholder="Item description">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control" id="quickQuantity" placeholder="Qty" value="1">
                            </div>
                            <div class="col-6">
                                <input type="number" step="0.01" class="form-control" id="quickRate" placeholder="Rate">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mt-2" onclick="addQuickItem()">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="col-md-6">
                        <h6>Select from Products</h6>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <tbody id="productsList">
                                    <!-- Products will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('invoiceEditForm').submit()">
                    Update Invoice
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let itemCounter = {{ isset($invoice->items) ? $invoice->items->count() : 1 }};

    // Customer type change handler
    $('#customer_type').change(function() {
        const type = $(this).val();
        if (type === 'existing') {
            $('#customer_select_wrapper').show();
            $('#customer_details').hide();
            loadCustomers();
        } else {
            $('#customer_select_wrapper').hide();
            $('#customer_details').show();
            $('#customer_id').val('');
        }
    });

    // Load customers for selection
    function loadCustomers() {
        $.get('/customers/data/list', function(response) {
            const customers = response.success ? response.data.data : [];
            const select = $('#customer_id');
            select.empty().append('<option value="">Choose a customer...</option>');
            customers.forEach(function(customer) {
                const selected = customer.id == {{ $invoice->customer_id ?? 'null' }} ? 'selected' : '';
                select.append(`<option value="${customer.id}" data-email="${customer.email}" data-phone="${customer.phone}" data-address="${customer.address}" ${selected}>${customer.name} - ${customer.email}</option>`);
            });
        });
    }

    // Customer selection change
    $('#customer_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#customer_email').val(selectedOption.data('email'));
            $('#customer_phone').val(selectedOption.data('phone'));
            $('#billing_address').val(selectedOption.data('address'));
        }
    });

    // Payment terms change handler
    $('#payment_terms').change(function() {
        const terms = $(this).val();
        const issueDate = new Date($('#issue_date').val());
        let dueDate = new Date(issueDate);

        switch (terms) {
            case 'net_15':
                dueDate.setDate(dueDate.getDate() + 15);
                break;
            case 'net_30':
                dueDate.setDate(dueDate.getDate() + 30);
                break;
            case 'net_45':
                dueDate.setDate(dueDate.getDate() + 45);
                break;
            case 'net_60':
                dueDate.setDate(dueDate.getDate() + 60);
                break;
            case 'due_on_receipt':
                dueDate = new Date(issueDate);
                break;
        }

        if (terms !== 'custom') {
            $('#due_date').val(dueDate.toISOString().split('T')[0]);
        }
    });

    // Add item button
    $('#addItemBtn').click(function() {
        $('#itemModal').modal('show');
        loadProducts();
    });

    // Load products for selection
    function loadProducts() {
        $.get('/products/data/list', function(response) {
            const products = response.success ? (response.data.data || response.data) : [];
            const tbody = $('#productsList');
            tbody.empty();

            if (Array.isArray(products)) {
                products.forEach(function(product) {
                tbody.append(`
                    <tr style="cursor: pointer;" onclick="selectProduct(${product.id}, '${product.name}', ${product.price})">
                        <td>
                            <div><strong>${product.name}</strong></div>
                            <small class="text-muted">${product.description || ''}</small>
                        </td>
                        <td class="text-end">$${parseFloat(product.price).toFixed(2)}</td>
                    </tr>
                `);
            });
            }
        });
    }

    // Product search
    $('#productSearch').on('input', function() {
        const search = $(this).val().toLowerCase();
        $('#productsList tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(search));
        });
    });

    // Select product
    window.selectProduct = function(id, name, price) {
        addItemToTable({
            description: name,
            quantity: 1,
            rate: parseFloat(price),
            tax_rate: 0
        });
        $('#itemModal').modal('hide');
    };

    // Add quick item
    window.addQuickItem = function() {
        const description = $('#quickDescription').val();
        const quantity = parseFloat($('#quickQuantity').val()) || 1;
        const rate = parseFloat($('#quickRate').val()) || 0;

        if (!description || rate <= 0) {
            alert('Please enter description and rate.');
            return;
        }

        addItemToTable({
            description: description,
            quantity: quantity,
            rate: rate,
            tax_rate: 0
        });

        // Clear quick add form
        $('#quickDescription').val('');
        $('#quickQuantity').val('1');
        $('#quickRate').val('');
        $('#itemModal').modal('hide');
    };

    // Add item to table
    function addItemToTable(item) {
        const row = `
            <tr data-item="${itemCounter}">
                <td>
                    <input type="text" class="form-control item-description" name="items[${itemCounter}][description]"
                           value="${item.description}" required>
                </td>
                <td>
                    <input type="number" class="form-control item-quantity" name="items[${itemCounter}][quantity]"
                           value="${item.quantity}" min="1" step="0.01" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control item-rate" name="items[${itemCounter}][rate]"
                           value="${item.rate.toFixed(2)}" min="0" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control item-tax" name="items[${itemCounter}][tax_rate]"
                           value="${item.tax_rate}" min="0" max="100">
                </td>
                <td>
                    <span class="item-amount">$${(item.quantity * item.rate).toFixed(2)}</span>
                    <input type="hidden" class="item-amount-input" name="items[${itemCounter}][amount]" value="${(item.quantity * item.rate).toFixed(2)}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#itemsTableBody').append(row);
        itemCounter++;
        calculateTotals();
    }

    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // Calculate item amount when quantity, rate, or tax changes
    $(document).on('input', '.item-quantity, .item-rate, .item-tax', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
        const rate = parseFloat(row.find('.item-rate').val()) || 0;
        const taxRate = parseFloat(row.find('.item-tax').val()) || 0;

        const subtotal = quantity * rate;
        const taxAmount = subtotal * (taxRate / 100);
        const total = subtotal + taxAmount;

        row.find('.item-amount').text(`$${total.toFixed(2)}`);
        row.find('.item-amount-input').val(total.toFixed(2));

        calculateTotals();
    });

    // Calculate discount when discount changes
    $(document).on('input', '#discount_value, #discount_type', function() {
        calculateTotals();
    });

    // Calculate invoice totals
    function calculateTotals() {
        let subtotal = 0;
        let taxTotal = 0;

        $('.item-amount-input').each(function() {
            const row = $(this).closest('tr');
            const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
            const rate = parseFloat(row.find('.item-rate').val()) || 0;
            const taxRate = parseFloat(row.find('.item-tax').val()) || 0;

            const itemSubtotal = quantity * rate;
            const itemTax = itemSubtotal * (taxRate / 100);

            subtotal += itemSubtotal;
            taxTotal += itemTax;
        });

        // Calculate discount
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val()) || 0;
        let discountAmount = 0;

        if (discountType === 'percentage') {
            discountAmount = subtotal * (discountValue / 100);
        } else {
            discountAmount = discountValue;
        }

        const grandTotal = subtotal + taxTotal - discountAmount;

        // Update display
        $('#subtotal').text(`$${subtotal.toFixed(2)}`);
        $('#discount_amount').text(`$${discountAmount.toFixed(2)}`);
        $('#tax_total').text(`$${taxTotal.toFixed(2)}`);
        $('#grand_total').text(`$${grandTotal.toFixed(2)}`);

        // Update hidden inputs
        $('#subtotal_input').val(subtotal.toFixed(2));
        $('#discount_input').val(discountAmount.toFixed(2));
        $('#tax_input').val(taxTotal.toFixed(2));
        $('#total_input').val(grandTotal.toFixed(2));
    }

    // Preview invoice
    window.previewInvoice = function() {
        if ($('#itemsTableBody tr').length === 0) {
            alert('Please add at least one item to the invoice.');
            return;
        }

        // Generate preview content (similar to create form)
        let previewHtml = generateInvoicePreview();
        $('#previewContent').html(previewHtml);
        $('#previewModal').modal('show');
    };

    function generateInvoicePreview() {
        // Similar to create form preview function
        const invoiceNumber = $('#invoice_number').val();
        const issueDate = $('#issue_date').val();
        const dueDate = $('#due_date').val();

        let customerInfo = '';
        if ($('#customer_type').val() === 'existing') {
            const customerText = $('#customer_id option:selected').text();
            customerInfo = customerText;
        } else {
            customerInfo = `
                ${$('#customer_name').val()}<br>
                ${$('#customer_email').val()}<br>
                ${$('#customer_phone').val()}<br>
                ${$('#billing_address').val().replace(/\n/g, '<br>')}
            `;
        }

        let itemsHtml = '';
        $('#itemsTableBody tr').each(function() {
            const description = $(this).find('.item-description').val();
            const quantity = $(this).find('.item-quantity').val();
            const rate = $(this).find('.item-rate').val();
            const taxRate = $(this).find('.item-tax').val();
            const amount = $(this).find('.item-amount').text();

            if (description) {
                itemsHtml += `
                    <tr>
                        <td>${description}</td>
                        <td class="text-center">${quantity}</td>
                        <td class="text-end">$${parseFloat(rate).toFixed(2)}</td>
                        <td class="text-center">${taxRate}%</td>
                        <td class="text-end">${amount}</td>
                    </tr>
                `;
            }
        });

        return `
            <div class="invoice-preview">
                <div class="row mb-4">
                    <div class="col-6">
                        <h2>INVOICE</h2>
                        <p><strong>Invoice #:</strong> ${invoiceNumber}</p>
                        <p><strong>Issue Date:</strong> ${issueDate}</p>
                        <p><strong>Due Date:</strong> ${dueDate}</p>
                    </div>
                    <div class="col-6 text-end">
                        <h4>Your Company Name</h4>
                        <p>123 Business Street<br>
                        City, State 12345<br>
                        (555) 123-4567</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <h5>Bill To:</h5>
                        <p>${customerInfo}</p>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-center">Tax</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-6">
                        <strong>Notes:</strong><br>
                        ${$('#notes').val() || 'No notes'}
                    </div>
                    <div class="col-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end">${$('#subtotal').text()}</td>
                            </tr>
                            <tr>
                                <td><strong>Discount:</strong></td>
                                <td class="text-end">${$('#discount_amount').text()}</td>
                            </tr>
                            <tr>
                                <td><strong>Tax:</strong></td>
                                <td class="text-end">${$('#tax_total').text()}</td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Total:</strong></td>
                                <td class="text-end"><strong>${$('#grand_total').text()}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    // Form validation
    $('#invoiceEditForm').on('submit', function(e) {
        if ($('#itemsTableBody tr').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the invoice.');
            return false;
        }

        if ($('#customer_type').val() === 'custom') {
            if (!$('#customer_name').val() || !$('#customer_email').val()) {
                e.preventDefault();
                alert('Please enter customer name and email.');
                return false;
            }
        } else if (!$('#customer_id').val()) {
            e.preventDefault();
            alert('Please select a customer.');
            return false;
        }
    });

    // Initialize
    if ($('#customer_type').val() === 'existing') {
        loadCustomers();
    }
    calculateTotals();
});
</script>
@endpush

@push('styles')
<style>
.invoice-preview {
    font-family: Arial, sans-serif;
}

.sticky-top {
    position: sticky;
}

.table th {
    border-top: none;
}

.item-amount {
    font-weight: bold;
}
</style>
@endpush