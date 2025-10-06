@extends('layouts.app')

@section('title', 'Inventory Valuation')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
        <li class="breadcrumb-item active">Stock Valuation</li>
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
                    <h2 class="mb-1">Inventory Valuation</h2>
                    <p class="text-muted mb-0">Complete analysis of your inventory value and performance</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="exportValuation()">
                        <i class="bi bi-download me-1"></i>Export Report
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-primary text-uppercase mb-1">Total Value</h6>
                            <h3 class="mb-0 text-primary" id="totalValue">-</h3>
                            <small class="text-muted">Current market value</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-currency-rupee" style="font-size: 2.5rem;"></i>
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
                            <h6 class="text-success text-uppercase mb-1">Cost Value</h6>
                            <h3 class="mb-0 text-success" id="costValue">-</h3>
                            <small class="text-muted">Based on cost price</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-cash-stack" style="font-size: 2.5rem;"></i>
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
                            <h6 class="text-info text-uppercase mb-1">Profit Margin</h6>
                            <h3 class="mb-0 text-info" id="profitMargin">-</h3>
                            <small class="text-muted">Potential profit</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-graph-up-arrow" style="font-size: 2.5rem;"></i>
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
                            <h6 class="text-warning text-uppercase mb-1">Turnover Rate</h6>
                            <h3 class="mb-0 text-warning" id="turnoverRate">-</h3>
                            <small class="text-muted">Times per year</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-arrow-repeat" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Value Distribution by Category
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryValueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>Performance Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="performanceMetrics">
                        <!-- Metrics will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Valuation Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detailed Valuation Report</h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="groupBy" style="width: auto;">
                            <option value="">No Grouping</option>
                            <option value="category">Group by Category</option>
                            <option value="supplier">Group by Supplier</option>
                            <option value="abc_class">Group by ABC Class</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshValuation()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Cost per Unit</th>
                                    <th>Selling Price</th>
                                    <th>Total Cost Value</th>
                                    <th>Total Selling Value</th>
                                    <th>Profit Potential</th>
                                    <th>% of Total Value</th>
                                </tr>
                            </thead>
                            <tbody id="valuationTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Row -->
                    <div class="border-top pt-3 mt-3">
                        <div class="row">
                            <div class="col-md-8">
                                <strong>Total Portfolio Value:</strong>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="row">
                                    <div class="col-4"><strong id="totalCostValue">₹0</strong></div>
                                    <div class="col-4"><strong id="totalSellingValue">₹0</strong></div>
                                    <div class="col-4"><strong id="totalProfitPotential" class="text-success">₹0</strong></div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let categoryChart;

$(document).ready(function() {
    loadValuationData();
    initializeChart();

    // Group by change handler
    $('#groupBy').on('change', function() {
        loadValuationData();
    });
});

function loadValuationData() {
    const groupBy = $('#groupBy').val();

    $.get('{{ route("inventory.valuation") }}', { group_by: groupBy })
        .done(function(response) {
            if (response.success) {
                updateSummaryCards(response.data.summary);
                updatePerformanceMetrics(response.data.metrics);
                renderValuationTable(response.data.items);
                updateCategoryChart(response.data.category_distribution);
            }
        })
        .fail(function() {
            showAlert('Error loading valuation data', 'danger');
        });
}

function updateSummaryCards(summary) {
    $('#totalValue').text('₹' + (summary.total_selling_value || 0).toLocaleString());
    $('#costValue').text('₹' + (summary.total_cost_value || 0).toLocaleString());
    $('#profitMargin').text('₹' + (summary.profit_potential || 0).toLocaleString());
    $('#turnoverRate').text((summary.turnover_rate || 0).toFixed(1) + 'x');
}

function updatePerformanceMetrics(metrics) {
    const html = `
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Active Products:</span>
                <strong>${metrics.active_products || 0}</strong>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Avg. Stock Value:</span>
                <strong>₹${(metrics.avg_stock_value || 0).toLocaleString()}</strong>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Profit Margin %:</span>
                <strong class="text-success">${(metrics.profit_margin_percent || 0).toFixed(1)}%</strong>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Dead Stock Value:</span>
                <strong class="text-danger">₹${(metrics.dead_stock_value || 0).toLocaleString()}</strong>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Fast Moving Items:</span>
                <strong class="text-success">${metrics.fast_moving || 0}</strong>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Slow Moving Items:</span>
                <strong class="text-warning">${metrics.slow_moving || 0}</strong>
            </div>
        </div>
    `;

    $('#performanceMetrics').html(html);
}

function renderValuationTable(items) {
    let html = '';
    let totalCost = 0;
    let totalSelling = 0;
    let totalProfit = 0;

    if (items.length === 0) {
        html = '<tr><td colspan="9" class="text-center py-4">No valuation data available</td></tr>';
    } else {
        items.forEach(function(item) {
            const costValue = item.quantity_in_stock * item.cost_per_unit;
            const sellingValue = item.quantity_in_stock * item.selling_price;
            const profitPotential = sellingValue - costValue;
            const profitMargin = costValue > 0 ? ((profitPotential / costValue) * 100) : 0;

            totalCost += costValue;
            totalSelling += sellingValue;
            totalProfit += profitPotential;

            html += `
                <tr>
                    <td>
                        <div>
                            <h6 class="mb-0">${item.product.name}</h6>
                            <small class="text-muted">${item.product.sku}</small>
                        </div>
                    </td>
                    <td>${item.product.category ? item.product.category.name : 'Uncategorized'}</td>
                    <td>
                        <span class="fw-bold">${item.quantity_in_stock}</span>
                        <small class="d-block text-muted">units</small>
                    </td>
                    <td>₹${item.cost_per_unit.toFixed(2)}</td>
                    <td>₹${item.selling_price.toFixed(2)}</td>
                    <td>₹${costValue.toLocaleString()}</td>
                    <td>₹${sellingValue.toLocaleString()}</td>
                    <td>
                        <span class="${profitPotential >= 0 ? 'text-success' : 'text-danger'}">
                            ₹${profitPotential.toLocaleString()}
                        </span>
                        <small class="d-block ${profitMargin >= 0 ? 'text-success' : 'text-danger'}">
                            ${profitMargin.toFixed(1)}%
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">
                            ${totalSelling > 0 ? ((sellingValue / totalSelling) * 100).toFixed(1) : 0}%
                        </span>
                    </td>
                </tr>
            `;
        });
    }

    $('#valuationTableBody').html(html);

    // Update totals
    $('#totalCostValue').text('₹' + totalCost.toLocaleString());
    $('#totalSellingValue').text('₹' + totalSelling.toLocaleString());
    $('#totalProfitPotential').text('₹' + totalProfit.toLocaleString());
}

function initializeChart() {
    const ctx = document.getElementById('categoryValueChart').getContext('2d');
    categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#C9CBCF',
                    '#4BC0C0'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function updateCategoryChart(distribution) {
    if (distribution && distribution.length > 0) {
        categoryChart.data.labels = distribution.map(item => item.category);
        categoryChart.data.datasets[0].data = distribution.map(item => item.value);
        categoryChart.update();
    }
}

function refreshValuation() {
    loadValuationData();
}

function exportValuation() {
    const groupBy = $('#groupBy').val();
    const params = new URLSearchParams({ group_by: groupBy, export: 'valuation' });
    window.location.href = `{{ route('inventory.export') }}?${params}`;
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