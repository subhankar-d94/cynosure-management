@extends('layouts.app')

@section('title', 'Inventory Valuation Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">💰 Inventory Valuation Report</h1>
                        <p class="text-muted mb-0">Stock value and cost analysis overview</p>
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

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-primary">
                <div class="metric-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $valuation->count() }}</div>
                    <div class="metric-label">Total Products</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-success">
                <div class="metric-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹{{ number_format($totalValue, 2) }}</div>
                    <div class="metric-label">Total Inventory Value</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-info">
                <div class="metric-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹{{ $valuation->count() > 0 ? number_format($totalValue / $valuation->count(), 2) : '0.00' }}</div>
                    <div class="metric-label">Avg. Product Value</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $valuation->sortByDesc('total_value')->first()['product_name'] ?? 'N/A' }}</div>
                    <div class="metric-label">Highest Value Product</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Value Distribution</h5>
                <canvas id="valuationChart" height="120"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Valuation Summary</h5>
                <div class="summary-item">
                    <span class="summary-label">Products with High Value (>₹10,000)</span>
                    <span class="summary-value">{{ $valuation->where('total_value', '>', 10000)->count() }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Products with Medium Value (₹1,000-₹10,000)</span>
                    <span class="summary-value">{{ $valuation->whereBetween('total_value', [1000, 10000])->count() }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Products with Low Value (<₹1,000)</span>
                    <span class="summary-value">{{ $valuation->where('total_value', '<', 1000)->count() }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Zero Value Products</span>
                    <span class="summary-value">{{ $valuation->where('total_value', 0)->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Valuation Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-table me-2"></i>Product Valuation Details</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Stock Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Value</th>
                                <th>Value Category</th>
                                <th>% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($valuation->sortByDesc('total_value') as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-icon me-2">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <strong>{{ $item['product_name'] }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $item['sku'] }}</span>
                                </td>
                                <td>{{ number_format($item['stock_quantity']) }}</td>
                                <td>₹{{ number_format($item['unit_price'], 2) }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($item['total_value'], 2) }}</td>
                                <td>
                                    @php
                                        $value = $item['total_value'];
                                        if ($value > 10000) {
                                            $category = 'High';
                                            $badgeClass = 'success';
                                        } elseif ($value >= 1000) {
                                            $category = 'Medium';
                                            $badgeClass = 'warning';
                                        } elseif ($value > 0) {
                                            $category = 'Low';
                                            $badgeClass = 'info';
                                        } else {
                                            $category = 'Zero';
                                            $badgeClass = 'secondary';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $category }}</span>
                                </td>
                                <td>
                                    @php $percentage = $totalValue > 0 ? ($item['total_value'] / $totalValue) * 100 : 0; @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ min($percentage * 5, 100) }}%"></div>
                                        </div>
                                        <small>{{ number_format($percentage, 1) }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No valuation data found.</p>
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

.summary-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e3e6f0;
}

.summary-label {
    font-size: 0.85rem;
    color: #5a5c69;
    flex: 1;
}

.summary-value {
    font-weight: 600;
    color: #2d3748;
    background: #f8f9fc;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
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
    const ctx = document.getElementById('valuationChart').getContext('2d');
    const valuationData = @json($valuation->take(10));

    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: valuationData.map(item => item.product_name.length > 20 ?
                item.product_name.substring(0, 20) + '...' : item.product_name),
            datasets: [{
                data: valuationData.map(item => item.total_value),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                    '#e74a3b', '#858796', '#5a5c69', '#6f42c1',
                    '#fd7e14', '#20c997'
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
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
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
    const url = `{{ route('reports.inventory.export') }}?type=valuation`;
    window.open(url, '_blank');
}
</script>
@endpush
@endsection