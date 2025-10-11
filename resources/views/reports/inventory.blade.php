@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">📦 Current Inventory Report</h1>
                        <p class="text-muted mb-0">Current stock levels and availability overview</p>
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
                    <div class="metric-value">{{ $inventoryData->count() }}</div>
                    <div class="metric-label">Total Products</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-success">
                <div class="metric-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ number_format($inventoryData->sum('quantity_in_stock')) }}</div>
                    <div class="metric-label">Total Stock</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $inventoryData->where('quantity_in_stock', '<=', 'reorder_level')->count() }}</div>
                    <div class="metric-label">Low Stock Items</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="metric-card bg-gradient-danger">
                <div class="metric-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">{{ $inventoryData->where('quantity_in_stock', 0)->count() }}</div>
                    <div class="metric-label">Out of Stock</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Search Products</label>
                        <input type="text" class="form-control" id="searchProducts" placeholder="Search by name or SKU...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stock Status</label>
                        <select class="form-select" id="stockFilter">
                            <option value="">All Items</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Supplier</label>
                        <select class="form-select" id="supplierFilter">
                            <option value="">All Suppliers</option>
                            @foreach($inventoryData->pluck('supplier.name')->unique()->filter() as $supplier)
                            <option value="{{ $supplier }}">{{ $supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-table me-2"></i>Inventory Details</h5>
                <div class="table-responsive">
                    <table class="table table-hover" id="inventoryTable">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Current Stock</th>
                                <th>Reorder Level</th>
                                <th>Supplier</th>
                                <th>Last Updated</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryData as $inventory)
                            <tr data-product="{{ strtolower($inventory->product->name ?? '') }}"
                                data-sku="{{ strtolower($inventory->product->sku ?? '') }}"
                                data-supplier="{{ strtolower($inventory->supplier->name ?? '') }}"
                                data-stock="{{ $inventory->quantity_in_stock }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-icon me-2">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $inventory->product->name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $inventory->product->category->name ?? 'No Category' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $inventory->product->sku ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $inventory->quantity_in_stock <= 0 ? 'text-danger' : ($inventory->quantity_in_stock <= $inventory->reorder_level ? 'text-warning' : 'text-success') }}">
                                        {{ number_format($inventory->quantity_in_stock) }}
                                    </span>
                                </td>
                                <td>{{ number_format($inventory->reorder_level) }}</td>
                                <td>{{ $inventory->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $inventory->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($inventory->quantity_in_stock <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($inventory->quantity_in_stock <= $inventory->reorder_level)
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No inventory data found.</p>
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

.metric-card.bg-gradient-warning {
    background: linear-gradient(135deg, #f6c23e 0%, #d4a027 100%);
    box-shadow: 0 4px 15px rgba(246, 194, 62, 0.3);
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
<script>
function applyFilters() {
    const searchTerm = document.getElementById('searchProducts').value.toLowerCase();
    const stockFilter = document.getElementById('stockFilter').value;
    const supplierFilter = document.getElementById('supplierFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#inventoryTable tbody tr[data-product]');

    rows.forEach(row => {
        const productName = row.getAttribute('data-product');
        const sku = row.getAttribute('data-sku');
        const supplier = row.getAttribute('data-supplier');
        const stock = parseInt(row.getAttribute('data-stock'));

        let showRow = true;

        // Search filter
        if (searchTerm && !productName.includes(searchTerm) && !sku.includes(searchTerm)) {
            showRow = false;
        }

        // Stock filter
        if (stockFilter) {
            if (stockFilter === 'out_of_stock' && stock > 0) showRow = false;
            if (stockFilter === 'low_stock' && (stock > 10 || stock <= 0)) showRow = false; // Assuming reorder level is around 10
            if (stockFilter === 'in_stock' && stock <= 0) showRow = false;
        }

        // Supplier filter
        if (supplierFilter && !supplier.includes(supplierFilter)) {
            showRow = false;
        }

        row.style.display = showRow ? '' : 'none';
    });
}

function exportReport() {
    const url = `{{ route('reports.inventory.export') }}`;
    window.open(url, '_blank');
}

// Real-time search
document.getElementById('searchProducts').addEventListener('input', applyFilters);
document.getElementById('stockFilter').addEventListener('change', applyFilters);
document.getElementById('supplierFilter').addEventListener('change', applyFilters);
</script>
@endpush
@endsection