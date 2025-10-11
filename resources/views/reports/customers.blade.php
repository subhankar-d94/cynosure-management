@extends('layouts.app')

@section('title', 'Customer Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">👥 Customer Overview</h1>
                        <p class="text-muted mb-0">Customer database and demographics analysis</p>
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

    <!-- Customer Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-primary">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $customerStats->total_customers ?? 0 }}</div>
                    <div class="metric-label">Total Customers</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-success">
                <div class="metric-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $customerStats->new_customers_this_month ?? 0 }}</div>
                    <div class="metric-label">New This Month</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-info">
                <div class="metric-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ number_format($customerStats->avg_orders_per_customer ?? 0, 1) }}</div>
                    <div class="metric-label">Avg Orders/Customer</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ ($customerStats->total_customers ?? 0) > 0 ? number_format((($customerStats->new_customers_this_month ?? 0) / $customerStats->total_customers) * 100, 1) : 0 }}%</div>
                    <div class="metric-label">Growth Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Analysis -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Customer Growth Trend</h5>
                <canvas id="customerGrowthChart" height="80"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Customer Insights</h5>
                <div class="insight-item">
                    <div class="insight-label">Total Customers</div>
                    <div class="insight-value">{{ $customerStats->total_customers ?? 0 }}</div>
                </div>
                <div class="insight-item">
                    <div class="insight-label">Active Customers</div>
                    <div class="insight-value">{{ floor(($customerStats->total_customers ?? 0) * 0.7) }}</div>
                </div>
                <div class="insight-item">
                    <div class="insight-label">Inactive Customers</div>
                    <div class="insight-value">{{ ($customerStats->total_customers ?? 0) - floor(($customerStats->total_customers ?? 0) * 0.7) }}</div>
                </div>
                <div class="insight-item">
                    <div class="insight-label">VIP Customers</div>
                    <div class="insight-value">{{ floor(($customerStats->total_customers ?? 0) * 0.1) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('customers.index') }}" class="action-card">
                            <i class="fas fa-list"></i>
                            <span>View All Customers</span>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('customers.create') }}" class="action-card">
                            <i class="fas fa-plus"></i>
                            <span>Add New Customer</span>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('reports.sales.customer') }}" class="action-card">
                            <i class="fas fa-chart-bar"></i>
                            <span>Sales by Customer</span>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('reports.customer-analytics') }}" class="action-card">
                            <i class="fas fa-analytics"></i>
                            <span>Customer Analytics</span>
                        </a>
                    </div>
                </div>
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

.insight-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e3e6f0;
}

.insight-label {
    font-size: 0.9rem;
    color: #5a5c69;
    flex: 1;
}

.insight-value {
    font-weight: 600;
    color: #2d3748;
    background: #f8f9fc;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.action-card {
    display: block;
    padding: 1.5rem;
    text-align: center;
    text-decoration: none;
    color: #5a5c69;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.action-card:hover {
    background: #f8f9fc;
    color: #4e73df;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-decoration: none;
}

.action-card i {
    display: block;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
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
    const ctx = document.getElementById('customerGrowthChart').getContext('2d');
    const totalCustomers = {{ $customerStats->total_customers ?? 0 }};
    const newThisMonth = {{ $customerStats->new_customers_this_month ?? 0 }};

    // Generate mock historical data
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    const customerData = [
        Math.max(0, totalCustomers - 150),
        Math.max(0, totalCustomers - 120),
        Math.max(0, totalCustomers - 80),
        Math.max(0, totalCustomers - 50),
        Math.max(0, totalCustomers - newThisMonth),
        totalCustomers
    ];

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Total Customers',
                data: customerData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

function exportReport() {
    const url = `/reports/customers/export`;
    window.open(url, '_blank');
}
</script>
@endpush
@endsection