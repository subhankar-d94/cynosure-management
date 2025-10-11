@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">📈 Sales Report</h1>
                        <p class="text-muted mb-0">Comprehensive sales data and trends from {{ $startDate }} to {{ $endDate }}</p>
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
                <form method="GET" action="{{ route('reports.sales') }}" class="row align-items-end">
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
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹{{ number_format($salesData->sum('total_amount'), 2) }}</div>
                    <div class="metric-label">Total Sales</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-success">
                <div class="metric-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $salesData->count() }}</div>
                    <div class="metric-label">Total Orders</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-info">
                <div class="metric-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">₹{{ $salesData->count() > 0 ? number_format($salesData->sum('total_amount') / $salesData->count(), 2) : '0.00' }}</div>
                    <div class="metric-label">Average Order Value</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $salesData->unique('customer_id')->count() }}</div>
                    <div class="metric-label">Unique Customers</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-table me-2"></i>Sales Transactions</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesData as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $order->customer->name ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $order->customer->email ?? '' }}</small>
                                    </div>
                                </td>
                                <td>{{ $order->items->count() }} items</td>
                                <td class="fw-bold text-success">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{
                                        $order->status === 'completed' ? 'success' :
                                        ($order->status === 'pending' ? 'warning' : 'secondary')
                                    }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No sales data found for the selected period.</p>
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
<script>
function exportReport() {
    const startDate = '{{ $startDate }}';
    const endDate = '{{ $endDate }}';
    const url = `{{ route('reports.sales.export') }}?start_date=${startDate}&end_date=${endDate}`;
    window.open(url, '_blank');
}
</script>
@endpush
@endsection