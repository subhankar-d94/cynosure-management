@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Create New Order</h3>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>
                <div class="card-body">
                    <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                        @csrf

                        <!-- Order Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Order Information</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_number" class="form-label">Order Number</label>
                                    <input type="text" class="form-control" id="order_number" name="order_number"
                                           value="{{ 'ORD-' . date('Y') . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_date" class="form-label">Order Date *</label>
                                    <input type="date" class="form-control" id="order_date" name="order_date"
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expected_delivery" class="form-label">Expected Delivery</label>
                                    <input type="date" class="form-control" id="expected_delivery" name="expected_delivery">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
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
                                    <label for="customer_type" class="form-label">Customer Type *</label>
                                    <select class="form-select" id="customer_type" name="customer_type" required>
                                        <option value="existing">Existing Customer</option>
                                        <option value="new">New Customer</option>
                                    </select>
                                </div>
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

                            <!-- Customer Details (for new customers) -->
                            <div id="customer_details" class="col-12 d-none">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Please provide customer details below. The customer will be created automatically when saving the order.
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">Customer Name *</label>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter customer name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_phone" class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" placeholder="+91 9876543210">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_email" class="form-label">Email (Optional)</label>
                                            <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="customer@example.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_address" class="form-label">Address (Optional)</label>
                                            <textarea class="form-control" id="customer_address" name="customer_address" rows="2" placeholder="Enter customer address"></textarea>
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
                                            <!-- Items will be added dynamically -->
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
                                              placeholder="Any special instructions or notes for this order..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Order Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal:</span>
                                            <span id="subtotal">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Discount:</span>
                                            <span id="total_discount">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span id="grand_total">$0.00</span>
                                        </div>

                                        <!-- Hidden fields for totals -->
                                        <input type="hidden" id="subtotal_input" name="subtotal" value="0">
                                        <input type="hidden" id="discount_input" name="discount" value="0">
                                        <input type="hidden" id="total_input" name="total" value="0">
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
                                        <option value="cash">Cash</option>
                                        <option value="card">Credit/Debit Card</option>
                                        <option value="upi">UPI</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="credit">Store Credit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="pending">Pending</option>
                                        <option value="partial">Partial</option>
                                        <option value="paid">Paid</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="paid_amount" class="form-label">Paid Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" value="0">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" name="action" value="save">
                                        <i class="fas fa-save"></i> Save Order
                                    </button>
                                    <button type="submit" class="btn btn-success" name="action" value="save_and_process">
                                        <i class="fas fa-check"></i> Save & Process
                                    </button>
                                    <button type="button" class="btn btn-info" id="previewBtn">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
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
                <button type="button" class="btn btn-primary" onclick="document.getElementById('orderForm').submit()">
                    Create Order
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
    let itemCounter = 0;

    // Customer type change handler
    $('#customer_type').change(function() {
        const type = $(this).val();
        if (type === 'existing') {
            $('#customer_select_wrapper').removeClass('d-none');
            $('#customer_details').addClass('d-none');
            $('#customer_search').val(''); // Clear search
            $('#customer_id').prop('required', true); // Make customer_id required
            // Remove required from new customer fields and clear them
            $('#customer_name, #customer_phone').prop('required', false).val('');
            $('#customer_email, #customer_address').val('');
            loadCustomers();
        } else {
            $('#customer_select_wrapper').addClass('d-none');
            $('#customer_details').removeClass('d-none');
            $('#customer_id').val('').prop('required', false); // Not required for new customer
            $('#customer_search').val(''); // Clear search
            // Make new customer fields required
            $('#customer_name, #customer_phone').prop('required', true);
        }
    });

    // Trigger initial state
    $('#customer_type').trigger('change');

    // Load customers for selection
    function loadCustomers() {
        $.get('/customers/data/list', function(response) {
            const customers = response.success ? response.data.data : [];
            const select = $('#customer_id');
            select.empty().append('<option value="">Choose a customer...</option>');
            customers.forEach(function(customer) {
                select.append(`<option value="${customer.id}" data-name="${customer.name}" data-phone="${customer.phone || ''}">${customer.name} - ${customer.phone || 'No phone'}</option>`);
            });
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
                        <td>$${parseFloat(product.base_price).toFixed(2)}</td>
                        <td>${product.inventory.quantity_in_stock}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary select-product"
                                    data-id="${product.id}" data-name="${product.name}"
                                    data-price="${product.base_price}" data-stock="${product.inventory.quantity_in_stock}">
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

        const grandTotal = subtotal - totalDiscount;

        $('#subtotal').text(`$${subtotal.toFixed(2)}`);
        $('#total_discount').text(`$${totalDiscount.toFixed(2)}`);
        $('#grand_total').text(`$${grandTotal.toFixed(2)}`);

        $('#subtotal_input').val(subtotal);
        $('#discount_input').val(totalDiscount);
        $('#total_input').val(grandTotal);
    }

    // Preview order
    $('#previewBtn').click(function() {
        // Generate preview content
        let previewHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Order Information</h6>
                    <p><strong>Order Number:</strong> ${$('#order_number').val()}</p>
                    <p><strong>Order Date:</strong> ${$('#order_date').val()}</p>
                    <p><strong>Expected Delivery:</strong> ${$('#expected_delivery').val() || 'Not specified'}</p>
                    <p><strong>Priority:</strong> ${$('#priority').val()}</p>
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

            previewHtml += `
                <tr>
                    <td>${productName}</td>
                    <td>${quantity}</td>
                    <td>$${parseFloat(price).toFixed(2)}</td>
                    <td>${total}</td>
                </tr>
            `;
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
                    <p><strong>Total:</strong> ${$('#grand_total').text()}</p>
                </div>
            </div>
        `;

        $('#previewContent').html(previewHtml);
        $('#previewModal').modal('show');
    });

    // Form validation
    $('#orderForm').on('submit', function(e) {
        // Check if at least one item is added
        if ($('#itemsTableBody tr').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the order.');
            return false;
        }

        const customerType = $('#customer_type').val();

        // Validate customer selection based on type
        if (customerType === 'existing') {
            if (!$('#customer_id').val()) {
                e.preventDefault();
                alert('Please select an existing customer.');
                return false;
            }
            // Remove customer detail fields when existing customer is selected
            $('#customer_name, #customer_phone, #customer_email, #customer_address').prop('disabled', true);
        } else {
            // For new customers
            if (!$('#customer_name').val()) {
                e.preventDefault();
                alert('Please enter customer name.');
                return false;
            }
            if (!$('#customer_phone').val()) {
                e.preventDefault();
                alert('Please enter customer phone number.');
                return false;
            }
            // Remove customer_id field when new customer is selected
            $('#customer_id').prop('disabled', true);
        }
    });
});
</script>
@endpush
