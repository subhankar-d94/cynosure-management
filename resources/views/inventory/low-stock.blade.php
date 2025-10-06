@extends('layouts.app')

@section('title', 'Low Stock Alerts')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
        <li class="breadcrumb-item active">Low Stock Alerts</li>
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
                    <h2 class="mb-1 text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts
                    </h2>
                    <p class="text-muted mb-0">Products that need immediate attention due to low inventory levels</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="bulkCreatePurchaseOrder()">
                        <i class="bi bi-bag-plus me-1"></i>Bulk Purchase Order
                    </button>
                    <button class="btn btn-outline-success" onclick="showBulkRestockModal()">
                        <i class="bi bi-arrow-up-circle me-1"></i>Bulk Restock
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-warning text-uppercase mb-1">Low Stock Items</h6>
                            <h3 class="mb-0 text-warning" id="lowStockCount">-</h3>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-danger text-uppercase mb-1">Out of Stock</h6>
                            <h3 class="mb-0 text-danger" id="outOfStockCount">-</h3>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-info text-uppercase mb-1">Restock Value</h6>
                            <h3 class="mb-0 text-info" id="restockValue">-</h3>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-currency-rupee" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-primary text-uppercase mb-1">Affected Categories</h6>
                            <h3 class="mb-0 text-primary" id="affectedCategories">-</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-tags" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-circle me-2"></i>Critical Items (Out of Stock)
                    </h5>
                </div>
                <div class="card-body" id="criticalItems">
                    <!-- Critical items will be loaded here -->
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
                            <input type="text" class="form-control" id="search" placeholder="Search products...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="category_filter">
                                <option value="">All Categories</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Priority</label>
                            <select class="form-select" id="priority_filter">
                                <option value="">All Priorities</option>
                                <option value="critical">Critical (Out of Stock)</option>
                                <option value="high">High (≤ 50% of Reorder Level)</option>
                                <option value="medium">Medium (≤ Reorder Level)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" id="sort_by">
                                <option value="priority">Priority</option>
                                <option value="quantity_shortage">Stock Shortage</option>
                                <option value="days_remaining">Days Remaining</option>
                                <option value="product_name">Product Name</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Order</label>
                            <select class="form-select" id="sort_order">
                                <option value="desc">DESC</option>
                                <option value="asc">ASC</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="refreshData()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="exportLowStock()">
                                    <i class="bi bi-download"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row mb-3" id="bulkActions" style="display: none;">
        <div class="col-12">
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span><strong id="selectedCount">0</strong> items selected</span>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-primary" onclick="showBulkRestockModal()">
                        <i class="bi bi-arrow-up-circle me-1"></i>Bulk Restock
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="bulkCreateReorderAlert()">
                        <i class="bi bi-bell me-1"></i>Create Alerts
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="bulkCreatePurchaseOrder()">
                        <i class="bi bi-bag-plus me-1"></i>Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Low Stock Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Priority</th>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Shortage</th>
                                    <th>Suggested Order</th>
                                    <th>Est. Value</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="lowStockTableBody">
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

<!-- Bulk Restock Modal -->
<div class="modal fade" id="bulkRestockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Restock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkRestockForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        This will restock selected items to their optimal levels.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Restock Strategy</label>
                        <select class="form-select" name="restock_strategy" required>
                            <option value="to_reorder_level">Stock to Reorder Level</option>
                            <option value="to_optimal_level">Stock to Optimal Level (2x Reorder)</option>
                            <option value="custom_multiplier">Custom Multiplier of Reorder Level</option>
                        </select>
                    </div>
                    <div class="mb-3" id="customMultiplierField" style="display: none;">
                        <label class="form-label">Multiplier</label>
                        <input type="number" class="form-control" name="multiplier" min="1" max="10" step="0.5" value="2">
                        <div class="form-text">Stock will be set to Reorder Level × Multiplier</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <select class="form-select" name="reason" required>
                            <option value="low_stock_restock">Low Stock Restock</option>
                            <option value="supplier_delivery">Supplier Delivery</option>
                            <option value="emergency_restock">Emergency Restock</option>
                            <option value="seasonal_preparation">Seasonal Preparation</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Apply Restock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Restock Modal -->
<div class="modal fade" id="quickRestockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Restock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickRestockForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="quickProductName" readonly>
                        <input type="hidden" name="inventory_id" id="quickInventoryId">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" id="quickCurrentStock" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Reorder Level</label>
                            <input type="text" class="form-control" id="quickReorderLevel" readonly>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Restock Quantity</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                        <div class="form-text">Quantity to add to current stock</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <select class="form-select" name="reason" required>
                            <option value="low_stock_restock">Low Stock Restock</option>
                            <option value="supplier_delivery">Supplier Delivery</option>
                            <option value="emergency_restock">Emergency Restock</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Restock Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedItems = new Set();
let currentPage = 1;
let perPage = 15;

$(document).ready(function() {
    loadStats();
    loadLowStockData();
    loadCriticalItems();

    // Filter change handlers
    $('#search, #category_filter, #priority_filter, #sort_by, #sort_order').on('change input', function() {
        currentPage = 1;
        loadLowStockData();
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.item-checkbox').prop('checked', isChecked);
        updateSelectedItems();
    });

    // Restock strategy change
    $('select[name="restock_strategy"]').on('change', function() {
        $('#customMultiplierField').toggle($(this).val() === 'custom_multiplier');
    });

    // Forms
    $('#bulkRestockForm').on('submit', function(e) {
        e.preventDefault();
        bulkRestock();
    });

    $('#quickRestockForm').on('submit', function(e) {
        e.preventDefault();
        quickRestock();
    });
});

function loadStats() {
    $.get('{{ route("inventory.stats") }}?low_stock=1')
        .done(function(response) {
            if (response.success) {
                $('#lowStockCount').text(response.data.low_stock_items);
                $('#outOfStockCount').text(response.data.out_of_stock_items);
                $('#restockValue').text('₹' + response.data.estimated_restock_value.toLocaleString());
                $('#affectedCategories').text(response.data.affected_categories);
            }
        })
        .fail(function() {
            // Set default values on error
            $('#lowStockCount').text('0');
            $('#outOfStockCount').text('0');
            $('#restockValue').text('₹0');
            $('#affectedCategories').text('0');
        });
}

function loadCriticalItems() {
    $.get('{{ route("inventory.data") }}?stock_status=out_of_stock&limit=5')
        .done(function(response) {
            if (response.success) {
                let html = '';
                const items = response.data.data || response.data;

                if (items.length === 0) {
                    html = '<p class="text-muted mb-0">No critical items found. Great job!</p>';
                } else {
                    html = '<div class="row g-2">';
                    items.forEach(function(item) {
                        html += `
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                    <div>
                                        <strong>${item.product ? item.product.name : 'Unknown'}</strong>
                                        <small class="d-block text-muted">${item.product ? item.product.sku : 'N/A'}</small>
                                    </div>
                                    <button class="btn btn-sm btn-danger" onclick="quickRestockItem(${item.id})">
                                        <i class="bi bi-arrow-up-circle me-1"></i>Restock
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }

                $('#criticalItems').html(html);
            }
        });
}

function loadLowStockData() {
    const filters = {
        page: currentPage,
        per_page: perPage,
        search: $('#search').val(),
        category_filter: $('#category_filter').val(),
        priority_filter: $('#priority_filter').val(),
        sort_by: $('#sort_by').val(),
        sort_order: $('#sort_order').val(),
        low_stock_only: true
    };

    $.get('{{ route("inventory.data") }}', filters)
        .done(function(response) {
            if (response.success) {
                renderLowStockTable(response.data.data || response.data);
                if (response.data.links) {
                    renderPagination(response.data);
                    updatePaginationInfo(response.data);
                }
            }
        })
        .fail(function() {
            showAlert('Error loading low stock data', 'danger');
        });
}

function renderLowStockTable(inventory) {
    let html = '';

    if (inventory.length === 0) {
        html = '<tr><td colspan="9" class="text-center py-4">No low stock items found</td></tr>';
    } else {
        inventory.forEach(function(item) {
            const priority = getPriority(item);
            const shortage = Math.max(0, item.reorder_level - item.quantity_in_stock);
            const suggestedOrder = shortage + Math.floor(item.reorder_level * 0.5); // 150% of reorder level
            const estimatedValue = suggestedOrder * item.cost_per_unit;

            html += `
                <tr class="table-${priority.class}">
                    <td>
                        <input type="checkbox" class="form-check-input item-checkbox"
                               value="${item.id}" onchange="updateSelectedItems()">
                    </td>
                    <td>
                        <span class="badge bg-${priority.class}">${priority.text}</span>
                    </td>
                    <td>
                        <div>
                            <h6 class="mb-0">${item.product ? item.product.name : 'Unknown Product'}</h6>
                            <small class="text-muted">
                                ${item.product ? item.product.sku : 'N/A'} |
                                ${item.product && item.product.category ? item.product.category.name : 'Uncategorized'}
                            </small>
                        </div>
                    </td>
                    <td>
                        <span class="fs-6 fw-bold text-${priority.class}">${item.quantity_in_stock}</span>
                        <small class="d-block text-muted">units</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${item.reorder_level}</span>
                    </td>
                    <td>
                        <span class="text-danger fw-bold">${shortage}</span>
                        <small class="d-block text-muted">units short</small>
                    </td>
                    <td>
                        <span class="text-success fw-bold">${suggestedOrder}</span>
                        <small class="d-block text-muted">units</small>
                    </td>
                    <td>
                        <strong>₹${estimatedValue.toFixed(2)}</strong>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-success" onclick="quickRestockItem(${item.id})">
                                <i class="bi bi-arrow-up-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="createReorderAlert(${item.id})">
                                <i class="bi bi-bell"></i>
                            </button>
                            <a href="{{ route('inventory.show', '') }}/${item.id}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#lowStockTableBody').html(html);
}

function getPriority(inventory) {
    const stock = inventory.quantity_in_stock;
    const reorderLevel = inventory.reorder_level;

    if (stock === 0) {
        return { class: 'danger', text: 'Critical' };
    } else if (stock <= reorderLevel * 0.5) {
        return { class: 'warning', text: 'High' };
    } else {
        return { class: 'info', text: 'Medium' };
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

function updateSelectedItems() {
    selectedItems.clear();
    $('.item-checkbox:checked').each(function() {
        selectedItems.add($(this).val());
    });

    $('#selectedCount').text(selectedItems.size);
    $('#bulkActions').toggle(selectedItems.size > 0);
}

function quickRestockItem(inventoryId) {
    $.get(`{{ route('inventory.details', '') }}/${inventoryId}`)
        .done(function(response) {
            if (response.success) {
                const data = response.data;
                $('#quickProductName').val(data.product.name);
                $('#quickInventoryId').val(data.id);
                $('#quickCurrentStock').val(data.quantity_in_stock);
                $('#quickReorderLevel').val(data.reorder_level);

                // Suggest optimal restock quantity
                const shortage = Math.max(0, data.reorder_level - data.quantity_in_stock);
                const suggested = shortage + Math.floor(data.reorder_level * 0.5);
                $('input[name="quantity"]').val(suggested);

                $('#quickRestockModal').modal('show');
            }
        });
}

function showBulkRestockModal() {
    if (selectedItems.size === 0) {
        showAlert('Please select items to restock', 'warning');
        return;
    }
    $('#bulkRestockModal').modal('show');
}

function bulkRestock() {
    const formData = new FormData($('#bulkRestockForm')[0]);
    formData.append('inventory_ids', JSON.stringify(Array.from(selectedItems)));
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: '{{ route("inventory.bulk-adjust") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            showAlert(response.message, 'success');
            $('#bulkRestockModal').modal('hide');
            $('#bulkRestockForm')[0].reset();
            loadLowStockData();
            loadStats();
            loadCriticalItems();
            selectedItems.clear();
            updateSelectedItems();
        } else {
            showAlert(response.message, 'danger');
        }
    })
    .fail(function() {
        showAlert('Error performing bulk restock', 'danger');
    });
}

function quickRestock() {
    const formData = new FormData($('#quickRestockForm')[0]);
    formData.append('adjustment_type', 'add');
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
            $('#quickRestockModal').modal('hide');
            $('#quickRestockForm')[0].reset();
            loadLowStockData();
            loadStats();
            loadCriticalItems();
        } else {
            showAlert(response.message, 'danger');
        }
    })
    .fail(function() {
        showAlert('Error restocking item', 'danger');
    });
}

function createReorderAlert(inventoryId) {
    $.post(`{{ route('inventory.reorder', '') }}/${inventoryId}`, {
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (response.success) {
            showAlert(response.message, 'success');
        } else {
            showAlert(response.message, 'danger');
        }
    })
    .fail(function() {
        showAlert('Error creating reorder alert', 'danger');
    });
}

function bulkCreateReorderAlert() {
    if (selectedItems.size === 0) {
        showAlert('Please select items', 'warning');
        return;
    }

    // Implementation for bulk reorder alerts
    showAlert('Bulk reorder alerts feature coming soon', 'info');
}

function bulkCreatePurchaseOrder() {
    if (selectedItems.size === 0) {
        showAlert('Please select items', 'warning');
        return;
    }

    // Implementation for bulk purchase order creation
    showAlert('Bulk purchase order feature coming soon', 'info');
}

function exportLowStock() {
    const filters = {
        search: $('#search').val(),
        category_filter: $('#category_filter').val(),
        priority_filter: $('#priority_filter').val(),
        low_stock_only: true
    };

    const params = new URLSearchParams(filters);
    window.location.href = `{{ route('inventory.export') }}?${params}`;
}

function refreshData() {
    loadStats();
    loadLowStockData();
    loadCriticalItems();
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