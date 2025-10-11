@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">📊 Business Analytics</h1>
                        <p class="text-muted mb-0">Real-time insights and performance metrics for your business</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-calendar-alt text-primary"></i> <span id="selectedPeriod">Last 30 Days</span>
                            </button>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#" data-period="7"><i class="fas fa-clock me-2"></i>Last 7 Days</a></li>
                                <li><a class="dropdown-item active" href="#" data-period="30"><i class="fas fa-calendar me-2"></i>Last 30 Days</a></li>
                                <li><a class="dropdown-item" href="#" data-period="90"><i class="fas fa-calendar-week me-2"></i>Last 90 Days</a></li>
                                <li><a class="dropdown-item" href="#" data-period="365"><i class="fas fa-calendar-year me-2"></i>Last Year</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-primary" onclick="refreshData()" id="refreshBtn">
                            <i class="fas fa-sync-alt" id="refreshIcon"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card bg-gradient-primary">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Total Revenue</div>
                            <div class="metric-value" id="totalRevenue">
                                <div class="loading-skeleton"></div>
                            </div>
                            <div class="metric-change text-success" id="revenueChange">
                                <i class="fas fa-arrow-up"></i> +0%
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card bg-gradient-success">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Total Orders</div>
                            <div class="metric-value" id="totalOrders">
                                <div class="loading-skeleton"></div>
                            </div>
                            <div class="metric-change text-success" id="ordersChange">
                                <i class="fas fa-arrow-up"></i> +0%
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card bg-gradient-info">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Active Customers</div>
                            <div class="metric-value" id="totalCustomers">
                                <div class="loading-skeleton"></div>
                            </div>
                            <div class="metric-change text-success" id="customersChange">
                                <i class="fas fa-arrow-up"></i> +0%
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Low Stock Alert</div>
                            <div class="metric-value" id="lowStockCount">
                                <div class="loading-skeleton"></div>
                            </div>
                            <div class="metric-change text-warning" id="stockChange">
                                <i class="fas fa-exclamation-triangle"></i> Needs attention
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Sales Trend Chart -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="analytics-card">
                <div class="analytics-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Sales Performance</h5>
                            <p class="text-muted small mb-0">Revenue and order trends over time</p>
                        </div>
                        <div class="chart-controls">
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="chartView" id="revenue" checked>
                                <label class="btn btn-outline-primary" for="revenue">Revenue</label>
                                <input type="radio" class="btn-check" name="chartView" id="orders">
                                <label class="btn btn-outline-primary" for="orders">Orders</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="analytics-card-body">
                    <div id="salesChartContainer" style="position: relative; height: 300px;">
                        <canvas id="salesChart"></canvas>
                        <div class="chart-loading" id="salesChartLoading">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <div class="spinner-border text-primary mb-2"></div>
                                <small class="text-muted">Loading chart data...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="analytics-card">
                <div class="analytics-card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Order Status</h5>
                    <p class="text-muted small mb-0">Distribution by order status</p>
                </div>
                <div class="analytics-card-body text-center">
                    <div id="orderChartContainer" style="position: relative; height: 250px;">
                        <canvas id="orderStatusChart"></canvas>
                        <div class="chart-loading" id="orderChartLoading">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <div class="spinner-border text-primary mb-2"></div>
                                <small class="text-muted">Loading chart data...</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3" id="orderLegend"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Row -->
    <div class="row">
        <!-- Top Products Table -->
        <div class="col-lg-8 mb-4">
            <div class="analytics-card">
                <div class="analytics-card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top Performing Products</h5>
                    <p class="text-muted small mb-0">Best selling products by revenue and quantity</p>
                </div>
                <div class="analytics-card-body">
                    <div id="topProductsContainer">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Product</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
                                        <th>Growth</th>
                                    </tr>
                                </thead>
                                <tbody id="topProductsList">
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="spinner-border text-primary"></div>
                                            <div class="mt-2 text-muted">Loading top products...</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Insights -->
        <div class="col-lg-4 mb-4">
            <div class="analytics-card">
                <div class="analytics-card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-user-friends me-2 text-info"></i>Customer Insights</h5>
                    <p class="text-muted small mb-0">Customer segmentation analysis</p>
                </div>
                <div class="analytics-card-body">
                    <div id="customerSegmentContainer" style="height: 300px;">
                        <canvas id="customerSegmentChart"></canvas>
                        <div class="chart-loading" id="customerChartLoading">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <div class="spinner-border text-primary mb-2"></div>
                                <small class="text-muted">Loading customer data...</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3" id="customerStats">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-value" id="newCustomers">-</div>
                                    <div class="stat-label">New Customers</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-value" id="returningCustomers">-</div>
                                    <div class="stat-label">Returning</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Modern Analytics Dashboard Styles */
.analytics-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
    height: 100%;
}

.analytics-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.analytics-card-header {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #f0f2f5;
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border-radius: 12px 12px 0 0;
}

.analytics-card-body {
    padding: 1.5rem;
}

/* Metric Cards */
.metric-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    border: none;
    color: white;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.metric-card.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
}

.metric-card.bg-gradient-success {
    background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
}

.metric-card.bg-gradient-info {
    background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
    box-shadow: 0 4px 15px rgba(54, 185, 204, 0.3);
}

.metric-card.bg-gradient-warning {
    background: linear-gradient(135deg, #f6c23e 0%, #d4a027 100%);
    box-shadow: 0 4px 15px rgba(246, 194, 62, 0.3);
}

.metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
}

.metric-card-body {
    padding: 1.5rem;
    position: relative;
    z-index: 2;
}

.metric-label {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.metric-change {
    font-size: 0.8rem;
    font-weight: 500;
    opacity: 0.9;
}

.metric-icon {
    font-size: 2.5rem;
    opacity: 0.3;
}

/* Loading States */
.loading-skeleton {
    height: 2rem;
    background: linear-gradient(90deg, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.4) 50%, rgba(255,255,255,0.2) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 4px;
    width: 120px;
}

.chart-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Chart Controls */
.chart-controls .btn-group-sm .btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
    border-radius: 6px;
}

.chart-controls .btn-check:checked + .btn-outline-primary {
    background-color: #4e73df;
    border-color: #4e73df;
    color: white;
}

/* Statistics */
.stat-item {
    padding: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: #718096;
    font-weight: 500;
}

/* Table Enhancements */
.table-hover tbody tr:hover {
    background-color: #f8f9fc;
}

.table th {
    font-weight: 600;
    color: #2d3748;
    border-bottom: 2px solid #e3e6f0;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .metric-card-body {
        padding: 1rem;
    }

    .metric-value {
        font-size: 1.5rem;
    }

    .analytics-card-header,
    .analytics-card-body {
        padding: 1rem;
    }

    .chart-controls {
        margin-top: 1rem;
    }
}

/* Custom Scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for refresh button */
.refresh-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let salesChart, orderStatusChart, customerSegmentChart;
let currentPeriod = 30;

$(document).ready(function() {
    initializeEventHandlers();
    loadDashboardData();
    initializeCharts();
});

function initializeEventHandlers() {
    // Period selector
    $('.dropdown-item[data-period]').on('click', function(e) {
        e.preventDefault();
        const period = $(this).data('period');
        const text = $(this).text().trim();

        currentPeriod = period;
        $('#selectedPeriod').text(text);

        // Update active state
        $('.dropdown-item').removeClass('active');
        $(this).addClass('active');

        // Reload data
        refreshData();
    });

    // Chart view toggles
    $('input[name="chartView"]').on('change', function() {
        updateSalesChart();
    });
}

function loadDashboardData() {
    showMetricLoading();

    $.ajax({
        url: '/analytics/dashboard-data',
        method: 'GET',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success) {
                updateMetrics(response.data);
            } else {
                showMetricError();
            }
        },
        error: function(xhr) {
            console.error('Dashboard data error:', xhr);
            showMetricError();
        }
    });
}

function updateMetrics(data) {
    // Animate number counting
    animateNumber('#totalRevenue', data.month_sales, '₹');
    animateNumber('#totalOrders', data.total_orders || 0);
    animateNumber('#totalCustomers', data.total_customers);
    animateNumber('#lowStockCount', data.low_stock_count);

    // Update change indicators (mock data for now)
    updateChangeIndicator('#revenueChange', 12.5, true);
    updateChangeIndicator('#ordersChange', 8.3, true);
    updateChangeIndicator('#customersChange', 5.2, true);

    if (data.low_stock_count > 0) {
        $('#stockChange').html('<i class="fas fa-exclamation-triangle"></i> Needs attention');
    } else {
        $('#stockChange').html('<i class="fas fa-check-circle"></i> All good');
    }
}

function animateNumber(selector, targetValue, prefix = '') {
    const element = $(selector);
    const startValue = 0;
    const duration = 1000;
    const startTime = Date.now();

    function update() {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);

        element.html(prefix + formatNumber(currentValue));

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

function updateChangeIndicator(selector, percentage, isPositive) {
    const element = $(selector);
    const icon = isPositive ? 'fa-arrow-up' : 'fa-arrow-down';
    const colorClass = isPositive ? 'text-success' : 'text-danger';
    const sign = isPositive ? '+' : '';

    element.removeClass('text-success text-danger text-warning');
    element.addClass(colorClass);
    element.html(`<i class="fas ${icon}"></i> ${sign}${percentage}%`);
}

function showMetricLoading() {
    $('#totalRevenue, #totalOrders, #totalCustomers, #lowStockCount').html('<div class="loading-skeleton"></div>');
}

function showMetricError() {
    $('#totalRevenue').html('<span class="text-danger">Error</span>');
    $('#totalOrders').html('<span class="text-danger">Error</span>');
    $('#totalCustomers').html('<span class="text-danger">Error</span>');
    $('#lowStockCount').html('<span class="text-danger">Error</span>');
}

function initializeCharts() {
    initSalesChart();
    initOrderStatusChart();
    initCustomerSegmentChart();
    loadChartsData();
}

function initSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
}

function initOrderStatusChart() {
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    orderStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc',
                    '#f6c23e', '#e74a3b', '#858796'
                ],
                borderWidth: 2,
                borderColor: '#fff'
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
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function initCustomerSegmentChart() {
    const ctx = document.getElementById('customerSegmentChart').getContext('2d');
    customerSegmentChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Customers',
                data: [],
                backgroundColor: '#4e73df',
                borderRadius: 6,
                borderSkipped: false
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
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
}

function loadChartsData() {
    showChartLoading();

    Promise.all([
        loadSalesData(),
        loadOrderData(),
        loadCustomerData(),
        loadTopProducts()
    ]).then(() => {
        hideChartLoading();
    }).catch((error) => {
        console.error('Error loading chart data:', error);
        hideChartLoading();
    });
}

function loadSalesData() {
    return $.ajax({
        url: '/analytics/data/sales',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success && salesChart) {
                salesChart.data = response.data;
                salesChart.update('none');
            }
        }
    });
}

function loadOrderData() {
    return $.ajax({
        url: '/analytics/data/orders',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success && orderStatusChart) {
                orderStatusChart.data.labels = response.data.labels;
                orderStatusChart.data.datasets[0].data = response.data.counts;
                orderStatusChart.update('none');
                updateOrderLegend(response.data);
            }
        }
    });
}

function loadCustomerData() {
    return $.ajax({
        url: '/analytics/data/customers',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success && customerSegmentChart) {
                const segments = response.data.segments || [];
                customerSegmentChart.data.labels = segments.map(s => s.label);
                customerSegmentChart.data.datasets[0].data = segments.map(s => s.count);
                customerSegmentChart.update('none');

                // Update customer stats
                $('#newCustomers').text(response.data.new_customers_count || 0);
                $('#returningCustomers').text(response.data.returning_customers_count || 0);
            }
        }
    });
}

function loadTopProducts() {
    return $.ajax({
        url: '/analytics/trends/product_performance',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success) {
                updateTopProductsTable(response.data);
            }
        }
    });
}

function updateOrderLegend(data) {
    const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
    let legendHtml = '';

    data.labels.forEach((label, index) => {
        const count = data.counts[index];
        const color = colors[index % colors.length];
        legendHtml += `
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="d-flex align-items-center">
                    <div style="width: 12px; height: 12px; background: ${color}; border-radius: 50%; margin-right: 8px;"></div>
                    <small>${label}</small>
                </div>
                <small class="font-weight-bold">${count}</small>
            </div>
        `;
    });

    $('#orderLegend').html(legendHtml);
}

function updateTopProductsTable(products) {
    let tableHtml = '';

    if (products && products.length > 0) {
        products.slice(0, 5).forEach((product, index) => {
            const growth = Math.random() * 20 - 10; // Mock growth data
            const growthClass = growth >= 0 ? 'text-success' : 'text-danger';
            const growthIcon = growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';

            tableHtml += `
                <tr>
                    <td><span class="badge bg-primary">${index + 1}</span></td>
                    <td>
                        <div class="font-weight-bold">${product.product_name || 'Unknown Product'}</div>
                        <small class="text-muted">${product.product_sku || 'N/A'}</small>
                    </td>
                    <td>${formatNumber(product.total_quantity || 0)}</td>
                    <td>₹${formatNumber(product.total_revenue || 0)}</td>
                    <td>
                        <span class="${growthClass}">
                            <i class="fas ${growthIcon}"></i> ${Math.abs(growth).toFixed(1)}%
                        </span>
                    </td>
                </tr>
            `;
        });
    } else {
        tableHtml = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                    No product data available
                </td>
            </tr>
        `;
    }

    $('#topProductsList').html(tableHtml);
}

function showChartLoading() {
    $('.chart-loading').show();
}

function hideChartLoading() {
    $('.chart-loading').hide();
}

function refreshData() {
    const refreshBtn = $('#refreshBtn');
    const refreshIcon = $('#refreshIcon');

    // Show loading state
    refreshIcon.addClass('refresh-spin');
    refreshBtn.prop('disabled', true);

    loadDashboardData();
    loadChartsData();

    // Hide loading state after animation
    setTimeout(() => {
        refreshIcon.removeClass('refresh-spin');
        refreshBtn.prop('disabled', false);
    }, 1000);
}

function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    return new Intl.NumberFormat('en-IN').format(num);
}

function exportChart(type) {
    // Future implementation for chart export
    alert('Export functionality will be implemented in a future update');
}
</script>
@endpush