@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Order #{{ $order->order_number ?? 'ORD-2024-12345' }}</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('orders.show', $order->id ?? 1) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye"></i> View Order
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="orderEditForm" action="{{ route('orders.update', $order->id ?? 1) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Order Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Order Information</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_number" class="form-label">Order Number</label>
                                    <input type="text" class="form-control" id="order_number" name="order_number"
                                           value="{{ $order->order_number ?? 'ORD-2024-12345' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_date" class="form-label">Order Date *</label>
                                    <input type="date" class="form-control" id="order_date" name="order_date"
                                           value="{{ $order->order_date ?? '2024-01-15' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expected_delivery" class="form-label">Expected Delivery</label>
                                    <input type="date" class="form-control" id="expected_delivery" name="expected_delivery"
                                           value="{{ $order->expected_delivery ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low" {{ ($order->priority ?? 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ ($order->priority ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ ($order->priority ?? 'medium') === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ ($order->priority ?? 'medium') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Order Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pending" {{ ($order->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ ($order->status ?? 'pending') === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="shipped" {{ ($order->status ?? 'pending') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ ($order->status ?? 'pending') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="completed" {{ ($order->status ?? 'pending') === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ ($order->status ?? 'pending') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Customer Information</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_type" class="form-label">Customer Type</label>
                                    <select class="form-select" id="customer_type" name="customer_type">
                                        <option value="existing" {{ isset($order->customer_id) ? 'selected' : '' }}>Existing Customer</option>
                                        <option value="walk-in" {{ !isset($order->customer_id) ? 'selected' : '' }}>Walk-in Customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="customer_select_wrapper" style="{{ isset($order->customer_id) ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Select Customer</label>
                                    <select class="form-select" id="customer_id" name="customer_id">
                                        <option value="">Choose a customer...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Customer Details (for walk-in customers) -->
                            <div id="customer_details" class="col-12" style="{{ !isset($order->customer_id) ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">Customer Name *</label>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                                   value="{{ $order->customer_name ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_phone" class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                                   value="{{ $order->customer_phone ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="customer_email" name="customer_email"
                                                   value="{{ $order->customer_email ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_address" class="form-label">Address</label>
                                            <textarea class="form-control" id="customer_address" name="customer_address" rows="2">{{ $order->customer_address ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                    <h5 class="mb-0">Order Items</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="30%">Product</th>
                                                <th width="15%">Quantity</th>
                                                <th width="15%">Unit Price</th>
                                                <th width="10%">Discount</th>
                                                <th width="15%">Total</th>
                                                <th width="10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            @if(isset($order->items) && $order->items->count() > 0)
                                                @foreach($order->items as $index => $item)
                                                <tr data-item="{{ $index }}">
                                                    <td>
                                                        {{ $item->product->name ?? 'Sample Product' }}
                                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                        <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item->product->name ?? 'Sample Product' }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]"
                                                               value="{{ $item->quantity ?? 1 }}" min="1" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control item-price" name="items[{{ $index }}][price]"
                                                               value="{{ $item->price ?? 599.99 }}" min="0" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control item-discount" name="items[{{ $index }}][discount]"
                                                               value="{{ $item->discount ?? 0 }}" min="0">
                                                    </td>
                                                    <td>
                                                        <span class="item-total">${{ number_format($item->total ?? 599.99, 2) }}</span>
                                                        <input type="hidden" class="item-total-input" name="items[{{ $index }}][total]" value="{{ $item->total ?? 599.99 }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger remove-item">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr data-item="0">
                                                    <td>
                                                        Sample Product A
                                                        <input type="hidden" name="items[0][product_id]" value="1">
                                                        <input type="hidden" name="items[0][product_name]" value="Sample Product A">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control item-quantity" name="items[0][quantity]"
                                                               value="2" min="1" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control item-price" name="items[0][price]"
                                                               value="599.99" min="0" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control item-discount" name="items[0][discount]"
                                                               value="0" min="0">
                                                    </td>
                                                    <td>
                                                        <span class="item-total">$1,199.98</span>
                                                        <input type="hidden" class="item-total-input" name="items[0][total]" value="1199.98">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger remove-item">
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

                        <!-- Order Summary Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Order Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                              placeholder="Any special instructions or notes for this order...">{{ $order->notes ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Order Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal:</span>
                                            <span id="subtotal">${{ number_format($order->subtotal ?? 1199.98, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Discount:</span>
                                            <span id="total_discount">${{ number_format($order->discount ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Tax:</span>
                                            <span id="tax_amount">${{ number_format($order->tax ?? 95.99, 2) }}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span id="grand_total">${{ number_format($order->total ?? 1295.97, 2) }}</span>
                                        </div>

                                        <!-- Hidden fields for totals -->
                                        <input type="hidden" id="subtotal_input" name="subtotal" value="{{ $order->subtotal ?? 1199.98 }}">
                                        <input type="hidden" id="discount_input" name="discount" value="{{ $order->discount ?? 0 }}">
                                        <input type="hidden" id="tax_input" name="tax" value="{{ $order->tax ?? 95.99 }}">
                                        <input type="hidden" id="total_input" name="total" value="{{ $order->total ?? 1295.97 }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Payment Information</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select" id="payment_method" name="payment_method">
                                        <option value="cash" {{ ($order->payment_method ?? 'cash') === 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="card" {{ ($order->payment_method ?? 'cash') === 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                        <option value="bank_transfer" {{ ($order->payment_method ?? 'cash') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="check" {{ ($order->payment_method ?? 'cash') === 'check' ? 'selected' : '' }}>Check</option>
                                        <option value="credit" {{ ($order->payment_method ?? 'cash') === 'credit' ? 'selected' : '' }}>Store Credit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="pending" {{ ($order->payment_status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="partial" {{ ($order->payment_status ?? 'pending') === 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="paid" {{ ($order->payment_status ?? 'pending') === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="failed" {{ ($order->payment_status ?? 'pending') === 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="paid_amount" class="form-label">Paid Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount"
                                           value="{{ $order->paid_amount ?? 0 }}">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" name="action" value="save">
                                        <i class="fas fa-save"></i> Update Order
                                    </button>
                                    <button type="submit" class="btn btn-success" name="action" value="save_and_complete">
                                        <i class="fas fa-check"></i> Update & Complete
                                    </button>
                                    <button type="button" class="btn btn-info" id="previewBtn">
                                        <i class="fas fa-eye"></i> Preview Changes
                                    </button>
                                    <a href="{{ route('orders.show', $order->id ?? 1) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Item Selection Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <!-- Products will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('orderEditForm').submit()">
                    Update Order
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let itemCounter = {{ isset($order->items) ? $order->items->count() : 1 }};
    const taxRate = 0.08; // 8% tax rate

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
                const selected = customer.id == {{ $order->customer_id ?? 'null' }} ? 'selected' : '';
                select.append(`<option value="${customer.id}" ${selected}>${customer.name} - ${customer.phone}</option>`);
            });
        });
    }

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
                    <tr>
                        <td>${product.name}</td>
                        <td>${product.sku}</td>
                        <td>$${parseFloat(product.price).toFixed(2)}</td>
                        <td>${product.stock_quantity}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary select-product"
                                    data-id="${product.id}" data-name="${product.name}"
                                    data-price="${product.price}" data-stock="${product.stock_quantity}">
                                Select
                            </button>
                        </td>
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
    $(document).on('click', '.select-product', function() {
        const productData = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            price: parseFloat($(this).data('price')),
            stock: $(this).data('stock')
        };
        addItemToTable(productData);
        $('#itemModal').modal('hide');
    });

    // Add item to table
    function addItemToTable(product) {
        const row = `
            <tr data-item="${itemCounter}">
                <td>
                    ${product.name}
                    <input type="hidden" name="items[${itemCounter}][product_id]" value="${product.id}">
                    <input type="hidden" name="items[${itemCounter}][product_name]" value="${product.name}">
                </td>
                <td>
                    <input type="number" class="form-control item-quantity" name="items[${itemCounter}][quantity]"
                           value="1" min="1" max="${product.stock}" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control item-price" name="items[${itemCounter}][price]"
                           value="${product.price.toFixed(2)}" min="0" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control item-discount" name="items[${itemCounter}][discount]"
                           value="0" min="0">
                </td>
                <td>
                    <span class="item-total">$${product.price.toFixed(2)}</span>
                    <input type="hidden" class="item-total-input" name="items[${itemCounter}][total]" value="${product.price}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item">
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

    // Calculate item total when quantity, price, or discount changes
    $(document).on('input', '.item-quantity, .item-price, .item-discount', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const discount = parseFloat(row.find('.item-discount').val()) || 0;

        const total = (quantity * price) - discount;
        row.find('.item-total').text(`$${total.toFixed(2)}`);
        row.find('.item-total-input').val(total);

        calculateTotals();
    });

    // Calculate order totals
    function calculateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;

        $('.item-total-input').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        $('.item-discount').each(function() {
            totalDiscount += parseFloat($(this).val()) || 0;
        });

        const tax = subtotal * taxRate;
        const grandTotal = subtotal + tax;

        $('#subtotal').text(`$${subtotal.toFixed(2)}`);
        $('#total_discount').text(`$${totalDiscount.toFixed(2)}`);
        $('#tax_amount').text(`$${tax.toFixed(2)}`);
        $('#grand_total').text(`$${grandTotal.toFixed(2)}`);

        $('#subtotal_input').val(subtotal);
        $('#discount_input').val(totalDiscount);
        $('#tax_input').val(tax);
        $('#total_input').val(grandTotal);
    }

    // Preview order
    $('#previewBtn').click(function() {
        // Generate preview content similar to create form
        let previewHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Order Information</h6>
                    <p><strong>Order Number:</strong> ${$('#order_number').val()}</p>
                    <p><strong>Order Date:</strong> ${$('#order_date').val()}</p>
                    <p><strong>Expected Delivery:</strong> ${$('#expected_delivery').val() || 'Not specified'}</p>
                    <p><strong>Priority:</strong> ${$('#priority').val()}</p>
                    <p><strong>Status:</strong> ${$('#status').val()}</p>
                </div>
                <div class="col-md-6">
                    <h6>Customer Information</h6>
        `;

        if ($('#customer_type').val() === 'existing') {
            const customerText = $('#customer_id option:selected').text();
            previewHtml += `<p><strong>Customer:</strong> ${customerText}</p>`;
        } else {
            previewHtml += `
                <p><strong>Name:</strong> ${$('#customer_name').val()}</p>
                <p><strong>Phone:</strong> ${$('#customer_phone').val()}</p>
                <p><strong>Email:</strong> ${$('#customer_email').val()}</p>
            `;
        }

        previewHtml += `
                </div>
            </div>
            <hr>
            <h6>Order Items</h6>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
        `;

        $('#itemsTableBody tr').each(function() {
            const productName = $(this).find('input[name*="[product_name]"]').val();
            const quantity = $(this).find('.item-quantity').val();
            const price = $(this).find('.item-price').val();
            const total = $(this).find('.item-total').text();

            if (productName) {
                previewHtml += `
                    <tr>
                        <td>${productName}</td>
                        <td>${quantity}</td>
                        <td>$${parseFloat(price).toFixed(2)}</td>
                        <td>${total}</td>
                    </tr>
                `;
            }
        });

        previewHtml += `
                </tbody>
            </table>
            <div class="row mt-3">
                <div class="col-md-8">
                    <h6>Notes</h6>
                    <p>${$('#notes').val() || 'No notes'}</p>
                </div>
                <div class="col-md-4">
                    <h6>Order Summary</h6>
                    <p><strong>Subtotal:</strong> ${$('#subtotal').text()}</p>
                    <p><strong>Discount:</strong> ${$('#total_discount').text()}</p>
                    <p><strong>Tax:</strong> ${$('#tax_amount').text()}</p>
                    <p><strong>Total:</strong> ${$('#grand_total').text()}</p>
                </div>
            </div>
        `;

        $('#previewContent').html(previewHtml);
        $('#previewModal').modal('show');
    });

    // Form validation
    $('#orderEditForm').on('submit', function(e) {
        if ($('#itemsTableBody tr').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the order.');
            return false;
        }

        if ($('#customer_type').val() !== 'existing' && !$('#customer_name').val()) {
            e.preventDefault();
            alert('Please enter customer name.');
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