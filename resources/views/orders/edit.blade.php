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
                                        <option value="confirmed" {{ ($order->status ?? 'pending') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="in_progress" {{ ($order->status ?? 'pending') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
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
                            <div class="col-md-6" id="customer_select_wrapper">
                                <div class="mb-3">
                                    <label for="customer_search" class="form-label">Search Customer</label>
                                    <input type="text" class="form-control mb-2" id="customer_search" placeholder="Search by name or phone...">
                                </div>
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Select Customer *</label>
                                    <select class="form-select" id="customer_id" name="customer_id" size="5" required>
                                        <option value="">Choose a customer...</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Hidden field for customer type - always existing when editing -->
                            <input type="hidden" name="customer_type" value="existing">
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
                                                        {{ $item->product_name ?? 'Unknown Product' }}
                                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                        <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item->product_name ?? 'Unknown Product' }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]"
                                                               value="{{ $item->quantity ?? 1 }}" min="1" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control item-price" name="items[{{ $index }}][price]"
                                                               value="{{ $item->unit_price ?? 0 }}" min="0" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control item-discount" name="items[{{ $index }}][discount]"
                                                               value="0" min="0">
                                                    </td>
                                                    <td>
                                                        <span class="item-total">₹{{ number_format($item->subtotal ?? 0, 2) }}</span>
                                                        <input type="hidden" class="item-total-input" name="items[{{ $index }}][total]" value="{{ $item->subtotal ?? 0 }}">
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
                                                        Click "Add Item" to add products
                                                        <input type="hidden" name="items[0][product_id]" value="">
                                                        <input type="hidden" name="items[0][product_name]" value="">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control item-quantity" name="items[0][quantity]"
                                                               value="1" min="1" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control item-price" name="items[0][price]"
                                                               value="0" min="0" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control item-discount" name="items[0][discount]"
                                                               value="0" min="0">
                                                    </td>
                                                    <td>
                                                        <span class="item-total">₹0.00</span>
                                                        <input type="hidden" class="item-total-input" name="items[0][total]" value="0">
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
                                            <span id="subtotal">₹{{ number_format($order->total_amount ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Delivery Charges:</span>
                                            <span id="delivery_charges">₹{{ number_format($order->delivery_charges ?? 0, 2) }}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span id="grand_total">₹{{ number_format(($order->total_amount ?? 0) + ($order->delivery_charges ?? 0), 2) }}</span>
                                        </div>

                                        <!-- Hidden fields for totals -->
                                        <input type="hidden" id="subtotal_input" name="subtotal" value="{{ $order->total_amount ?? 0 }}">
                                        <input type="hidden" id="delivery_charges_input" name="delivery_charges" value="{{ $order->delivery_charges ?? 0 }}">
                                        <input type="hidden" id="total_input" name="total" value="{{ ($order->total_amount ?? 0) + ($order->delivery_charges ?? 0) }}">
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
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ ($order->payment_method ?? '') === 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="card" {{ ($order->payment_method ?? '') === 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                        <option value="upi" {{ ($order->payment_method ?? '') === 'upi' ? 'selected' : '' }}>UPI</option>
                                        <option value="bank_transfer" {{ ($order->payment_method ?? '') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="check" {{ ($order->payment_method ?? '') === 'check' ? 'selected' : '' }}>Check</option>
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

@push('styles')
<style>
/* Customer select box styling */
#customer_id {
    min-height: 150px;
    max-height: 200px;
    overflow-y: auto;
}

#customer_id option {
    padding: 8px;
    cursor: pointer;
}

#customer_id option:hover {
    background-color: #f0f0f0;
}

#customer_search {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

#customer_search:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let itemCounter = {{ isset($order->items) ? $order->items->count() : 1 }};
    // No tax rate as per user requirement

    // Load customers for selection
    function loadCustomers() {
        $.get('/customers/data/list', function(response) {
            console.log('Customer response:', response);

            // Handle different response structures
            let customers = [];
            if (response.success && response.data) {
                customers = response.data.data || response.data || [];
            } else if (Array.isArray(response)) {
                customers = response;
            }

            console.log('Customers array:', customers);

            const select = $('#customer_id');
            select.empty().append('<option value="">Choose a customer...</option>');

            if (customers.length === 0) {
                select.append('<option value="" disabled>No customers found</option>');
                console.warn('No customers loaded');
                return;
            }

            customers.forEach(function(customer) {
                const selected = customer.id == {{ $order->customer_id ?? 'null' }} ? 'selected' : '';
                select.append(`<option value="${customer.id}" data-name="${customer.name}" data-phone="${customer.phone || ''}" ${selected}>${customer.name} - ${customer.phone || 'No phone'}</option>`);
            });

            console.log('Loaded ' + customers.length + ' customers');
        }).fail(function(xhr, status, error) {
            console.error('Error loading customers:', error);
            $('#customer_id').html('<option value="">Error loading customers</option>');
        });
    }

    // Customer search functionality
    $('#customer_search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#customer_id option').each(function() {
            const nameData = $(this).data('name');
            const phoneData = $(this).data('phone');
            const name = (nameData && typeof nameData === 'string') ? nameData.toLowerCase() : '';
            const phone = (phoneData && typeof phoneData === 'string') ? phoneData.toLowerCase() : '';
            const text = $(this).text().toLowerCase();

            if (name.includes(searchTerm) || phone.includes(searchTerm) || text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
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
                    <tr>
                        <td>${product.name}</td>
                        <td>${product.sku}</td>
                        <td>₹${parseFloat(product.base_price || product.price || 0).toFixed(2)}</td>
                        <td>Available</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary select-product"
                                    data-id="${product.id}" data-name="${product.name}"
                                    data-price="${product.base_price || product.price || 0}" data-stock="100">
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
                    <span class="item-total">₹${product.price.toFixed(2)}</span>
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
        row.find('.item-total').text(`₹${total.toFixed(2)}`);
        row.find('.item-total-input').val(total);

        calculateTotals();
    });

    // Calculate order totals
    function calculateTotals() {
        let subtotal = 0;

        $('.item-total-input').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        const deliveryCharges = parseFloat($('#delivery_charges_input').val()) || 0;
        const grandTotal = subtotal + deliveryCharges;

        $('#subtotal').text(`₹${subtotal.toFixed(2)}`);
        $('#grand_total').text(`₹${grandTotal.toFixed(2)}`);

        $('#subtotal_input').val(subtotal);
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
                    <p><strong>Customer:</strong> ${$('#customer_id option:selected').text()}</p>
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
                        <td>₹${parseFloat(price).toFixed(2)}</td>
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
        // Check if at least one item is added
        if ($('#itemsTableBody tr').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the order.');
            return false;
        }

        // Validate customer selection - always required for edit
        if (!$('#customer_id').val()) {
            e.preventDefault();
            alert('Please select a customer.');
            return false;
        }
    });

    // Initialize - always load customers for edit page
    loadCustomers();
    calculateTotals();
});
</script>
@endpush