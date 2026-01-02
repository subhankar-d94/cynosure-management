@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Orders</h5>
                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Draft</h5>
                            <h3 class="mb-0">{{ $stats['draft'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-file-alt fa-2x"></i>
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
                            <h5 class="card-title mb-1">Pending</h5>
                            <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Completed</h5>
                            <h3 class="mb-0">{{ $stats['completed'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Purchase Orders Card -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="card-title mb-0">Purchase Orders</h3>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Purchase Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="supplierFilter" class="form-label">Supplier</label>
                    <select class="form-select" id="supplierFilter">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="searchFilter" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchFilter" placeholder="PO number, supplier...">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearFilters()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Purchase Orders Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Order Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                        <tr>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}" class="text-decoration-none">
                                    <strong>{{ $purchase->purchase_order_number }}</strong>
                                </a>
                            </td>
                            <td>
                                {{ $purchase->supplier->company_name ?? 'N/A' }}
                            </td>
                            <td>
                                {{ date('M d, Y', strtotime($purchase->purchase_date)) }}
                            </td>
                            <td>
                                <strong>₹{{ number_format($purchase->total_amount, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{
                                    $purchase->status === 'completed' ? 'success' :
                                    ($purchase->status === 'approved' ? 'info' :
                                    ($purchase->status === 'pending' ? 'warning' :
                                    ($purchase->status === 'cancelled' ? 'danger' : 'secondary')))
                                }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" onclick="deletePurchase({{ $purchase->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                    <p>No purchase orders found.</p>
                                    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create First Purchase Order
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filter change handlers
    $('#statusFilter, #supplierFilter').change(function() {
        applyFilters();
    });

    // Search with debounce
    let searchTimeout;
    $('#searchFilter').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            applyFilters();
        }, 500);
    });

    function applyFilters() {
        const params = new URLSearchParams({
            status: $('#statusFilter').val(),
            supplier_id: $('#supplierFilter').val(),
            search: $('#searchFilter').val()
        });
        window.location.href = '{{ route("purchases.index") }}?' + params.toString();
    }

    window.clearFilters = function() {
        window.location.href = '{{ route("purchases.index") }}';
    };

    window.deletePurchase = function(purchaseId) {
        if (confirm('Are you sure you want to delete this purchase order? This action cannot be undone.')) {
            const form = document.getElementById('deleteForm');
            form.action = `/purchases/${purchaseId}`;
            form.submit();
        }
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

.badge {
    font-weight: 500;
}

.table td {
    vertical-align: middle;
}
</style>
@endpush
