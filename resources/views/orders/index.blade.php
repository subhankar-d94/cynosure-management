@extends('layouts.app')

@section('title', 'Orders')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
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
                <div>
                    <h2 class="mb-1">Orders</h2>
                    <p class="text-muted mb-0">Manage and track all customer orders</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="exportOrders()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <button class="btn btn-outline-secondary" onclick="bulkActions()">
                        <i class="bi bi-list-check me-1"></i>Bulk Actions
                    </button>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>New Order
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
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
                            <h6 class="text-success text-uppercase mb-1">Total Revenue</h6>
                            <h3 class="mb-0 text-success" id="totalRevenue">-</h3>
                            <small class="text-muted">All orders</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-currency-rupee" style="font-size: 2.5rem;"></i>
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
                            <h6 class="text-warning text-uppercase mb-1">Pending Orders</h6>
                            <h3 class="mb-0 text-warning" id="pendingOrders">-</h3>
                            <small class="text-muted">Awaiting action</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-clock-history" style="font-size: 2.5rem;"></i>
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
                            <h6 class="text-info text-uppercase mb-1">Average Order</h6>
                            <h3 class="mb-0 text-info" id="averageOrder">-</h3>
                            <small class="text-muted">Per order value</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-graph-up" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" placeholder="Order number, customer...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Payment Status</label>
                            <select class="form-select" id="payment_status">
                                <option value="">All Payments</option>
                                <option value="pending">Pending</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" id="date_range">
                                <option value="" selected>All Orders</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-2" id="customDateRange" style="display: none;">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" id="start_date">
                        </div>
                        <div class="col-md-1" id="customDateRangeEnd" style="display: none;">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" id="end_date">
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
                    <h5 class="card-title mb-0">Order List</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" style="display: none;">
                            <i class="bi bi-trash"></i> Delete Selected
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshTable()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> orders
                        </div>
                        <nav id="pagination">
                            <!-- Pagination will be loaded via AJAX -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="quickViewBody">
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

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkActionsForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select class="form-select" name="action" required>
                            <option value="">Select Action</option>
                            <option value="update_status">Update Status</option>
                            <option value="export">Export Selected</option>
                            <option value="delete">Delete Orders</option>
                            <option value="duplicate">Duplicate Orders</option>
                        </select>
                    </div>
                    <div class="mb-3" id="statusField" style="display: none;">
                        <label class="form-label">New Status</label>
                        <select class="form-select" name="new_status">
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted">Selected orders: <span id="selectedCount">0</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Execute</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected order(s)? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentPage = 1;
let perPage = 15;
let selectedOrders = [];
let currentOrderId = null;

$(document).ready(function() {
    loadStats();
    loadOrders();

    // Filter change handlers
    $('#search, #status, #payment_status, #date_range').on('change input', function() {
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

    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.order-checkbox').prop('checked', isChecked);
        updateSelectedOrders();
    });

    // Individual checkbox change
    $(document).on('change', '.order-checkbox', function() {
        updateSelectedOrders();
    });

    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        if (selectedOrders.length > 0) {
            $('#deleteModal').modal('show');
        }
    });

    // Confirm delete
    $('#confirmDeleteBtn').on('click', function() {
        deleteSelectedOrders();
    });

    // Update status form
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        submitStatusUpdate();
    });

    // Bulk actions form
    $('#bulkActionsForm').on('submit', function(e) {
        e.preventDefault();
        executeBulkAction();
    });

    // Bulk actions action change
    $('select[name="action"]').on('change', function() {
        $('#statusField').toggle($(this).val() === 'update_status');
    });
});

function loadStats() {
    $.get('{{ route("orders.stats") }}')
        .done(function(response) {
            if (response.success) {
                $('#totalOrders').text(response.data.total_orders || 0);
                $('#totalRevenue').text('₹' + (response.data.total_revenue || 0).toLocaleString());
                $('#pendingOrders').text(response.data.pending_orders || 0);
                $('#averageOrder').text('₹' + (response.data.average_order || 0).toLocaleString());
            }
        })
        .fail(function() {
            // Set default values on error
            $('#totalOrders').text('0');
            $('#totalRevenue').text('₹0');
            $('#pendingOrders').text('0');
            $('#averageOrder').text('₹0');
        });
}

function getFilters() {
    const filters = {
        search: $('#search').val() || '',
        status: $('#status').val() || '',
        payment_status: $('#payment_status').val() || '',
        date_range: $('#date_range').val() || '',
        page: currentPage,
        per_page: perPage
    };

    if (filters.date_range === 'custom') {
        filters.date_from = $('#start_date').val();
        filters.date_to = $('#end_date').val();
    } else if (filters.date_range) {
        // Calculate dates based on the selected range
        const today = new Date();
        const formatDate = (date) => date.toISOString().split('T')[0];

        switch (filters.date_range) {
            case 'today':
                filters.date_from = formatDate(today);
                filters.date_to = formatDate(today);
                break;
            case 'week':
                const weekStart = new Date(today);
                weekStart.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekStart.getDate() + 6); // End of week (Saturday)
                filters.date_from = formatDate(weekStart);
                filters.date_to = formatDate(weekEnd);
                break;
            case 'month':
                const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                filters.date_from = formatDate(monthStart);
                filters.date_to = formatDate(monthEnd);
                break;
            case 'quarter':
                const quarter = Math.floor(today.getMonth() / 3);
                const quarterStart = new Date(today.getFullYear(), quarter * 3, 1);
                const quarterEnd = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                filters.date_from = formatDate(quarterStart);
                filters.date_to = formatDate(quarterEnd);
                break;
        }
    }

    return filters;
}

function loadOrders() {
    const filters = getFilters();

    $.get('{{ route("orders.data") }}', filters)
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
        html = '<tr><td colspan="9" class="text-center py-4">No orders found</td></tr>';
    } else {
        orders.forEach(function(order) {
            const statusClass = getOrderStatusClass(order.status);
            const paymentStatusClass = getPaymentStatusClass(order.payment_status);

            html += `
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input order-checkbox" value="${order.id}">
                    </td>
                    <td>
                        <a href="{{ route('orders.show', '') }}/${order.id}" class="fw-bold text-decoration-none">
                            #${order.order_number}
                        </a>
                    </td>
                    <td>
                        <div>
                            <h6 class="mb-0">${order.customer ? order.customer.name : 'Walk-in Customer'}</h6>
                            <small class="text-muted">${order.customer ? order.customer.phone : 'No email'}</small>
                        </div>
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
                            Tax: ₹${(order.tax_amount || 0).toLocaleString()}
                        </small>
                    </td>
                    <td>
                        <span class="badge ${paymentStatusClass}">${order.payment_status || 'Pending'}</span>
                    </td>
                    <td>
                        <span class="badge ${statusClass}">${order.status}</span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="quickView(${order.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary" onclick="updateStatus(${order.id})">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            <a href="/orders/${order.id}/edit" class="btn btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/orders/${order.id}/invoice">View Invoice</a></li>
                                    <li><a class="dropdown-item" href="/orders/${order.id}/print">Print Order</a></li>
                                    <li><button class="dropdown-item" onclick="duplicateOrder(${order.id})">Duplicate</button></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button class="dropdown-item text-danger" onclick="deleteOrder(${order.id})">Delete</button></li>
                                </ul>
                            </div>
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
        'in_progress': 'bg-primary',
        'completed': 'bg-success',
        'cancelled': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
}

function getPaymentStatusClass(status) {
    const statusClasses = {
        'pending': 'bg-warning',
        'partial': 'bg-info',
        'paid': 'bg-success',
        'failed': 'bg-danger',
        'refunded': 'bg-secondary'
    };
    return statusClasses[status] || 'bg-warning';
}

function updateSelectedOrders() {
    selectedOrders = [];
    $('.order-checkbox:checked').each(function() {
        selectedOrders.push($(this).val());
    });

    if (selectedOrders.length > 0) {
        $('#bulkDeleteBtn').show();
    } else {
        $('#bulkDeleteBtn').hide();
    }

    $('#selectedCount').text(selectedOrders.length);

    // Update select all checkbox
    const totalCheckboxes = $('.order-checkbox').length;
    const checkedCheckboxes = $('.order-checkbox:checked').length;

    if (checkedCheckboxes === 0) {
        $('#selectAll').prop('indeterminate', false).prop('checked', false);
    } else if (checkedCheckboxes === totalCheckboxes) {
        $('#selectAll').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#selectAll').prop('indeterminate', true);
    }
}

function quickView(orderId) {
    currentOrderId = orderId;

    $('#quickViewBody').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Loading order details...</p>
        </div>
    `);

    $('#quickViewModal').modal('show');

    $.get(`/orders/${orderId}/details`)
        .done(function(response) {
            console.log('Order details response:', response);
            if (response.success && response.data) {
                displayOrderDetails(response.data);
                $('#viewFullOrderBtn').attr('onclick', `window.open('{{ route('orders.show', '') }}/${orderId}', '_blank')`);
            } else {
                console.error('Invalid response format:', response);
                $('#quickViewBody').html('<p class="text-danger">Invalid response format</p>');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Ajax error:', xhr, status, error);
            $('#quickViewBody').html('<p class="text-danger">Error loading order details</p>');
        });
}

function displayOrderDetails(order) {
    console.log('Displaying order details for:', order);

    if (!order) {
        $('#quickViewBody').html('<p class="text-danger">Order data is missing</p>');
        return;
    }

    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Order Information</h6>
                <table class="table table-sm">
                    <tr><td>Order Number:</td><td>#${order.order_number || 'N/A'}</td></tr>
                    <tr><td>Customer Name:</td><td>${order.customer_name ? order.customer_name : 'Unknown'}</td></tr>
                    <tr><td>Date:</td><td>${new Date(order.created_at).toLocaleString()}</td></tr>
                    <tr><td>Status:</td><td><span class="badge ${getOrderStatusClass(order.status)}">${order.status}</span></td></tr>
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
                            <td>${item.product_name ? item.product_name : 'Unknown Product'}</td>
                            <td>${item.quantity}</td>
                            <td>₹${item.unit_price.toLocaleString()}</td>
                            <td>₹${(item.quantity * item.unit_price).toLocaleString()}</td>
                        </tr>
                    `).join('') : '<tr><td colspan="4">No items found</td></tr>'}
                </tbody>
            </table>
        </div>
    `;

    $('#quickViewBody').html(html);
}

function updateStatus(orderId) {
    currentOrderId = orderId;
    $('#updateStatusModal').modal('show');
}

function submitStatusUpdate() {
    const formData = new FormData($('#updateStatusForm')[0]);

    $.ajax({
        url: '{{ route("orders.update-status") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Order-ID': currentOrderId
        }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Order status updated successfully', 'success');
            $('#updateStatusModal').modal('hide');
            $('#updateStatusForm')[0].reset();
            loadOrders();
            loadStats();
        } else {
            showAlert(response.message || 'Error updating status', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error updating order status', 'danger');
    });
}

function bulkActions() {
    if (selectedOrders.length === 0) {
        showAlert('Please select orders first', 'warning');
        return;
    }
    $('#bulkActionsModal').modal('show');
}

function executeBulkAction() {
    const formData = new FormData($('#bulkActionsForm')[0]);
    formData.append('order_ids', JSON.stringify(selectedOrders));

    $.ajax({
        url: '{{ route("orders.bulk-action") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Bulk action completed successfully', 'success');
            $('#bulkActionsModal').modal('hide');
            $('#bulkActionsForm')[0].reset();
            selectedOrders = [];
            $('#selectAll').prop('checked', false);
            $('#bulkDeleteBtn').hide();
            loadOrders();
            loadStats();
        } else {
            showAlert(response.message || 'Error executing bulk action', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error executing bulk action', 'danger');
    });
}

function deleteOrder(orderId) {
    selectedOrders = [orderId];
    $('#deleteModal').modal('show');
}

function deleteSelectedOrders() {
    if (selectedOrders.length === 0) return;

    const deletePromises = selectedOrders.map(id => {
        return $.ajax({
            url: `{{ route('orders.destroy', '') }}/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    Promise.all(deletePromises)
        .then(() => {
            showAlert('Order(s) deleted successfully', 'success');
            $('#deleteModal').modal('hide');
            selectedOrders = [];
            $('#bulkDeleteBtn').hide();
            $('#selectAll').prop('checked', false);
            loadOrders();
            loadStats();
        })
        .catch(() => {
            showAlert('Error deleting order(s)', 'danger');
        });
}

function duplicateOrder(orderId) {
    $.ajax({
        url: `{{ route('orders.duplicate', '') }}/${orderId}`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Order duplicated successfully', 'success');
            loadOrders();
            loadStats();
        } else {
            showAlert(response.message || 'Error duplicating order', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error duplicating order', 'danger');
    });
}

function renderPagination(data) {
    $('#pagination').html(data.links || '');
}

function updatePaginationInfo(data) {
    $('#showingFrom').text(data.from || 0);
    $('#showingTo').text(data.to || 0);
    $('#totalRecords').text(data.total || 0);
}

function refreshTable() {
    loadOrders();
    loadStats();
}

function exportOrders() {
    const filters = getFilters();
    const params = new URLSearchParams(filters);
    window.location.href = `{{ route('orders.export') }}?${params}`;
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
