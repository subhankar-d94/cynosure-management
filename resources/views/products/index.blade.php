@extends('layouts.app')

@section('title', 'Products Management')

@section('content')
<div class="container-fluid products-page">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="header-content">
                <h1 class="page-title mb-2">
                    <i class="fas fa-boxes me-2"></i>Products Management
                </h1>
                <p class="page-subtitle mb-0">
                    Manage your product catalog and inventory
                    <span class="badge bg-primary ms-2">{{ $products->total() }} Total Products</span>
                </p>
            </div>
            <div class="header-actions d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Product
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card filters-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" id="filterForm">
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
                               placeholder="Search by name, SKU or description...">
                    </div>

                    <!-- Category Filter -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-tag me-1"></i>Category
                        </label>
                        <select class="form-select" name="category_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stock Status -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-warehouse me-1"></i>Stock Status
                        </label>
                        <select class="form-select" name="stock_filter">
                            <option value="">All Products</option>
                            <option value="in_stock" {{ request('stock_filter') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('stock_filter') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ request('stock_filter') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    <!-- Customizable -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sliders-h me-1"></i>Type
                        </label>
                        <select class="form-select" name="customizable_filter">
                            <option value="">All Types</option>
                            <option value="1" {{ request('customizable_filter') == '1' ? 'selected' : '' }}>Customizable</option>
                            <option value="0" {{ request('customizable_filter') == '0' ? 'selected' : '' }}>Standard</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sort me-1"></i>Sort By
                        </label>
                        <div class="input-group">
                            <select class="form-select" name="sort_by">
                                <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Date</option>
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="base_price" {{ request('sort_by') == 'base_price' ? 'selected' : '' }}>Price</option>
                                <option value="stock" {{ request('sort_by') == 'stock' ? 'selected' : '' }}>Stock</option>
                            </select>
                            <select class="form-select" name="sort_order" style="max-width: 80px;">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>ASC</option>
                                <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>DESC</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table Card -->
    <div class="card products-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Products List
                </h5>
                <div class="text-muted">
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover products-table mb-0">
                    <thead>
                        <tr>
                            <th width="90">Image</th>
                            <th>Product Name</th>
                            <th width="180">Category</th>
                            <th width="110">Price</th>
                            <th width="110">Stock</th>
                            <th width="170" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <!-- Image -->
                            <td>
                                <div class="product-thumbnail-wrapper">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ asset('storage/' . $product->images[0]) }}"
                                             alt="{{ $product->name }}"
                                             class="product-thumbnail"
                                             onerror="this.parentElement.innerHTML='<div class=\'product-thumbnail-placeholder\'><i class=\'fas fa-box\'></i></div>'">
                                    @else
                                        <div class="product-thumbnail-placeholder">
                                            <i class="fas fa-box"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Product Name -->
                            <td>
                                <div class="product-info">
                                    <h6 class="product-name mb-1">{{ $product->name }}</h6>
                                    @if($product->description)
                                        <p class="product-description mb-0">{{ Str::limit($product->description, 80) }}</p>
                                    @endif
                                </div>
                            </td>

                            <!-- Category -->
                            <td>
                                @if($product->category)
                                    <span class="category-badge">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Uncategorized</span>
                                @endif
                            </td>

                            <!-- Price -->
                            <td>
                                <div class="price-display">
                                    ₹{{ number_format($product->base_price, 2) }}
                                </div>
                            </td>

                            <!-- Stock -->
                            <td>
                                @php
                                    $inventory = $product->inventory;
                                    $stockQty = $inventory ? $inventory->quantity_in_stock : 0;
                                    $reorderLevel = $inventory ? $inventory->reorder_level : 10;

                                    if ($stockQty == 0) {
                                        $stockClass = 'danger';
                                        $stockText = 'Out of Stock';
                                    } elseif ($stockQty <= $reorderLevel) {
                                        $stockClass = 'warning';
                                        $stockText = 'Low Stock';
                                    } else {
                                        $stockClass = 'success';
                                        $stockText = 'In Stock';
                                    }
                                @endphp
                                <div class="stock-info">
                                    <span class="badge bg-{{ $stockClass }}">{{ $stockText }}</span>
                                    <small class="d-block text-muted mt-1">{{ $stockQty }} units</small>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="btn-action btn-view"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="btn-action btn-edit"
                                       title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-action btn-delete"
                                                title="Delete Product">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Products Found</h5>
                                    <p class="text-muted mb-3">Try adjusting your filters or add a new product</p>
                                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add Your First Product
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
        @if($products->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="pagination-info mb-2 mb-md-0">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                </div>
                <div>
                    {{ $products->links('pagination::bootstrap-5') }}
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
.products-page {
    padding: 1.5rem;
    background: #f8f9fa;
    overflow-x: hidden;
}

/* Fix horizontal scrollbar */
body {
    overflow-x: hidden;
}

.container-fluid {
    overflow-x: hidden;
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

/* Products Card */
.products-card {
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
}

.products-card .card-header {
    background: #fff;
    border-bottom: 2px solid #f3f4f6;
    padding: 1.25rem 1.5rem;
}

.products-card .card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
}

/* Table Responsive */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

/* Products Table */
.products-table {
    font-size: 0.9rem;
    width: 100%;
    table-layout: auto;
}

.products-table thead th {
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

.products-table tbody td {
    padding: 0.75rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
}

.products-table tbody tr {
    transition: background-color 0.2s ease;
}

.products-table tbody tr:hover {
    background: #f9fafb;
}

/* Product Thumbnail */
.product-thumbnail-wrapper {
    position: relative;
    text-align: center;
    overflow: hidden;
    width: 70px;
    height: 70px;
    margin: 0 auto;
}

.product-thumbnail {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
}

.product-thumbnail:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.product-thumbnail-placeholder {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-radius: 10px;
    border: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.product-thumbnail-placeholder:hover {
    background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
    color: #6b7280;
    border-color: #d1d5db;
}

/* Product Info */
.product-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #111827;
}

.product-description {
    font-size: 0.8rem;
    color: #6b7280;
    line-height: 1.4;
}

/* Category Badge */
.category-badge {
    display: inline-block;
    padding: 0.4rem 0.75rem;
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Price Display */
.price-display {
    font-size: 1.1rem;
    font-weight: 700;
    color: #10b981;
}

/* Stock Info */
.stock-info .badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
    font-weight: 600;
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
    .products-page {
        padding: 1rem;
    }

    .page-header {
        padding: 1.5rem;
    }

    .page-title {
        font-size: 1.4rem;
    }

    .products-table {
        font-size: 0.85rem;
    }

    .product-thumbnail {
        width: 60px;
        height: 60px;
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
