@extends('layouts.app')

@section('title', 'Financial Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">💼 Financial Overview</h1>
                        <p class="text-muted mb-0">Revenue, expenses, and profitability from {{ $startDate }} to {{ $endDate }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <button class="btn btn-primary" onclick="exportReport()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <form method="GET" action="{{ route('reports.financial') }}" class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-success">
                <div class="metric-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹{{ number_format($revenue, 2) }}</div>
                    <div class="metric-label">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹0.00</div>
                    <div class="metric-label">Total Expenses</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-primary">
                <div class="metric-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹{{ number_format($revenue, 2) }}</div>
                    <div class="metric-label">Net Profit</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-info">
                <div class="metric-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">100%</div>
                    <div class="metric-label">Profit Margin</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Revenue vs Expenses Trend</h5>
                <canvas id="financialChart" height="80"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-pie-chart me-2"></i>Financial Breakdown</h5>
                <canvas id="breakdownChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Financial Details -->
    <div class="row">
        <div class="col-md-6">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3 text-success"><i class="fas fa-arrow-up me-2"></i>Revenue Sources</h5>
                <div class="revenue-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Product Sales</span>
                        <span class="fw-bold text-success">₹{{ number_format($revenue, 2) }}</span>
                    </div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>
                <div class="revenue-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Services</span>
                        <span class="fw-bold text-success">₹0.00</span>
                    </div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 0%"></div>
                    </div>
                </div>
                <div class="revenue-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Other Income</span>
                        <span class="fw-bold text-success">₹0.00</span>
                    </div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3 text-warning"><i class="fas fa-arrow-down me-2"></i>Expense Categories</h5>
                <div class="expense-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Cost of Goods Sold</span>
                        <span class="fw-bold text-warning">₹0.00</span>
                    </div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 0%"></div>
                    </div>
                </div>
                <div class="expense-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Operating Expenses</span>
                        <span class="fw-bold text-warning">₹0.00</span>
                    </div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 0%"></div>
                    </div>
                </div>
                <div class="expense-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Administrative</span>
                        <span class="fw-bold text-warning">₹0.00</span>
                    </div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 0%"></div>
                    </div>
                </div>
                <div class="expense-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Marketing</span>
                        <span class="fw-bold text-warning">₹0.00</span>
                    </div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Note about expenses -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Expense tracking is currently being implemented.
                Only revenue data is available at this time. For detailed profit & loss statements,
                please visit the <a href="{{ route('reports.profit-loss') }}" class="alert-link">Profit & Loss Report</a>.
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.metric-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 1.5rem;
    color: white;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    transition: transform 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
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

.metric-icon {
    font-size: 2rem;
    margin-right: 1rem;
    opacity: 0.8;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.metric-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.revenue-item, .expense-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e3e6f0;
}

.revenue-item:last-child, .expense-item:last-child {
    border-bottom: none;
}

.bg-white {
    background-color: #fff !important;
}

.rounded-lg {
    border-radius: 12px !important;
}

.shadow-sm {
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Financial trend chart
    const ctx1 = document.getElementById('financialChart').getContext('2d');
    const chart1 = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Revenue',
                data: [{{ $revenue/4 }}, {{ $revenue/3 }}, {{ $revenue/2 }}, {{ $revenue }}],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                fill: true
            }, {
                label: 'Expenses',
                data: [0, 0, 0, 0],
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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

    // Breakdown chart
    const ctx2 = document.getElementById('breakdownChart').getContext('2d');
    const chart2 = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Revenue', 'Expenses'],
            datasets: [{
                data: [{{ $revenue }}, 0],
                backgroundColor: ['#1cc88a', '#f6c23e'],
                borderWidth: 2,
                borderColor: '#fff'
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
});

function exportReport() {
    const startDate = '{{ $startDate }}';
    const endDate = '{{ $endDate }}';
    const url = `/reports/financial/export?start_date=${startDate}&end_date=${endDate}`;
    window.open(url, '_blank');
}
</script>
@endpush
@endsection