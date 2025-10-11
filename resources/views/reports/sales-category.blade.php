@extends('layouts.app')

@section('title', 'Sales by Category Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">🏷️ Sales by Category</h1>
                        <p class="text-muted mb-0">Performance breakdown by product categories from {{ $startDate }} to {{ $endDate }}</p>
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
                <form method="GET" action="{{ route('reports.sales.category') }}" class="row align-items-end">
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

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-primary">
                <div class="metric-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $categoryData->count() }}</div>
                    <div class="metric-label">Categories</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-success">
                <div class="metric-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹{{ number_format($categoryData->sum('total_revenue'), 2) }}</div>
                    <div class="metric-label">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-info">
                <div class="metric-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ number_format($categoryData->sum('total_quantity')) }}</div>
                    <div class="metric-label">Items Sold</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $categoryData->first()->category_name ?? 'N/A' }}</div>
                    <div class="metric-label">Top Category</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Revenue Distribution</h5>
                <canvas id="categoryChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Category Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-table me-2"></i>Category Performance</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Revenue</th>
                                <th>Quantity Sold</th>
                                <th>Avg. Price</th>
                                <th>% of Total</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalRevenue = $categoryData->sum('total_revenue'); @endphp
                            @forelse($categoryData as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="category-icon me-2">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <strong>{{ $category->category_name }}</strong>
                                    </div>
                                </td>
                                <td class="fw-bold text-success">₹{{ number_format($category->total_revenue, 2) }}</td>
                                <td>{{ number_format($category->total_quantity) }}</td>
                                <td>₹{{ $category->total_quantity > 0 ? number_format($category->total_revenue / $category->total_quantity, 2) : '0.00' }}</td>
                                <td>
                                    @php $percentage = $totalRevenue > 0 ? ($category->total_revenue / $totalRevenue) * 100 : 0; @endphp
                                    <span class="badge bg-primary">{{ number_format($percentage, 1) }}%</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-{{
                                            $percentage >= 20 ? 'success' :
                                            ($percentage >= 10 ? 'warning' : 'danger')
                                        }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No category sales data found for the selected period.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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

.category-icon {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
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
    const ctx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($categoryData);

    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.category_name),
            datasets: [{
                data: categoryData.map(item => item.total_revenue),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                    '#e74a3b', '#858796', '#5a5c69', '#6f42c1'
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
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
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
});

function exportReport() {
    const startDate = '{{ $startDate }}';
    const endDate = '{{ $endDate }}';
    const url = `{{ route('reports.sales.export') }}?type=category&start_date=${startDate}&end_date=${endDate}`;
    window.open(url, '_blank');
}
</script>
@endpush
@endsection