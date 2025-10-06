@extends('layouts.app')

@section('title', 'Inventory Management')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Inventory</li>
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
                    <h2 class="mb-1">Inventory Management</h2>
                    <p class="text-muted mb-0">Monitor and manage your product inventory levels</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.low-stock') }}" class="btn btn-outline-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>Low Stock Alerts
                    </a>
                    <a href="{{ route('inventory.valuation') }}" class="btn btn-outline-info">
                        <i class="bi bi-cash-coin me-1"></i>Stock Valuation
                    </a>
                    <a href="{{ route('inventory.movements') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clock-history me-1"></i>Movement History
                    </a>
                    <button class="btn btn-primary" onclick="showStockAdjustModal()">
                        <i class="bi bi-plus-lg me-1"></i>Adjust Stock
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4" id="statsCards">
        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Total Products</h6>
                            <h3 class="mb-0" id="totalProducts">-</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
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
                            <h6 class="text-muted text-uppercase mb-1">Low Stock Items</h6>
                            <h3 class="mb-0 text-warning" id="lowStockItems">-</h3>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
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
                            <h6 class="text-muted text-uppercase mb-1">Out of Stock</h6>
                            <h3 class="mb-0 text-danger" id="outOfStockItems">-</h3>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
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
                            <h6 class="text-muted text-uppercase mb-1">Total Stock Value</h6>
                            <h3 class="mb-0 text-success" id="totalStockValue">-</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-currency-rupee" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" placeholder="Search products...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Stock Status</label>
                            <select class="form-select" id="stock_status">
                                <option value="">All Products</option>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" id="sort_by">
                                <option value="product_name">Product Name</option>
                                <option value="quantity_in_stock">Stock Quantity</option>
                                <option value="stock_value">Stock Value</option>
                                <option value="reorder_level">Reorder Level</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Order</label>
                            <select class="form-select" id="sort_order">
                                <option value="asc">ASC</option>
                                <option value="desc">DESC</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" onclick="exportInventory()">
                                    <i class="bi bi-download me-1"></i>Export
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="refreshData()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Cost per Unit</th>
                                    <th>Stock Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> items
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

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockAdjustForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <select class="form-select" name="inventory_id" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adjustment Type</label>
                        <select class="form-select" name="adjustment_type" required>
                            <option value="add">Add Stock</option>
                            <option value="remove">Remove Stock</option>
                            <option value="set">Set Stock Level</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <select class="form-select" name="reason" required>
                            <option value="manual_adjustment">Manual Adjustment</option>
                            <option value="damaged_goods">Damaged Goods</option>
                            <option value="theft_loss">Theft/Loss</option>
                            <option value="supplier_return">Supplier Return</option>
                            <option value="customer_return">Customer Return</option>
                            <option value="stock_count">Stock Count Correction</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Adjust Stock</button>
                </div>
            </form>
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
    loadInventoryData();

    // Filter change handlers
    $('#search, #stock_status, #sort_by, #sort_order').on('change input', function() {
        currentPage = 1;
        loadInventoryData();
    });

    // Forms
    $('#stockAdjustForm').on('submit', function(e) {
        e.preventDefault();
        adjustStock();
    });

    // Load products for adjustment modal
    loadProductsForAdjustment();
});

function loadStats() {
    $.get('{{ route("inventory.stats") }}')
        .done(function(response) {
            if (response.success) {
                $('#totalProducts').text(response.data.total_products);
                $('#lowStockItems').text(response.data.low_stock_items);
                $('#outOfStockItems').text(response.data.out_of_stock_items);
                $('#totalStockValue').text('₹' + response.data.total_stock_value.toLocaleString());
            }
        })
        .fail(function() {
            // Set default values on error
            $('#totalProducts').text('0');
            $('#lowStockItems').text('0');
            $('#outOfStockItems').text('0');
            $('#totalStockValue').text('₹0');
        });
}

function loadInventoryData() {
    const filters = {
        page: currentPage,
        per_page: perPage,
        search: $('#search').val(),
        stock_status: $('#stock_status').val(),
        sort_by: $('#sort_by').val(),
        sort_order: $('#sort_order').val()
    };

    $.get('{{ route("inventory.data") }}', filters)
        .done(function(response) {
            if (response.success) {
                renderInventoryTable(response.data.data || response.data);
                if (response.data.links) {
                    renderPagination(response.data);
                    updatePaginationInfo(response.data);
                }
            }
        })
        .fail(function() {
            showAlert('Error loading inventory data', 'danger');
        });
}

function renderInventoryTable(inventory) {
    let html = '';

    if (inventory.length === 0) {
        html = '<tr><td colspan="8" class="text-center py-4">No inventory data found</td></tr>';
    } else {
        inventory.forEach(function(item) {
            const stockStatus = getStockStatus(item);
            const stockValue = (item.quantity_in_stock * item.cost_per_unit).toFixed(2);

            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">${item.product ? item.product.name : 'Unknown Product'}</h6>
                                <small class="text-muted">${item.product && item.product.category ? item.product.category.name : 'Uncategorized'}</small>
                            </div>
                        </div>
                    </td>
                    <td><code>${item.product ? item.product.sku : 'N/A'}</code></td>
                    <td>
                        <span class="fs-5 fw-bold ${stockStatus.textClass}">${item.quantity_in_stock}</span>
                        <small class="d-block text-muted">units</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${item.reorder_level}</span>
                    </td>
                    <td>₹${parseFloat(item.cost_per_unit).toFixed(2)}</td>
                    <td>
                        <strong class="text-success">₹${stockValue}</strong>
                    </td>
                    <td>
                        <span class="badge bg-${stockStatus.class}">${stockStatus.text}</span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('inventory.show', '') }}/${item.id}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('inventory.edit', '') }}/${item.id}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#inventoryTableBody').html(html);
}

function getStockStatus(inventory) {
    const stock = inventory.quantity_in_stock;
    const reorderLevel = inventory.reorder_level;

    if (stock === 0) {
        return { class: 'danger', text: 'Out of Stock', textClass: 'text-danger' };
    } else if (stock <= reorderLevel) {
        return { class: 'warning', text: 'Low Stock', textClass: 'text-warning' };
    } else {
        return { class: 'success', text: 'In Stock', textClass: 'text-success' };
    }
}

function renderPagination(data) {
    $('#pagination').html(data.links || '');
}

function updatePaginationInfo(data) {
    $('#showingFrom').text(data.from || 0);
    $('#showingTo').text(data.to || 0);
    $('#totalRecords').text(data.total || 0);
}

function showStockAdjustModal() {
    $('#stockAdjustModal').modal('show');
}

function adjustStock() {
    const formData = new FormData($('#stockAdjustForm')[0]);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: '{{ route("inventory.adjust") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            showAlert(response.message, 'success');
            $('#stockAdjustModal').modal('hide');
            $('#stockAdjustForm')[0].reset();
            loadInventoryData();
            loadStats();
        } else {
            showAlert(response.message, 'danger');
        }
    })
    .fail(function() {
        showAlert('Error adjusting stock', 'danger');
    });
}

function loadProductsForAdjustment() {
    $.get('{{ route("inventory.data") }}?all=1')
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Select Product</option>';
                const data = response.data.data || response.data;
                data.forEach(function(item) {
                    if (item.product) {
                        options += `<option value="${item.id}">${item.product.name} (${item.product.sku})</option>`;
                    }
                });
                $('select[name="inventory_id"]').html(options);
            }
        });
}

function exportInventory() {
    const filters = {
        search: $('#search').val(),
        stock_status: $('#stock_status').val()
    };

    const params = new URLSearchParams(filters);
    window.location.href = `{{ route('inventory.export') }}?${params}`;
}

function refreshData() {
    loadStats();
    loadInventoryData();
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