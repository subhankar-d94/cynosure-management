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
                            <h5 class="card-title mb-1">Total Suppliers</h5>
                            <h3 class="mb-0">{{ $stats['total_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-truck fa-2x"></i>
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
                            <h5 class="card-title mb-1">Active</h5>
                            <h3 class="mb-0">{{ $stats['active_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Inactive</h5>
                            <h3 class="mb-0">{{ $stats['inactive_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-pause-circle fa-2x"></i>
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
                            <h5 class="card-title mb-1">Pending</h5>
                            <h3 class="mb-0">{{ $stats['pending_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Supplier Management Card -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="card-title mb-0">Supplier Management</h3>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Supplier
                        </a>
                        <button class="btn btn-outline-secondary" onclick="exportSuppliers()">
                            <i class="fas fa-download"></i> Export CSV
                        </button>
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
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="categoryFilter" class="form-label">Category</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="raw_materials">Raw Materials</option>
                        <option value="manufacturing">Manufacturing</option>
                        <option value="technology">Technology</option>
                        <option value="services">Services</option>
                        <option value="logistics">Logistics</option>
                        <option value="packaging">Packaging</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="consulting">Consulting</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="searchFilter" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchFilter" placeholder="Company name, contact person, email, phone...">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearFilters()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Supplier Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Contact Person</th>
                            <th>Category</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        {{ strtoupper(substr($supplier->company_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="text-decoration-none">
                                            <strong>{{ $supplier->company_name }}</strong>
                                        </a>
                                        @if($supplier->email)
                                        <br><small class="text-muted">{{ $supplier->email }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $supplier->contact_person }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ ucwords(str_replace('_', ' ', $supplier->category)) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $supplier->phone ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{
                                    $supplier->status === 'active' ? 'success' :
                                    ($supplier->status === 'pending' ? 'warning' : 'secondary')
                                }}">
                                    {{ ucfirst($supplier->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" onclick="deleteSupplier({{ $supplier->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-truck fa-3x mb-3"></i>
                                    <p>No suppliers found.</p>
                                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Supplier
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
                {{ $suppliers->links() }}
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
    $('#statusFilter, #categoryFilter').change(function() {
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
            category: $('#categoryFilter').val(),
            search: $('#searchFilter').val()
        });
        window.location.href = '{{ route("suppliers.index") }}?' + params.toString();
    }

    window.clearFilters = function() {
        window.location.href = '{{ route("suppliers.index") }}';
    };

    window.deleteSupplier = function(supplierId) {
        if (confirm('Are you sure you want to delete this supplier? This action cannot be undone.')) {
            const form = document.getElementById('deleteForm');
            form.action = `/suppliers/${supplierId}`;
            form.submit();
        }
    };

    window.exportSuppliers = function() {
        const params = new URLSearchParams({
            export: 1,
            status: $('#statusFilter').val(),
            category: $('#categoryFilter').val(),
            search: $('#searchFilter').val()
        });
        window.open('{{ route("suppliers.index") }}?' + params.toString(), '_blank');
    };
});
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-weight: 500;
}
</style>
@endpush
