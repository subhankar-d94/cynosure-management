@extends('layouts.app')

@section('title', 'Sales by Product Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">📦 Sales by Product</h1>
                        <p class="text-muted mb-0">Best-selling products and inventory turnover from {{ $startDate }} to {{ $endDate }}</p>
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
                <form method="GET" action="{{ route('reports.sales.product') }}" class="row align-items-end">
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
                    <i class="fas fa-box"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $productData->count() }}</div>
                    <div class="metric-label">Products Sold</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-success">
                <div class="metric-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹{{ number_format($productData->sum('total_revenue'), 2) }}</div>
                    <div class="metric-label">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-info">
                <div class="metric-icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ number_format($productData->sum('total_quantity')) }}</div>
                    <div class="metric-label">Units Sold</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $productData->first()->product_name ?? 'N/A' }}</div>
                    <div class="metric-label">Top Product</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Top 10 Products by Revenue</h5>
                <canvas id="productChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Product Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-table me-2"></i>Product Performance</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                                <th>Avg. Price</th>
                                <th>Performance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalRevenue = $productData->sum('total_revenue'); @endphp
                            @forelse($productData as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-icon me-2">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $product->product_name }}</strong>
                                            <br><small class="text-muted">Product</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $product->sku }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ number_format($product->total_quantity) }}</span>
                                </td>
                                <td class="fw-bold text-success">₹{{ number_format($product->total_revenue, 2) }}</td>
                                <td>₹{{ $product->total_quantity > 0 ? number_format($product->total_revenue / $product->total_quantity, 2) : '0.00' }}</td>
                                <td>
                                    @php $percentage = $totalRevenue > 0 ? ($product->total_revenue / $totalRevenue) * 100 : 0; @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                            <div class="progress-bar bg-{{
                                                $percentage >= 10 ? 'success' :
                                                ($percentage >= 5 ? 'warning' : 'danger')
                                            }}" style="width: {{ min($percentage * 5, 100) }}%"></div>
                                        </div>
                                        <small>{{ number_format($percentage, 1) }}%</small>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewProductDetails('{{ $product->sku }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No product sales data found for the selected period.</p>
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

.product-icon {
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
    const ctx = document.getElementById('productChart').getContext('2d');
    const productData = @json($productData->take(10));

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: productData.map(item => item.product_name.length > 15 ?
                item.product_name.substring(0, 15) + '...' : item.product_name),
            datasets: [{
                label: 'Revenue (₹)',
                data: productData.map(item => item.total_revenue),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
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
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const product = productData[context.dataIndex];
                            return [
                                `Revenue: ₹${context.parsed.y.toLocaleString()}`,
                                `Units Sold: ${product.total_quantity}`
                            ];
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
    const url = `{{ route('reports.sales.export') }}?type=product&start_date=${startDate}&end_date=${endDate}`;
    window.open(url, '_blank');
}

function viewProductDetails(sku) {
    // You can implement product details modal or redirect
    alert('Product details for SKU: ' + sku);
}
</script>
@endpush
@endsection