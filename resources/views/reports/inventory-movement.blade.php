@extends('layouts.app')

@section('title', 'Inventory Movement Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">📊 Inventory Movement Report</h1>
                        <p class="text-muted mb-0">Stock in/out transactions and history from {{ $startDate }} to {{ $endDate }}</p>
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
                <form method="GET" action="{{ route('reports.inventory.movement') }}" class="row align-items-end">
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

    <!-- Coming Soon Message -->
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-5">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-clock fa-4x text-primary"></i>
                    </div>
                    <h3 class="mb-3">Feature Under Development</h3>
                    <p class="text-muted mb-4">
                        The Inventory Movement Report is currently being developed and will be available soon.
                        This report will include:
                    </p>

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="feature-item">
                                        <i class="fas fa-arrow-up text-success me-2"></i>
                                        <strong>Stock Inbound</strong>
                                        <small class="d-block text-muted">Purchase orders, returns, adjustments</small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="feature-item">
                                        <i class="fas fa-arrow-down text-danger me-2"></i>
                                        <strong>Stock Outbound</strong>
                                        <small class="d-block text-muted">Sales, transfers, adjustments</small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="feature-item">
                                        <i class="fas fa-chart-line text-info me-2"></i>
                                        <strong>Movement Trends</strong>
                                        <small class="d-block text-muted">Historical patterns and analysis</small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="feature-item">
                                        <i class="fas fa-filter text-warning me-2"></i>
                                        <strong>Advanced Filtering</strong>
                                        <small class="d-block text-muted">By product, supplier, movement type</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-muted mb-3">
                            In the meantime, you can view individual product movement history from the
                            <a href="{{ route('inventory.index') }}" class="text-primary">Inventory Management</a> section.
                        </p>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('inventory.index') }}" class="btn btn-primary">
                                <i class="fas fa-boxes"></i> View Inventory
                            </a>
                            <a href="{{ route('reports.inventory') }}" class="btn btn-outline-primary">
                                <i class="fas fa-warehouse"></i> Current Stock Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder Stats -->
    <div class="row mt-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-primary">
                <div class="metric-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">0</div>
                    <div class="metric-label">Inbound Movements</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-danger">
                <div class="metric-icon">
                    <i class="fas fa-minus-circle"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">0</div>
                    <div class="metric-label">Outbound Movements</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-success">
                <div class="metric-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">0</div>
                    <div class="metric-label">Total Movements</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-info">
                <div class="metric-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">0</div>
                    <div class="metric-label">Products Affected</div>
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
    opacity: 0.6;
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

.metric-card.bg-gradient-danger {
    background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
    box-shadow: 0 4px 15px rgba(231, 74, 59, 0.3);
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

.feature-item {
    text-align: left;
    padding: 1rem;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    background: #f8f9fc;
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

.text-primary {
    color: #4e73df !important;
}
</style>
@endpush

@push('scripts')
<script>
function exportReport() {
    alert('Export functionality will be available when the report is implemented.');
}
</script>
@endpush
@endsection