@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">
            <i class="bi bi-speedometer2 me-1"></i>Dashboard
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Welcome back!</h2>
                    <p class="text-muted mb-0">Here's what's happening with your business today.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-pdf me-2"></i>Export PDF</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-excel me-2"></i>Export Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1">Total Revenue</h6>
                            <h3 class="text-white mb-1" id="totalRevenue">₹{{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</h3>
                            <small class="text-white-50">This month</small>
                        </div>
                        <div class="align-self-start">
                            <i class="bi bi-currency-rupee fs-2 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1">Total Orders</h6>
                            <h3 class="text-white mb-1" id="totalOrders">{{ $stats['total_orders'] ?? 0 }}</h3>
                            <small class="text-white-50">All time</small>
                        </div>
                        <div class="align-self-start">
                            <i class="bi bi-cart-check fs-2 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1">Pending Orders</h6>
                            <h3 class="text-white mb-1" id="pendingOrders">{{ $stats['pending_orders'] ?? 0 }}</h3>
                            <small class="text-white-50">Needs attention</small>
                        </div>
                        <div class="align-self-start">
                            <i class="bi bi-clock fs-2 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1">Low Stock Items</h6>
                            <h3 class="text-white mb-1" id="lowStockItems">{{ $stats['low_stock_items'] ?? 0 }}</h3>
                            <small class="text-white-50">Reorder needed</small>
                        </div>
                        <div class="align-self-start">
                            <i class="bi bi-exclamation-triangle fs-2 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2 text-primary"></i>Sales Overview
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="chartPeriod" id="chart7d" value="7d">
                        <label class="btn btn-outline-primary" for="chart7d">7D</label>

                        <input type="radio" class="btn-check" name="chartPeriod" id="chart30d" value="30d" checked>
                        <label class="btn btn-outline-primary" for="chart30d">30D</label>

                        <input type="radio" class="btn-check" name="chartPeriod" id="chart90d" value="90d">
                        <label class="btn btn-outline-primary" for="chart90d">90D</label>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Order Status Chart -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2 text-primary"></i>Order Status
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4 mb-4">
        <!-- Recent Orders -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Recent Orders
                    </h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="recentOrdersBody">
                                @forelse($recentOrders ?? [] as $order)
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-primary">#{{ str_pad($order['id'], 5, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>{{ $order['customer_name'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'completed' ? 'success' : 'primary') }}">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </td>
                                    <td>₹{{ number_format($order['total_amount'], 2) }}</td>
                                    <td>{{ $order['order_date'] }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="viewOrder({{ $order['id'] }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" onclick="processOrder({{ $order['id'] }})">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        No recent orders found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>Low Stock Alerts
                    </h5>
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-warning">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="lowStockList">
                        @forelse($lowStockAlerts ?? [] as $alert)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $alert['product_name'] }}</h6>
                                <small class="text-muted">
                                    Stock: {{ $alert['current_stock'] }} |
                                    Reorder: {{ $alert['reorder_level'] }}
                                </small>
                            </div>
                            <span class="badge bg-danger">
                                -{{ $alert['shortage'] }}
                            </span>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                            All products in stock
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-people fs-1 text-primary mb-3"></i>
                    <h4 class="mb-1">{{ $stats['total_customers'] ?? 0 }}</h4>
                    <p class="text-muted mb-0">Total Customers</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-box fs-1 text-success mb-3"></i>
                    <h4 class="mb-1">{{ $stats['total_products'] ?? 0 }}</h4>
                    <p class="text-muted mb-0">Total Products</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-receipt fs-1 text-info mb-3"></i>
                    <h4 class="mb-1">{{ $stats['pending_invoices'] ?? 0 }}</h4>
                    <p class="text-muted mb-0">Pending Invoices</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-truck fs-1 text-warning mb-3"></i>
                    <h4 class="mb-1">{{ $stats['pending_shipments'] ?? 0 }}</h4>
                    <p class="text-muted mb-0">Pending Shipments</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Chart configurations
let salesChart, orderStatusChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    bindEventListeners();
});

function initializeCharts() {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue',
                data: [],
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111, 66, 193, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    orderStatusChart = new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($charts['order_status_distribution'] ?? [])),
            datasets: [{
                data: @json(array_values($charts['order_status_distribution'] ?? [])),
                backgroundColor: [
                    '#6f42c1',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#0dcaf0'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Load initial data
    updateSalesChart('30d');
}

function bindEventListeners() {
    // Chart period change
    document.querySelectorAll('input[name="chartPeriod"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateSalesChart(this.value);
        });
    });
}

function updateSalesChart(period) {
    fetch(`/dashboard/chart-data?type=monthly_sales&period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                salesChart.data.labels = Object.keys(data.data);
                salesChart.data.datasets[0].data = Object.values(data.data);
                salesChart.update();
            }
        })
        .catch(error => {
            console.error('Error updating sales chart:', error);
            showToast('Failed to update sales chart', 'danger');
        });
}

function refreshDashboard() {
    showLoading(document.body);

    fetch('/dashboard/refresh-stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsCards(data.data);
                updateRecentOrders();
                updateLowStockAlerts();
                showToast('Dashboard refreshed successfully');
            }
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
            showToast('Failed to refresh dashboard', 'danger');
        })
        .finally(() => {
            hideLoading(document.body);
        });
}

function updateStatsCards(stats) {
    document.getElementById('totalRevenue').textContent = '₹' + stats.monthly_revenue.toLocaleString();
    document.getElementById('totalOrders').textContent = stats.total_orders.toLocaleString();
    document.getElementById('pendingOrders').textContent = stats.pending_orders.toLocaleString();
    document.getElementById('lowStockItems').textContent = stats.low_stock_items.toLocaleString();
}

function updateRecentOrders() {
    fetch('/dashboard/recent-orders')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateOrdersTable(data.data);
            }
        })
        .catch(error => console.error('Error updating recent orders:', error));
}

function updateLowStockAlerts() {
    fetch('/dashboard/low-stock-alerts')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateLowStockList(data.data);
            }
        })
        .catch(error => console.error('Error updating low stock alerts:', error));
}

function updateOrdersTable(orders) {
    const tbody = document.getElementById('recentOrdersBody');
    tbody.innerHTML = '';

    if (orders.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No recent orders found
                </td>
            </tr>
        `;
        return;
    }

    orders.forEach(order => {
        const statusBadge = getStatusBadge(order.status);
        const row = `
            <tr>
                <td><span class="fw-semibold text-primary">#${String(order.id).padStart(5, '0')}</span></td>
                <td>${order.customer_name}</td>
                <td>${statusBadge}</td>
                <td>₹${order.total_amount.toLocaleString()}</td>
                <td>${formatDate(order.order_date)}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="viewOrder(${order.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="processOrder(${order.id})">
                            <i class="bi bi-check"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function updateLowStockList(alerts) {
    const list = document.getElementById('lowStockList');
    list.innerHTML = '';

    if (alerts.length === 0) {
        list.innerHTML = `
            <div class="list-group-item text-center text-muted py-4">
                <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                All products in stock
            </div>
        `;
        return;
    }

    alerts.forEach(alert => {
        const item = `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">${alert.product_name}</h6>
                    <small class="text-muted">Stock: ${alert.current_stock} | Reorder: ${alert.reorder_level}</small>
                </div>
                <span class="badge bg-danger">-${alert.shortage}</span>
            </div>
        `;
        list.insertAdjacentHTML('beforeend', item);
    });
}

function getStatusBadge(status) {
    const badges = {
        'pending': 'bg-warning',
        'confirmed': 'bg-primary',
        'in_progress': 'bg-info',
        'completed': 'bg-success',
        'cancelled': 'bg-danger'
    };

    return `<span class="badge ${badges[status] || 'bg-secondary'}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
}

function viewOrder(orderId) {
    window.location.href = `/orders/${orderId}`;
}

function processOrder(orderId) {
    if (confirm('Are you sure you want to process this order?')) {
        fetch(`/orders/${orderId}/process`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Order processed successfully');
                refreshDashboard();
            } else {
                showToast(data.message || 'Failed to process order', 'danger');
            }
        })
        .catch(error => {
            console.error('Error processing order:', error);
            showToast('Failed to process order', 'danger');
        });
    }
}
</script>
@endpush
