@extends('layouts.app')

@section('title', 'Inventory Movement History')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
        <li class="breadcrumb-item active">Movement History</li>
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
                    <h2 class="mb-1">Inventory Movement History</h2>
                    <p class="text-muted mb-0">Track all inventory changes and stock movements</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="exportMovements()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Inventory
                    </a>
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
                            <label class="form-label">Search Product</label>
                            <input type="text" class="form-control" id="search" placeholder="Search products...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Movement Type</label>
                            <select class="form-select" id="movement_type">
                                <option value="">All Types</option>
                                <option value="in">Stock In</option>
                                <option value="out">Stock Out</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Reason</label>
                            <select class="form-select" id="reason_filter">
                                <option value="">All Reasons</option>
                                <option value="manual_adjustment">Manual Adjustment</option>
                                <option value="supplier_delivery">Supplier Delivery</option>
                                <option value="customer_return">Customer Return</option>
                                <option value="damaged_goods">Damaged Goods</option>
                                <option value="order_fulfillment">Order Fulfillment</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" id="date_range">
                                <option value="today">Today</option>
                                <option value="week" selected>This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="customDateRange" style="display: none;">
                            <label class="form-label">Custom Range</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control" id="start_date">
                                <input type="date" class="form-control" id="end_date">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Movement Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Total Movements</h6>
                            <h3 class="mb-0" id="totalMovements">-</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-arrow-left-right" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-success text-uppercase mb-1">Stock Added</h6>
                            <h3 class="mb-0 text-success" id="stockAdded">-</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-danger text-uppercase mb-1">Stock Removed</h6>
                            <h3 class="mb-0 text-danger" id="stockRemoved">-</h3>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-arrow-down-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-info text-uppercase mb-1">Value Impact</h6>
                            <h3 class="mb-0 text-info" id="valueImpact">-</h3>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-currency-rupee" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Movement History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Movement History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Reason</th>
                                    <th>User</th>
                                    <th>Balance After</th>
                                    <th>Value Impact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="movementsTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> movements
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

<!-- Movement Details Modal -->
<div class="modal fade" id="movementDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Movement Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="movementDetailsBody">
                <!-- Movement details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentPage = 1;
let perPage = 15;

$(document).ready(function() {
    loadStats();
    loadMovements();

    // Filter change handlers
    $('#search, #movement_type, #reason_filter, #date_range').on('change input', function() {
        currentPage = 1;
        loadMovements();
    });

    // Custom date range toggle
    $('#date_range').on('change', function() {
        $('#customDateRange').toggle($(this).val() === 'custom');
        if ($(this).val() !== 'custom') {
            loadMovements();
        }
    });

    // Custom date inputs
    $('#start_date, #end_date').on('change', function() {
        if ($('#date_range').val() === 'custom') {
            loadMovements();
        }
    });
});

function loadStats() {
    const filters = getFilters();

    $.get('{{ route("inventory.stats") }}?movements=1', filters)
        .done(function(response) {
            if (response.success) {
                $('#totalMovements').text(response.data.total_movements || 0);
                $('#stockAdded').text(response.data.stock_added || 0);
                $('#stockRemoved').text(response.data.stock_removed || 0);
                $('#valueImpact').text('₹' + (response.data.value_impact || 0).toLocaleString());
            }
        })
        .fail(function() {
            // Set default values on error
            $('#totalMovements').text('0');
            $('#stockAdded').text('0');
            $('#stockRemoved').text('0');
            $('#valueImpact').text('₹0');
        });
}

function getFilters() {
    const filters = {
        search: $('#search').val(),
        movement_type: $('#movement_type').val(),
        reason_filter: $('#reason_filter').val(),
        date_range: $('#date_range').val()
    };

    if (filters.date_range === 'custom') {
        filters.start_date = $('#start_date').val();
        filters.end_date = $('#end_date').val();
    }

    return filters;
}

function loadMovements() {
    const filters = {
        ...getFilters(),
        page: currentPage,
        per_page: perPage
    };

    $.get('{{ route("inventory.movements") }}', filters)
        .done(function(response) {
            if (response.success) {
                renderMovementsTable(response.data.data || response.data);
                if (response.data.links) {
                    renderPagination(response.data);
                    updatePaginationInfo(response.data);
                }
            }
        })
        .fail(function() {
            showAlert('Error loading movement history', 'danger');
        });
}

function renderMovementsTable(movements) {
    let html = '';

    if (movements.length === 0) {
        html = '<tr><td colspan="9" class="text-center py-4">No movements found</td></tr>';
    } else {
        movements.forEach(function(movement) {
            const typeIcon = movement.type === 'in' ? 'bi-arrow-up-circle text-success' : 'bi-arrow-down-circle text-danger';
            const typeText = movement.type === 'in' ? 'Stock In' : 'Stock Out';
            const quantityClass = movement.type === 'in' ? 'text-success' : 'text-danger';
            const sign = movement.type === 'in' ? '+' : '-';
            const valueImpact = Math.abs(movement.quantity * (movement.unit_cost || 0));

            html += `
                <tr>
                    <td>
                        <div>
                            <strong>${new Date(movement.created_at).toLocaleDateString()}</strong>
                            <small class="d-block text-muted">${new Date(movement.created_at).toLocaleTimeString()}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <h6 class="mb-0">${movement.product ? movement.product.name : 'Unknown Product'}</h6>
                            <small class="text-muted">${movement.product ? movement.product.sku : 'N/A'}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-${movement.type === 'in' ? 'success' : 'danger'}">
                            <i class="bi ${typeIcon} me-1"></i>${typeText}
                        </span>
                    </td>
                    <td>
                        <span class="fw-bold ${quantityClass}">${sign}${movement.quantity}</span>
                        <small class="d-block text-muted">units</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${formatReason(movement.reason)}</span>
                    </td>
                    <td>
                        <small>${movement.user ? movement.user.name : 'System'}</small>
                    </td>
                    <td>
                        <strong>${movement.balance_after || 0}</strong>
                        <small class="d-block text-muted">units</small>
                    </td>
                    <td>
                        <span class="${movement.type === 'in' ? 'text-success' : 'text-danger'}">
                            ${sign}₹${valueImpact.toFixed(2)}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="showMovementDetails(${movement.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $('#movementsTableBody').html(html);
}

function formatReason(reason) {
    const reasonMap = {
        'manual_adjustment': 'Manual Adjustment',
        'supplier_delivery': 'Supplier Delivery',
        'customer_return': 'Customer Return',
        'damaged_goods': 'Damaged Goods',
        'order_fulfillment': 'Order Fulfillment',
        'theft_loss': 'Theft/Loss',
        'stock_count': 'Stock Count',
        'other': 'Other'
    };

    return reasonMap[reason] || reason.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function showMovementDetails(movementId) {
    // This would load detailed movement information
    $('#movementDetailsBody').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading movement details...</p>
        </div>
    `);

    $('#movementDetailsModal').modal('show');

    // In a real implementation, this would fetch detailed movement data
    setTimeout(function() {
        $('#movementDetailsBody').html(`
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Movement details feature will be implemented with full audit trail information.
            </div>
        `);
    }, 1000);
}

function renderPagination(data) {
    $('#pagination').html(data.links || '');
}

function updatePaginationInfo(data) {
    $('#showingFrom').text(data.from || 0);
    $('#showingTo').text(data.to || 0);
    $('#totalRecords').text(data.total || 0);
}

function exportMovements() {
    const filters = getFilters();
    const params = new URLSearchParams(filters);
    window.location.href = `{{ route('inventory.export') }}?movements=1&${params}`;
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