@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container-fluid customers-page">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="header-content">
                <h1 class="page-title mb-2">
                    <i class="fas fa-users me-2"></i>Customers Management
                </h1>
                <p class="page-subtitle mb-0">
                    Manage your customer database and relationships
                    <span class="badge bg-primary ms-2">{{ $customers->total() }} Total Customers</span>
                </p>
            </div>
            <div class="header-actions d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Customer
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h6 class="stat-label">Total Customers</h6>
                    <h3 class="stat-value">{{ number_format($stats['total_customers']) }}</h3>
                    <small class="text-muted">Active customers</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-content">
                    <h6 class="stat-label">New This Month</h6>
                    <h3 class="stat-value">{{ number_format($stats['new_customers']) }}</h3>
                    <small class="text-muted">Recent additions</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-cart-plus"></i>
                </div>
                <div class="stat-content">
                    <h6 class="stat-label">Active Orders</h6>
                    <h3 class="stat-value">{{ number_format($stats['active_orders']) }}</h3>
                    <small class="text-muted">In progress</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-content">
                    <h6 class="stat-label">Total Revenue</h6>
                    <h3 class="stat-value">₹{{ number_format($stats['total_revenue'], 2) }}</h3>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card filters-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search me-1"></i>Search
                        </label>
                        <input type="text"
                               class="form-control"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by name, email, phone...">
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-toggle-on me-1"></i>Status
                        </label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Customer Type -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-user-tag me-1"></i>Customer Type
                        </label>
                        <select class="form-select" name="customer_type">
                            <option value="">All Types</option>
                            <option value="individual" {{ request('customer_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="business" {{ request('customer_type') == 'business' ? 'selected' : '' }}>Business</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sort me-1"></i>Sort By
                        </label>
                        <select class="form-select" name="sort_by">
                            <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                            <option value="total_orders" {{ request('sort_by') == 'total_orders' ? 'selected' : '' }}>Total Orders</option>
                            <option value="total_spent" {{ request('sort_by') == 'total_spent' ? 'selected' : '' }}>Total Spent</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-arrow-down-up-across-line me-1"></i>Order
                        </label>
                        <select class="form-select" name="sort_order">
                            <option value="asc" {{ request('sort_order', 'asc') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table Card -->
    <div class="card customers-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Customers List
                </h5>
                <div class="text-muted">
                    Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover customers-table mb-0">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th width="140">Contact</th>
                            <th width="120">Total Orders</th>
                            <th width="140">Total Spent</th>
                            <th width="140">Last Order</th>
                            <th width="100">Status</th>
                            <th width="200" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <!-- Customer Name -->
                            <td>
                                <h6 class="customer-name mb-0">{{ $customer->name }}</h6>
                            </td>

                            <!-- Contact -->
                            <td>
                                <small class="text-muted">{{ $customer->phone ?? 'No phone' }}</small>
                            </td>

                            <!-- Total Orders -->
                            <td>
                                <span class="fw-bold">{{ $customer->total_orders ?? 0 }}</span>
                                <small class="d-block text-muted">orders</small>
                            </td>

                            <!-- Total Spent -->
                            <td>
                                <span class="fw-bold text-success">₹{{ number_format($customer->total_spent ?? 0, 2) }}</span>
                            </td>

                            <!-- Last Order -->
                            <td>
                                <small>
                                    @if($customer->last_order_date)
                                        {{ \Carbon\Carbon::parse($customer->last_order_date)->format('d M Y') }}
                                    @else
                                        Never
                                    @endif
                                </small>
                            </td>

                            <!-- Status -->
                            <td>
                                @if($customer->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('customers.show', $customer->id) }}"
                                       class="btn-action btn-view"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                       class="btn-action btn-edit"
                                       title="Edit Customer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-action btn-delete"
                                                title="Delete Customer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Customers Found</h5>
                                    <p class="text-muted mb-3">Try adjusting your filters or add a new customer</p>
                                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add Your First Customer
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Footer -->
        @if($customers->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="pagination-info mb-2 mb-md-0">
                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
                </div>
                <div>
                    {{ $customers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
/* Page Styles */
.customers-page {
    padding: 1.5rem;
    background: #f8f9fa;
}

/* Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    border-radius: 16px;
    color: #fff;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0;
}

.page-subtitle {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.9);
}

.page-header .badge {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    padding: 0.35rem 0.75rem;
    font-weight: 600;
}

/* Stat Cards */
.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
    border-left: 4px solid;
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.stat-card-primary { border-left-color: #6366f1; }
.stat-card-success { border-left-color: #10b981; }
.stat-card-info { border-left-color: #3b82f6; }
.stat-card-warning { border-left-color: #f59e0b; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-card-primary .stat-icon {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.stat-card-success .stat-icon {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.stat-card-info .stat-icon {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.stat-card-warning .stat-icon {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.25rem;
}

/* Filters Card */
.filters-card {
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    border: none;
}

.filters-card .card-body {
    padding: 1.5rem;
}

.form-label {
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    padding: 0.6rem 0.75rem;
    font-size: 0.9rem;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

/* Customers Card */
.customers-card {
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
}

.customers-card .card-header {
    background: #fff;
    border-bottom: 2px solid #f3f4f6;
    padding: 1.25rem 1.5rem;
}

.customers-card .card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
}

/* Customers Table */
.customers-table {
    font-size: 0.9rem;
    width: 100%;
}

.customers-table thead th {
    background: #f9fafb;
    color: #6b7280;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 0.75rem 1rem;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}

.customers-table tbody td {
    padding: 0.75rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
}

.customers-table tbody tr {
    transition: background-color 0.2s ease;
}

.customers-table tbody tr:hover {
    background: #f9fafb;
}

/* Customer Name */
.customer-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #111827;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-action.btn-view {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.btn-action.btn-view:hover {
    background: #6366f1;
    color: #fff;
    transform: translateY(-2px);
}

.btn-action.btn-edit {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.btn-action.btn-edit:hover {
    background: #f59e0b;
    color: #fff;
    transform: translateY(-2px);
}

.btn-action.btn-delete {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.btn-action.btn-delete:hover {
    background: #ef4444;
    color: #fff;
    transform: translateY(-2px);
}

/* Empty State */
.empty-state {
    padding: 3rem 2rem;
}

.empty-state i {
    opacity: 0.4;
}

/* Pagination */
.card-footer {
    background: #f9fafb;
    border-top: 2px solid #e5e7eb;
    padding: 1rem 1.5rem;
}

.pagination-info {
    font-size: 0.9rem;
    color: #6b7280;
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    border-radius: 8px;
    margin: 0 0.15rem;
    border: 1px solid #e5e7eb;
    color: #6366f1;
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    background: #6366f1;
    color: #fff;
    border-color: #6366f1;
}

.page-item.active .page-link {
    background: #6366f1;
    border-color: #6366f1;
}

/* Responsive */
@media (max-width: 768px) {
    .customers-page {
        padding: 1rem;
    }

    .page-header {
        padding: 1.5rem;
    }

    .page-title {
        font-size: 1.4rem;
    }

    .stat-card {
        padding: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }

    .stat-value {
        font-size: 1.5rem;
    }

    .customers-table {
        font-size: 0.85rem;
    }

    .action-buttons {
        gap: 0.25rem;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
}
</style>
@endpush
