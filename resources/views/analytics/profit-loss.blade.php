@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Profit & Loss Analytics</h1>
            <p class="text-muted">Revenue, Cost, and Profit Analysis</p>
        </div>
        <div>
            <a href="{{ route('analytics.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Analytics
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                           value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                           value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-primary me-2" onclick="loadData()">
                        <i class="fas fa-search"></i> Apply Filter
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilter()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4" id="summaryCards">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Revenue</h6>
                            <h3 class="mb-0" id="totalRevenue">₹0.00</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-rupee-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Cost of Goods Sold</h6>
                            <h3 class="mb-0" id="totalCogs">₹0.00</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Gross Profit</h6>
                            <h3 class="mb-0" id="grossProfit">₹0.00</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Profit Margin</h6>
                            <h3 class="mb-0" id="profitMargin">0%</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Costs Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Purchase Orders Cost</h6>
                            <h4 class="mb-0" id="purchaseCosts">₹0.00</h4>
                            <small class="text-muted">Total amount spent on purchase orders during this period</small>
                        </div>
                        <div>
                            <i class="fas fa-file-invoice-dollar fa-3x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue vs Cost Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Revenue vs Cost Trend</h5>
        </div>
        <div class="card-body">
            <canvas id="revenueVsCostChart" height="80"></canvas>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="row">
        <!-- Top Products by Profit -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top 10 Products by Profit</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Units Sold</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">COGS</th>
                                    <th class="text-end">Profit</th>
                                </tr>
                            </thead>
                            <tbody id="topProductsTable">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit by Category -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profit by Category</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">COGS</th>
                                    <th class="text-end">Profit</th>
                                    <th class="text-end">Margin %</th>
                                </tr>
                            </thead>
                            <tbody id="categoryProfitTable">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Profit Trend -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Daily Profit Trend</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">COGS</th>
                            <th class="text-end">Profit</th>
                            <th class="text-end">Margin %</th>
                        </tr>
                    </thead>
                    <tbody id="dailyProfitTable">
                        <tr>
                            <td colspan="5" class="text-center text-muted">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let revenueVsCostChart;

    // Load data on page load
    loadData();

    function loadData() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        // Load summary data
        $.ajax({
            url: '/analytics/profit-loss-data',
            method: 'GET',
            data: { start_date: startDate, end_date: endDate },
            success: function(response) {
                if (response.success) {
                    updateSummaryCards(response.data.summary);
                    updateTopProducts(response.data.top_products);
                    updateCategoryProfit(response.data.category_profit);
                    updateDailyProfit(response.data.daily_profit);
                }
            },
            error: function(error) {
                console.error('Error loading profit/loss data:', error);
                alert('Failed to load profit/loss data');
            }
        });

        // Load chart data
        $.ajax({
            url: '/analytics/revenue-vs-cost',
            method: 'GET',
            data: { period: 30 },
            success: function(response) {
                if (response.success) {
                    updateChart(response.data);
                }
            },
            error: function(error) {
                console.error('Error loading chart data:', error);
            }
        });
    }

    function updateSummaryCards(summary) {
        $('#totalRevenue').text('₹' + formatNumber(summary.revenue));
        $('#totalCogs').text('₹' + formatNumber(summary.cogs));
        $('#grossProfit').text('₹' + formatNumber(summary.gross_profit));
        $('#profitMargin').text(summary.gross_profit_margin.toFixed(2) + '%');
        $('#purchaseCosts').text('₹' + formatNumber(summary.purchase_costs));
    }

    function updateTopProducts(products) {
        const tbody = $('#topProductsTable');
        tbody.empty();

        if (products.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center text-muted">No data available</td></tr>');
            return;
        }

        products.forEach(product => {
            const profitClass = product.profit >= 0 ? 'text-success' : 'text-danger';
            tbody.append(`
                <tr>
                    <td>
                        <strong>${product.product_name}</strong><br>
                        <small class="text-muted">${product.sku}</small>
                    </td>
                    <td class="text-end">${formatNumber(product.units_sold)}</td>
                    <td class="text-end">₹${formatNumber(product.revenue)}</td>
                    <td class="text-end">₹${formatNumber(product.cogs)}</td>
                    <td class="text-end ${profitClass}"><strong>₹${formatNumber(product.profit)}</strong></td>
                </tr>
            `);
        });
    }

    function updateCategoryProfit(categories) {
        const tbody = $('#categoryProfitTable');
        tbody.empty();

        if (categories.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center text-muted">No data available</td></tr>');
            return;
        }

        categories.forEach(category => {
            const margin = category.revenue > 0 ? ((category.profit / category.revenue) * 100).toFixed(2) : 0;
            const profitClass = category.profit >= 0 ? 'text-success' : 'text-danger';
            tbody.append(`
                <tr>
                    <td><strong>${category.category_name}</strong></td>
                    <td class="text-end">₹${formatNumber(category.revenue)}</td>
                    <td class="text-end">₹${formatNumber(category.cogs)}</td>
                    <td class="text-end ${profitClass}"><strong>₹${formatNumber(category.profit)}</strong></td>
                    <td class="text-end">${margin}%</td>
                </tr>
            `);
        });
    }

    function updateDailyProfit(dailyData) {
        const tbody = $('#dailyProfitTable');
        tbody.empty();

        if (dailyData.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center text-muted">No data available</td></tr>');
            return;
        }

        dailyData.forEach(day => {
            const margin = day.revenue > 0 ? ((day.profit / day.revenue) * 100).toFixed(2) : 0;
            const profitClass = day.profit >= 0 ? 'text-success' : 'text-danger';
            tbody.append(`
                <tr>
                    <td>${formatDate(day.date)}</td>
                    <td class="text-end">₹${formatNumber(day.revenue)}</td>
                    <td class="text-end">₹${formatNumber(day.cogs)}</td>
                    <td class="text-end ${profitClass}"><strong>₹${formatNumber(day.profit)}</strong></td>
                    <td class="text-end">${margin}%</td>
                </tr>
            `);
        });
    }

    function updateChart(chartData) {
        const ctx = document.getElementById('revenueVsCostChart').getContext('2d');

        if (revenueVsCostChart) {
            revenueVsCostChart.destroy();
        }

        revenueVsCostChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '₹' + formatNumber(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }

    function formatNumber(num) {
        return parseFloat(num).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    window.loadData = loadData;

    window.resetFilter = function() {
        const today = new Date().toISOString().split('T')[0];
        const lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        const lastMonthStr = lastMonth.toISOString().split('T')[0];

        $('#start_date').val(lastMonthStr);
        $('#end_date').val(today);
        loadData();
    };
});
</script>
@endpush

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
}

.table td {
    vertical-align: middle;
    font-size: 0.875rem;
}

.bg-success, .bg-danger, .bg-primary, .bg-info {
    background: linear-gradient(135deg, var(--bs-success) 0%, #0d6efd 100%) !important;
}

.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}

.bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
}

.bg-info {
    background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
}
</style>
@endpush
