@extends('layouts.app')

@section('title', 'Customer Orders')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.show', $customer->id ?? 1) }}">Customer Details</a></li>
        <li class="breadcrumb-item active">Orders</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        <span id="customerInitial">C</span>
                    </div>
                    <div>
                        <h2 class="mb-1">Orders for <span id="customerName">Customer</span></h2>
                        <p class="text-muted mb-0">Complete order history and order management</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="createNewOrder()">
                        <i class="bi bi-plus-lg me-1"></i>New Order
                    </button>
                    <a href="{{ route('customers.show', $customer->id ?? 1) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Customer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-primary text-uppercase mb-1">Total Orders</h6>
                            <h3 class="mb-0 text-primary" id="totalOrders">-</h3>
                            <small class="text-muted">All time</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-cart3" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-success text-uppercase mb-1">Total Value</h6>
                            <h3 class="mb-0 text-success" id="totalValue">-</h3>
                            <small class="text-muted">Lifetime revenue</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-currency-rupee" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-info text-uppercase mb-1">Pending Orders</h6>
                            <h3 class="mb-0 text-info" id="pendingOrders">-</h3>
                            <small class="text-muted">Awaiting action</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-clock-history" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-warning text-uppercase mb-1">Average Order</h6>
                            <h3 class="mb-0 text-warning" id="averageOrder">-</h3>
                            <small class="text-muted">Per order value</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-graph-up" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search Orders</label>
                            <input type="text" class="form-control" id="search" placeholder="Order number, items...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" id="date_range">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-2" id="customDateRange" style="display: none;">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" id="start_date">
                        </div>
                        <div class="col-md-2" id="customDateRangeEnd" style="display: none;">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" id="end_date">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" id="sort_by">
                                <option value="created_at">Date</option>
                                <option value="order_number">Order #</option>
                                <option value="total_amount">Amount</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Order</label>
                            <select class="form-select" id="sort_order">
                                <option value="desc">Newest First</option>
                                <option value="asc">Oldest First</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Order History</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="exportOrders()">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshOrders()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Delivery</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                                <!-- Orders will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> orders
                        </div>
                        <nav id="pagination">
                            <!-- Pagination will be loaded here -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsBody">
                <!-- Order details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="viewFullOrderBtn">View Full Order</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes about this status change..."></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notify_customer" value="1" checked>
                            <label class="form-check-label">Notify customer via email</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentCustomerId = {{ $customer->id ?? 'null' }};
let currentPage = 1;
let perPage = 15;
let currentOrderId = null;

$(document).ready(function() {
    if (currentCustomerId) {
        loadCustomerInfo();
        loadOrderStats();
        loadOrders();
    }

    // Filter change handlers
    $('#search, #status, #date_range, #sort_by, #sort_order').on('change input', function() {
        currentPage = 1;
        loadOrders();
    });

    // Custom date range toggle
    $('#date_range').on('change', function() {
        const isCustom = $(this).val() === 'custom';
        $('#customDateRange, #customDateRangeEnd').toggle(isCustom);
        if (!isCustom) {
            loadOrders();
        }
    });

    // Custom date inputs
    $('#start_date, #end_date').on('change', function() {
        if ($('#date_range').val() === 'custom') {
            loadOrders();
        }
    });

    // Update status form
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        submitStatusUpdate();
    });
});

function loadCustomerInfo() {
    $.get(`/customers/${currentCustomerId}`)
        .done(function(response) {
            if (response.success) {
                const customer = response.data;
                $('#customerName').text(customer.name);
                $('#customerInitial').text(customer.name.charAt(0).toUpperCase());
            }
        })
        .fail(function() {
            console.log('Error loading customer info');
        });
}

function loadOrderStats() {
    $.get(`/customers/${currentCustomerId}/orders`, { stats: 1 })
        .done(function(response) {
            if (response.success && response.stats) {
                const stats = response.stats;
                $('#totalOrders').text(stats.total_orders || 0);
                $('#totalValue').text('₹' + (stats.total_value || 0).toLocaleString());
                $('#pendingOrders').text(stats.pending_orders || 0);
                $('#averageOrder').text('₹' + (stats.average_order || 0).toLocaleString());
            }
        })
        .fail(function() {
            // Set default values
            $('#totalOrders').text('0');
            $('#totalValue').text('₹0');
            $('#pendingOrders').text('0');
            $('#averageOrder').text('₹0');
        });
}

function getFilters() {
    const filters = {
        search: $('#search').val(),
        status: $('#status').val(),
        date_range: $('#date_range').val(),
        sort_by: $('#sort_by').val(),
        sort_order: $('#sort_order').val(),
        page: currentPage,
        per_page: perPage
    };

    if (filters.date_range === 'custom') {
        filters.start_date = $('#start_date').val();
        filters.end_date = $('#end_date').val();
    }

    return filters;
}

function loadOrders() {
    const filters = getFilters();

    $.get(`/customers/${currentCustomerId}/orders`, filters)
        .done(function(response) {
            if (response.success) {
                renderOrdersTable(response.data.data || response.data);
                if (response.data.links) {
                    renderPagination(response.data);
                    updatePaginationInfo(response.data);
                }
            }
        })
        .fail(function() {
            showAlert('Error loading orders', 'danger');
        });
}

function renderOrdersTable(orders) {
    let html = '';

    if (orders.length === 0) {
        html = '<tr><td colspan="8" class="text-center py-4">No orders found</td></tr>';
    } else {
        orders.forEach(function(order) {
            const statusClass = getOrderStatusClass(order.status);
            const paymentStatusClass = getPaymentStatusClass(order.payment_status);

            html += `
                <tr>
                    <td>
                        <a href="/orders/${order.id}" class="fw-bold text-decoration-none">
                            #${order.order_number}
                        </a>
                    </td>
                    <td>
                        <div>
                            <strong>${new Date(order.created_at).toLocaleDateString()}</strong>
                            <small class="d-block text-muted">${new Date(order.created_at).toLocaleTimeString()}</small>
                        </div>
                    </td>
                    <td>
                        <span class="fw-bold">${order.items_count || 0}</span>
                        <small class="d-block text-muted">items</small>
                    </td>
                    <td>
                        <span class="fw-bold">₹${(order.total_amount || 0).toLocaleString()}</span>
                        <small class="d-block text-muted">
                            Subtotal: ₹${(order.subtotal || 0).toLocaleString()}
                        </small>
                    </td>
                    <td>
                        <span class="badge ${paymentStatusClass}">${order.payment_status || 'Pending'}</span>
                    </td>
                    <td>
                        <span class="badge ${statusClass}">${order.status}</span>
                    </td>
                    <td>
                        <small>${order.estimated_delivery ? new Date(order.estimated_delivery).toLocaleDateString() : 'TBD'}</small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="viewOrderDetails(${order.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary" onclick="updateOrderStatus(${order.id})">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            <a href="/orders/${order.id}/edit" class="btn btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#ordersTableBody').html(html);
}

function getOrderStatusClass(status) {
    const statusClasses = {
        'pending': 'bg-warning',
        'confirmed': 'bg-info',
        'processing': 'bg-primary',
        'shipped': 'bg-secondary',
        'delivered': 'bg-success',
        'cancelled': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
}

function getPaymentStatusClass(status) {
    const statusClasses = {
        'pending': 'bg-warning',
        'paid': 'bg-success',
        'partially_paid': 'bg-info',
        'failed': 'bg-danger',
        'refunded': 'bg-secondary'
    };
    return statusClasses[status] || 'bg-warning';
}

function renderPagination(data) {
    $('#pagination').html(data.links || '');
}

function updatePaginationInfo(data) {
    $('#showingFrom').text(data.from || 0);
    $('#showingTo').text(data.to || 0);
    $('#totalRecords').text(data.total || 0);
}

function viewOrderDetails(orderId) {
    currentOrderId = orderId;

    $('#orderDetailsBody').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Loading order details...</p>
        </div>
    `);

    $('#orderDetailsModal').modal('show');

    $.get(`/orders/${orderId}`)
        .done(function(response) {
            if (response.success) {
                displayOrderDetails(response.data);
                $('#viewFullOrderBtn').attr('onclick', `window.open('/orders/${orderId}', '_blank')`);
            }
        })
        .fail(function() {
            $('#orderDetailsBody').html('<p class="text-danger">Error loading order details</p>');
        });
}

function displayOrderDetails(order) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Order Information</h6>
                <table class="table table-sm">
                    <tr><td>Order Number:</td><td>#${order.order_number}</td></tr>
                    <tr><td>Date:</td><td>${new Date(order.created_at).toLocaleString()}</td></tr>
                    <tr><td>Status:</td><td><span class="badge ${getOrderStatusClass(order.status)}">${order.status}</span></td></tr>
                    <tr><td>Payment Status:</td><td><span class="badge ${getPaymentStatusClass(order.payment_status)}">${order.payment_status}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Order Summary</h6>
                <table class="table table-sm">
                    <tr><td>Subtotal:</td><td>₹${(order.subtotal || 0).toLocaleString()}</td></tr>
                    <tr><td>Tax:</td><td>₹${(order.tax_amount || 0).toLocaleString()}</td></tr>
                    <tr><td>Shipping:</td><td>₹${(order.shipping_amount || 0).toLocaleString()}</td></tr>
                    <tr><td><strong>Total:</strong></td><td><strong>₹${(order.total_amount || 0).toLocaleString()}</strong></td></tr>
                </table>
            </div>
        </div>

        <hr>

        <h6>Order Items</h6>
        <div class="table-responsive">
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
                    ${order.items ? order.items.map(item => `
                        <tr>
                            <td>${item.product ? item.product.name : 'Unknown Product'}</td>
                            <td>${item.quantity}</td>
                            <td>₹${item.unit_price.toLocaleString()}</td>
                            <td>₹${(item.quantity * item.unit_price).toLocaleString()}</td>
                        </tr>
                    `).join('') : '<tr><td colspan="4">No items found</td></tr>'}
                </tbody>
            </table>
        </div>

        ${order.notes ? `
            <hr>
            <h6>Notes</h6>
            <p>${order.notes}</p>
        ` : ''}
    `;

    $('#orderDetailsBody').html(html);
}

function updateOrderStatus(orderId) {
    currentOrderId = orderId;
    $('#updateStatusModal').modal('show');
}

function submitStatusUpdate() {
    const formData = new FormData($('#updateStatusForm')[0]);

    $.ajax({
        url: `/orders/${currentOrderId}/status`,
        method: 'PUT',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Order status updated successfully', 'success');
            $('#updateStatusModal').modal('hide');
            $('#updateStatusForm')[0].reset();
            loadOrders();
            loadOrderStats();
        } else {
            showAlert(response.message || 'Error updating status', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error updating order status', 'danger');
    });
}

function createNewOrder() {
    window.location.href = `/orders/create?customer_id=${currentCustomerId}`;
}

function refreshOrders() {
    loadOrders();
    loadOrderStats();
}

function exportOrders() {
    const filters = getFilters();
    const params = new URLSearchParams(filters);
    window.location.href = `/customers/${currentCustomerId}/orders/export?${params}`;
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