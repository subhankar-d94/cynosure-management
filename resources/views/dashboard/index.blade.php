@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid dashboard-container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="welcome-content">
                        <h1 class="welcome-title mb-2">
                            <i class="fas fa-chart-line me-2"></i>Dashboard Overview
                        </h1>
                        <p class="welcome-subtitle mb-0">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ now()->format('l, F j, Y') }} | Here's what's happening today
                        </p>
                    </div>
                    <div class="d-flex gap-2 mt-3 mt-md-0">
                        <a href="{{ route('analytics.profit-loss') }}" class="btn btn-gradient-success">
                            <i class="fas fa-chart-bar me-2"></i>Profit & Loss
                        </a>
                        <a href="{{ route('analytics.index') }}" class="btn btn-gradient-primary">
                            <i class="fas fa-analytics me-2"></i>Analytics
                        </a>
                        <button class="btn btn-outline-light" onclick="refreshDashboard()" id="refreshBtn">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary KPI Cards -->
    <div class="row g-4 mb-4">
        <!-- Monthly Revenue -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card revenue-card">
                <div class="kpi-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon-wrapper">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="kpi-trend {{ $stats['revenue_growth'] >= 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-arrow-{{ $stats['revenue_growth'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ number_format(abs($stats['revenue_growth']), 1) }}%
                        </div>
                    </div>
                    <h6 class="kpi-label">Monthly Revenue</h6>
                    <h2 class="kpi-value" id="monthlyRevenue">₹{{ number_format($stats['monthly_revenue'], 2) }}</h2>
                    <p class="kpi-meta">
                        <span class="badge badge-light">{{ $stats['monthly_orders'] }} orders</span>
                    </p>
                </div>
                <div class="kpi-card-footer">
                    <small><i class="far fa-clock me-1"></i>This month</small>
                </div>
            </div>
        </div>

        <!-- Monthly Profit -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card profit-card">
                <div class="kpi-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon-wrapper">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="kpi-trend positive">
                            <i class="fas fa-percentage"></i>
                            {{ number_format($stats['profit_margin'], 1) }}%
                        </div>
                    </div>
                    <h6 class="kpi-label">Gross Profit</h6>
                    <h2 class="kpi-value" id="monthlyProfit">₹{{ number_format($stats['monthly_profit'], 2) }}</h2>
                    <p class="kpi-meta">
                        <span class="badge badge-light">Margin: {{ number_format($stats['profit_margin'], 1) }}%</span>
                    </p>
                </div>
                <div class="kpi-card-footer">
                    <small><i class="fas fa-info-circle me-1"></i>After COGS</small>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card today-card">
                <div class="kpi-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon-wrapper">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="kpi-badge">
                            <i class="fas fa-star"></i> Today
                        </div>
                    </div>
                    <h6 class="kpi-label">Today's Revenue</h6>
                    <h2 class="kpi-value" id="todayRevenue">₹{{ number_format($stats['today_revenue'], 2) }}</h2>
                    <p class="kpi-meta">
                        <span class="badge badge-light">{{ $stats['today_orders'] }} orders</span>
                    </p>
                </div>
                <div class="kpi-card-footer">
                    <small><i class="far fa-clock me-1"></i>Live updates</small>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card alert-card">
                <div class="kpi-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon-wrapper">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="kpi-badge warning">
                            <i class="fas fa-exclamation"></i>
                        </div>
                    </div>
                    <h6 class="kpi-label">Pending Orders</h6>
                    <h2 class="kpi-value" id="pendingOrders">{{ $stats['pending_orders'] }}</h2>
                    <p class="kpi-meta">
                        <span class="badge badge-light">{{ $stats['total_orders'] }} total</span>
                    </p>
                </div>
                <div class="kpi-card-footer">
                    <small><i class="fas fa-bell me-1"></i>Needs attention</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Sales Overview Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="chart-title mb-1">
                                <i class="fas fa-chart-area me-2"></i>Sales Overview
                            </h5>
                            <p class="chart-subtitle mb-0">Revenue trend over the last 12 months</p>
                        </div>
                        <div class="btn-group btn-group-sm chart-filter" role="group">
                            <input type="radio" class="btn-check" name="chartPeriod" id="chart7d" value="7">
                            <label class="btn btn-outline-primary" for="chart7d">7D</label>
                            <input type="radio" class="btn-check" name="chartPeriod" id="chart30d" value="30" checked>
                            <label class="btn btn-outline-primary" for="chart30d">30D</label>
                            <input type="radio" class="btn-check" name="chartPeriod" id="chart90d" value="90">
                            <label class="btn btn-outline-primary" for="chart90d">90D</label>
                            <input type="radio" class="btn-check" name="chartPeriod" id="chart365d" value="365">
                            <label class="btn btn-outline-primary" for="chart365d">1Y</label>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5 class="chart-title mb-1">
                        <i class="fas fa-pie-chart me-2"></i>Order Status
                    </h5>
                    <p class="chart-subtitle mb-0">Current order distribution</p>
                </div>
                <div class="chart-card-body text-center">
                    <canvas id="orderStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon customers">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ number_format($stats['total_customers']) }}</h3>
                    <p class="stat-label">Total Customers</p>
                    <a href="{{ route('customers.index') }}" class="stat-link">View all <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon products">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ number_format($stats['total_products']) }}</h3>
                    <p class="stat-label">Total Products</p>
                    <a href="{{ route('products.index') }}" class="stat-link">View all <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon inventory">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ number_format($stats['low_stock_items']) }}</h3>
                    <p class="stat-label">Low Stock Items</p>
                    <a href="{{ route('products.index', ['stock_status' => 'low_stock']) }}" class="stat-link">Reorder <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon suppliers">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ number_format($stats['total_suppliers']) }}</h3>
                    <p class="stat-label">Active Suppliers</p>
                    <a href="{{ route('suppliers.index') }}" class="stat-link">View all <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-xl-8">
            <div class="data-card">
                <div class="data-card-header">
                    <h5 class="data-title">
                        <i class="fas fa-shopping-cart me-2"></i>Recent Orders
                    </h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="data-card-body">
                    <div class="table-responsive">
                        <table class="table data-table">
                            <thead>
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
                                <tr class="fade-in">
                                    <td>
                                        <span class="order-id">#{{ $order['order_number'] }}</span>
                                    </td>
                                    <td>{{ $order['customer_name'] }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $order['status'] }}">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">₹{{ number_format($order['total_amount'], 2) }}</td>
                                    <td>{{ $order['order_date'] }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action view" onclick="viewOrder({{ $order['id'] }})" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                        <p class="mb-0">No recent orders found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts & Quick Actions -->
        <div class="col-xl-4">
            <!-- Low Stock Alerts -->
            <div class="data-card mb-4">
                <div class="data-card-header">
                    <h5 class="data-title">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Stock Alerts
                    </h5>
                    <a href="{{ route('products.index', ['stock_status' => 'low_stock']) }}" class="btn btn-sm btn-outline-warning">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="data-card-body p-0">
                    <div class="alert-list" id="lowStockList">
                        @forelse($lowStockAlerts ?? [] as $alert)
                        <div class="alert-item">
                            <div class="alert-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="alert-content">
                                <h6 class="alert-product">{{ $alert['product_name'] }}</h6>
                                <p class="alert-meta mb-0">
                                    <span class="text-danger fw-bold">{{ $alert['current_stock'] }}</span> in stock
                                    <span class="text-muted">| Reorder: {{ $alert['reorder_level'] }}</span>
                                </p>
                            </div>
                            <div class="alert-badge">
                                <span class="badge bg-danger">-{{ $alert['shortage'] }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success d-block"></i>
                            <p class="mb-0">All products in stock</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions-card">
                <div class="quick-actions-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="quick-actions-body">
                    <a href="{{ route('orders.create') }}" class="quick-action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>New Order</span>
                    </a>
                    <a href="{{ route('products.create') }}" class="quick-action-btn">
                        <i class="fas fa-box"></i>
                        <span>Add Product</span>
                    </a>
                    <a href="{{ route('customers.create') }}" class="quick-action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>New Customer</span>
                    </a>
                    <a href="{{ route('purchases.create') }}" class="quick-action-btn">
                        <i class="fas fa-shopping-basket"></i>
                        <span>Purchase Order</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let salesChart, orderStatusChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    bindEventListeners();
    startAutoRefresh();
});

function initializeCharts() {
    // Sales Chart - Area Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 400);
    salesGradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
    salesGradient.addColorStop(1, 'rgba(99, 102, 241, 0.05)');

    salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json(array_keys($charts['monthly_sales'] ?? [])),
            datasets: [{
                label: 'Revenue (₹)',
                data: @json(array_values($charts['monthly_sales'] ?? [])),
                borderColor: '#6366f1',
                backgroundColor: salesGradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString('en-IN');
                        },
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Order Status Chart - Doughnut
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    orderStatusChart = new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_map('ucfirst', array_keys($charts['order_status_distribution'] ?? []))),
            datasets: [{
                data: @json(array_values($charts['order_status_distribution'] ?? [])),
                backgroundColor: [
                    '#6366f1', // pending
                    '#10b981', // completed
                    '#f59e0b', // confirmed
                    '#ef4444', // cancelled
                    '#06b6d4'  // in_progress
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

function bindEventListeners() {
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
                salesChart.update('active');
            }
        })
        .catch(error => console.error('Error updating sales chart:', error));
}

function refreshDashboard() {
    const btn = document.getElementById('refreshBtn');
    const icon = btn.querySelector('i');

    icon.classList.add('fa-spin');
    btn.disabled = true;

    fetch('/dashboard/refresh-stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsCards(data.data);
                showToast('Dashboard refreshed successfully', 'success');
            }
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
            showToast('Failed to refresh dashboard', 'error');
        })
        .finally(() => {
            icon.classList.remove('fa-spin');
            btn.disabled = false;
        });
}

function updateStatsCards(stats) {
    document.getElementById('monthlyRevenue').textContent = '₹' + stats.monthly_revenue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('monthlyProfit').textContent = '₹' + stats.monthly_profit.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('todayRevenue').textContent = '₹' + stats.today_revenue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('pendingOrders').textContent = stats.pending_orders.toLocaleString();
}

function viewOrder(orderId) {
    window.location.href = `/orders/${orderId}`;
}

function showToast(message, type = 'success') {
    // Simple toast notification (you can replace with a toast library)
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function startAutoRefresh() {
    // Auto-refresh stats every 5 minutes
    setInterval(() => {
        fetch('/dashboard/refresh-stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatsCards(data.data);
                }
            });
    }, 300000); // 5 minutes
}
</script>
@endpush

@push('styles')
<style>
/* Dashboard Container */
.dashboard-container {
    padding: 1.5rem;
    background: #f8f9fa;
}

/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    color: #fff;
}

.welcome-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #fff;
}

.welcome-subtitle {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.9);
}

.btn-gradient-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-gradient-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    color: #fff;
}

.btn-gradient-primary {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-gradient-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    color: #fff;
}

.btn-outline-light {
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: #fff;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    color: #fff;
}

/* KPI Cards */
.kpi-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.kpi-card-body {
    padding: 1.5rem;
}

.kpi-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #fff;
}

.revenue-card .kpi-icon-wrapper {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
}

.profit-card .kpi-icon-wrapper {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.today-card .kpi-icon-wrapper {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.alert-card .kpi-icon-wrapper {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.kpi-trend {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.kpi-trend.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.kpi-trend.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.kpi-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.kpi-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.kpi-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.5rem;
}

.kpi-meta {
    margin-bottom: 0;
}

.badge-light {
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.35rem 0.75rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.75rem;
}

.kpi-card-footer {
    background: #f9fafb;
    padding: 0.75rem 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.kpi-card-footer small {
    color: #6b7280;
    font-size: 0.8rem;
}

/* Chart Cards */
.chart-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    height: 100%;
}

.chart-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.chart-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0;
}

.chart-subtitle {
    font-size: 0.85rem;
    color: #6b7280;
}

.chart-filter .btn {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    font-weight: 600;
}

.chart-card-body {
    padding: 1.5rem;
}

/* Stat Cards */
.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 1.25rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: #fff;
}

.stat-icon.customers {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
}

.stat-icon.products {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-icon.inventory {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-icon.suppliers {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.stat-link {
    font-size: 0.85rem;
    color: #6366f1;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
}

.stat-link:hover {
    color: #4f46e5;
    transform: translateX(3px);
    display: inline-block;
}

/* Data Cards */
.data-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.data-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: between;
    align-items: center;
}

.data-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0;
}

.data-card-body {
    padding: 1.5rem;
}

/* Data Table */
.data-table {
    margin-bottom: 0;
}

.data-table thead th {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
    padding: 1rem;
}

.data-table tbody td {
    padding: 1rem;
    vertical-align: middle;
    font-size: 0.9rem;
    border-bottom: 1px solid #f3f4f6;
}

.order-id {
    font-weight: 700;
    color: #6366f1;
}

.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-pending {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.status-completed {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-confirmed {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.status-in_progress {
    background: rgba(6, 182, 212, 0.1);
    color: #06b6d4;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action.view {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.btn-action.view:hover {
    background: #6366f1;
    color: #fff;
}

/* Alert List */
.alert-list {
    max-height: 400px;
    overflow-y: auto;
}

.alert-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
}

.alert-item:last-child {
    border-bottom: none;
}

.alert-item:hover {
    background: #f9fafb;
}

.alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.alert-content {
    flex: 1;
}

.alert-product {
    font-size: 0.95rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.alert-meta {
    font-size: 0.8rem;
    color: #6b7280;
}

/* Quick Actions */
.quick-actions-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.quick-actions-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
}

.quick-actions-body {
    padding: 1.5rem;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.quick-action-btn {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 1.25rem 1rem;
    text-align: center;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.quick-action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-3px);
    color: #fff;
}

.quick-action-btn i {
    font-size: 1.5rem;
}

.quick-action-btn span {
    font-size: 0.85rem;
    font-weight: 600;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }

    .welcome-card {
        padding: 1.5rem;
    }

    .welcome-title {
        font-size: 1.4rem;
    }

    .kpi-value {
        font-size: 1.5rem;
    }

    .stat-value {
        font-size: 1.5rem;
    }

    .quick-actions-body {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
