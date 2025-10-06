@extends('layouts.app')

@section('title', 'Inventory Details - ' . $inventory->product->name)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
        <li class="breadcrumb-item active">{{ $inventory->product->name }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <h2 class="mb-0 me-3">{{ $inventory->product->name }}</h2>
                        @php
                            $stockStatus = $inventory->quantity_in_stock == 0 ? 'danger' :
                                          ($inventory->quantity_in_stock <= $inventory->reorder_level ? 'warning' : 'success');
                            $statusText = $inventory->quantity_in_stock == 0 ? 'Out of Stock' :
                                         ($inventory->quantity_in_stock <= $inventory->reorder_level ? 'Low Stock' : 'In Stock');
                        @endphp
                        <span class="badge bg-{{ $stockStatus }} fs-6">{{ $statusText }}</span>
                    </div>
                    <p class="text-muted mb-0">
                        <strong>SKU:</strong> <code>{{ $inventory->product->sku }}</code> |
                        <strong>Category:</strong> {{ $inventory->product->category->name ?? 'Uncategorized' }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success" onclick="showStockAdjustModal()">
                        <i class="bi bi-boxes me-1"></i>Adjust Stock
                    </button>
                    <button class="btn btn-outline-warning" onclick="createReorderAlert()">
                        <i class="bi bi-bell me-1"></i>Reorder Alert
                    </button>
                    <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Settings
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots me-1"></i>More
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportHistory()">
                                <i class="bi bi-download me-2"></i>Export History</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printLabel()">
                                <i class="bi bi-printer me-2"></i>Print Label</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-warning" href="#" onclick="createPurchaseOrder()">
                                <i class="bi bi-bag-plus me-2"></i>Create Purchase Order</a></li>
                        </ul>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Current Stock Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>Current Stock Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-{{ $stockStatus }}">{{ $inventory->quantity_in_stock }}</h2>
                                <small class="text-muted">Current Stock</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-info">{{ $inventory->reorder_level }}</h2>
                                <small class="text-muted">Reorder Level</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-primary">₹{{ number_format($inventory->cost_per_unit, 2) }}</h2>
                                <small class="text-muted">Cost per Unit</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-success">₹{{ number_format($inventory->quantity_in_stock * $inventory->cost_per_unit, 2) }}</h2>
                                <small class="text-muted">Total Value</small>
                            </div>
                        </div>
                    </div>

                    @if($inventory->quantity_in_stock <= $inventory->reorder_level)
                    <div class="alert alert-{{ $stockStatus }} mt-4">
                        <i class="bi bi-{{ $inventory->quantity_in_stock == 0 ? 'x-circle' : 'exclamation-triangle' }} me-2"></i>
                        <strong>{{ $inventory->quantity_in_stock == 0 ? 'Critical Alert:' : 'Warning:' }}</strong>
                        {{ $inventory->quantity_in_stock == 0 ? 'This product is out of stock!' : 'Stock level is at or below the reorder threshold.' }}
                        @if($inventory->quantity_in_stock > 0)
                            You need {{ $inventory->reorder_level - $inventory->quantity_in_stock + $inventory->reorder_level }} more units to reach optimal levels.
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stock Chart -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Stock Level Trend
                    </h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="chartPeriod" style="width: auto;">
                            <option value="7">Last 7 days</option>
                            <option value="30" selected>Last 30 days</option>
                            <option value="90">Last 90 days</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshChart()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="stockChart" height="80"></canvas>
                </div>
            </div>

            <!-- Movement History -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Movements
                    </h5>
                    <a href="{{ route('inventory.history', $inventory) }}" class="btn btn-sm btn-outline-primary">
                        View All History
                    </a>
                </div>
                <div class="card-body">
                    <div id="movementHistory">
                        <!-- Movement history will be loaded here -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading movement history...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Performance Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4" id="performanceMetrics">
                        <!-- Metrics will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="showStockAdjustModal()">
                            <i class="bi bi-arrow-up-circle me-1"></i>Add Stock
                        </button>
                        <button class="btn btn-danger" onclick="showRemoveStockModal()">
                            <i class="bi bi-arrow-down-circle me-1"></i>Remove Stock
                        </button>
                        <button class="btn btn-warning" onclick="createReorderAlert()">
                            <i class="bi bi-bell me-1"></i>Set Reorder Alert
                        </button>
                        <button class="btn btn-info" onclick="createPurchaseOrder()">
                            <i class="bi bi-bag-plus me-1"></i>Purchase Order
                        </button>
                        <a href="{{ route('products.show', $inventory->product) }}" class="btn btn-outline-primary">
                            <i class="bi bi-box me-1"></i>View Product Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Supplier Information -->
            @if($inventory->supplier)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-building me-1"></i>Supplier Information
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">{{ $inventory->supplier->name }}</h6>
                    <p class="text-muted small mb-2">{{ $inventory->supplier->email }}</p>
                    <p class="text-muted small mb-3">{{ $inventory->supplier->phone }}</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('suppliers.show', $inventory->supplier) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                        <button class="btn btn-sm btn-outline-success" onclick="contactSupplier()">
                            Contact
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Related Products -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-tags me-1"></i>Related Products
                    </h6>
                </div>
                <div class="card-body">
                    <div id="relatedProducts">
                        <!-- Related products will be loaded here -->
                        <p class="text-muted small">Loading related products...</p>
                    </div>
                </div>
            </div>

            <!-- Inventory Alerts -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>Active Alerts
                    </h6>
                </div>
                <div class="card-body">
                    <div id="activeAlerts">
                        @if($inventory->quantity_in_stock <= $inventory->reorder_level)
                            <div class="alert alert-{{ $stockStatus }} alert-sm">
                                <i class="bi bi-{{ $inventory->quantity_in_stock == 0 ? 'x-circle' : 'exclamation-triangle' }} me-2"></i>
                                <strong>{{ $statusText }}</strong>
                                <p class="mb-0 small">Last updated {{ $inventory->updated_at->diffForHumans() }}</p>
                            </div>
                        @else
                            <p class="text-muted small mb-0">No active alerts</p>
                        @endif
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
                <h5 class="modal-title">Adjust Stock Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockAdjustForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" class="form-control" value="{{ $inventory->quantity_in_stock }} units" readonly>
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
                        <input type="hidden" name="inventory_id" value="{{ $inventory->id }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <select class="form-select" name="reason" required>
                            <option value="manual_adjustment">Manual Adjustment</option>
                            <option value="supplier_delivery">Supplier Delivery</option>
                            <option value="customer_return">Customer Return</option>
                            <option value="damaged_goods">Damaged Goods</option>
                            <option value="theft_loss">Theft/Loss</option>
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
                    <button type="submit" class="btn btn-primary">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let stockChart;

$(document).ready(function() {
    loadMovementHistory();
    loadPerformanceMetrics();
    loadRelatedProducts();
    initializeChart();

    // Chart period change
    $('#chartPeriod').on('change', function() {
        refreshChart();
    });

    // Form submission
    $('#stockAdjustForm').on('submit', function(e) {
        e.preventDefault();
        adjustStock();
    });
});

function initializeChart() {
    const ctx = document.getElementById('stockChart').getContext('2d');
    stockChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Stock Level',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Reorder Level',
                data: [],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderDash: [5, 5],
                tension: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity'
                    }
                }
            }
        }
    });

    loadChartData();
}

function loadChartData() {
    const period = $('#chartPeriod').val();
    $.get(`{{ route('inventory.chart-data', $inventory) }}?period=${period}`)
        .done(function(response) {
            if (response.success) {
                const data = response.data;
                stockChart.data.labels = data.labels;
                stockChart.data.datasets[0].data = data.stock_levels;
                stockChart.data.datasets[1].data = data.reorder_levels;
                stockChart.update();
            }
        })
        .fail(function() {
            console.error('Failed to load chart data');
        });
}

function refreshChart() {
    loadChartData();
}

function loadMovementHistory() {
    $.get('{{ route("inventory.history-data", $inventory) }}?limit=10')
        .done(function(response) {
            if (response.success) {
                renderMovementHistory(response.data);
            }
        })
        .fail(function() {
            $('#movementHistory').html('<p class="text-muted">Error loading movement history</p>');
        });
}

function renderMovementHistory(movements) {
    let html = '';

    if (movements.length === 0) {
        html = '<p class="text-muted mb-0">No movement history available</p>';
    } else {
        html = '<div class="timeline">';
        movements.forEach(function(movement) {
            const iconClass = movement.type === 'in' ? 'bi-arrow-up-circle text-success' : 'bi-arrow-down-circle text-danger';
            const quantityClass = movement.type === 'in' ? 'text-success' : 'text-danger';
            const sign = movement.type === 'in' ? '+' : '-';

            html += `
                <div class="timeline-item">
                    <div class="timeline-marker">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${movement.reason}</h6>
                                <p class="mb-1 small text-muted">${movement.notes || 'No additional notes'}</p>
                                <small class="text-muted">${new Date(movement.created_at).toLocaleString()}</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold ${quantityClass}">${sign}${movement.quantity}</span>
                                <small class="d-block text-muted">Balance: ${movement.balance_after}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
    }

    $('#movementHistory').html(html);
}

function loadPerformanceMetrics() {
    $.get('{{ route("inventory.details", $inventory) }}')
        .done(function(response) {
            if (response.success) {
                renderPerformanceMetrics(response.data.metrics);
            }
        })
        .fail(function() {
            $('#performanceMetrics').html('<p class="text-muted">Error loading performance metrics</p>');
        });
}

function renderPerformanceMetrics(metrics) {
    const html = `
        <div class="col-md-6">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Avg. Monthly Usage:</span>
                <strong>${metrics.avg_monthly_usage || 0} units</strong>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Stock Turnover:</span>
                <strong>${metrics.turnover_rate || 0}x/year</strong>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Days of Stock:</span>
                <strong class="${metrics.days_of_stock <= 7 ? 'text-danger' : (metrics.days_of_stock <= 30 ? 'text-warning' : 'text-success')}">${metrics.days_of_stock || 0} days</strong>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Restock Frequency:</span>
                <strong>${metrics.restock_frequency || 0}/month</strong>
            </div>
        </div>
        <div class="col-12">
            <hr>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Total Movements (30d):</span>
                <strong>${metrics.total_movements || 0}</strong>
            </div>
        </div>
    `;

    $('#performanceMetrics').html(html);
}

function loadRelatedProducts() {
    $.get(`{{ route('inventory.data') }}?category_id={{ $inventory->product->category_id ?? '' }}&limit=5&exclude={{ $inventory->id }}`)
        .done(function(response) {
            if (response.success) {
                renderRelatedProducts(response.data.data || response.data);
            }
        })
        .fail(function() {
            $('#relatedProducts').html('<p class="text-muted small">Error loading related products</p>');
        });
}

function renderRelatedProducts(products) {
    let html = '';

    if (products.length === 0) {
        html = '<p class="text-muted small mb-0">No related products found</p>';
    } else {
        products.forEach(function(item) {
            const stockStatus = item.quantity_in_stock <= item.reorder_level ? 'warning' : 'success';
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                    <div>
                        <small class="fw-bold">${item.product ? item.product.name : 'Unknown'}</small>
                        <small class="d-block text-muted">${item.product ? item.product.sku : 'N/A'}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-${stockStatus}">${item.quantity_in_stock}</span>
                        <a href="{{ route('inventory.show', '') }}/${item.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
            `;
        });
    }

    $('#relatedProducts').html(html);
}

function showStockAdjustModal() {
    $('#stockAdjustModal').modal('show');
}

function showRemoveStockModal() {
    $('select[name="adjustment_type"]').val('remove');
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

            // Reload the page to reflect changes
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        } else {
            showAlert(response.message, 'danger');
        }
    })
    .fail(function() {
        showAlert('Error adjusting stock', 'danger');
    });
}

function createReorderAlert() {
    $.post('{{ route("inventory.reorder", $inventory) }}', {
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

function createPurchaseOrder() {
    window.location.href = '{{ route("purchases.create") }}?inventory_id={{ $inventory->id }}';
}

function exportHistory() {
    window.location.href = '{{ route("inventory.export-history", $inventory) }}';
}

function printLabel() {
    showAlert('Print label feature coming soon', 'info');
}

function contactSupplier() {
    showAlert('Contact supplier feature coming soon', 'info');
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

<style>
.timeline {
    position: relative;
    max-height: 400px;
    overflow-y: auto;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 14px;
    top: 30px;
    width: 2px;
    height: calc(100% + 10px);
    background-color: #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.5rem;
}

.alert-sm p {
    margin-bottom: 0;
}
</style>
@endpush