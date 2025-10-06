@extends('layouts.app')

@section('title', 'Products Management')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Products</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Products Management</h2>
                    <p class="text-muted mb-0">Manage your product catalog and inventory</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="exportProducts()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <button class="btn btn-outline-primary" onclick="showImportModal()">
                        <i class="bi bi-upload me-1"></i>Import
                    </button>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Add Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" placeholder="Search products...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="category_filter">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Stock Status</label>
                            <select class="form-select" id="stock_filter">
                                <option value="">All Products</option>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Customizable</label>
                            <select class="form-select" id="customizable_filter">
                                <option value="">All</option>
                                <option value="1">Customizable</option>
                                <option value="0">Standard</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" id="sort_by">
                                <option value="name">Name</option>
                                <option value="base_price">Price</option>
                                <option value="created_at">Date Added</option>
                                <option value="stock">Stock Level</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Order</label>
                            <select class="form-select" id="sort_order">
                                <option value="asc">ASC</option>
                                <option value="desc">DESC</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row mb-3" id="bulkActions" style="display: none;">
        <div class="col-12">
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span><strong id="selectedCount">0</strong> products selected</span>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="showBulkUpdateModal()">
                        <i class="bi bi-pencil me-1"></i>Bulk Update
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                        <i class="bi bi-trash me-1"></i>Delete Selected
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="productsTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Base Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> products
                        </div>
                        <nav id="pagination">
                            <!-- Pagination will be loaded via AJAX -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">CSV File</label>
                        <input type="file" class="form-control" name="file" accept=".csv" required>
                        <div class="form-text">Upload a CSV file with product data</div>
                    </div>
                    <div class="alert alert-info">
                        <strong>CSV Format:</strong> SKU, Name, Category ID, Description, Base Price, Weight, Is Customizable
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Products</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Update Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkUpdateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id">
                            <option value="">Don't Change</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Base Price</label>
                        <input type="number" class="form-control" name="base_price" step="0.01" placeholder="Don't change">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customizable</label>
                        <select class="form-select" name="is_customizable">
                            <option value="">Don't Change</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Products</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedProducts = new Set();
let currentPage = 1;
let perPage = 15;

$(document).ready(function() {
    loadProducts();

    // Filter change handlers
    $('#search, #category_filter, #stock_filter, #customizable_filter, #sort_by, #sort_order').on('change input', function() {
        currentPage = 1;
        loadProducts();
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.product-checkbox').prop('checked', isChecked);
        updateSelectedProducts();
    });

    // Import form
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        importProducts();
    });

    // Bulk update form
    $('#bulkUpdateForm').on('submit', function(e) {
        e.preventDefault();
        bulkUpdateProducts();
    });
});

function loadProducts() {
    const filters = {
        page: currentPage,
        per_page: perPage,
        search: $('#search').val(),
        category_id: $('#category_filter').val(),
        stock_filter: $('#stock_filter').val(),
        customizable_only: $('#customizable_filter').val(),
        sort_by: $('#sort_by').val(),
        sort_order: $('#sort_order').val(),
        paginate: true
    };

    $.get('{{ route("products.data") }}', filters)
        .done(function(response) {
            if (response.success) {
                renderProductsTable(response.data.data);
                renderPagination(response.data);
                updatePaginationInfo(response.data);
            }
        })
        .fail(function() {
            showAlert('Error loading products', 'danger');
        });
}

function renderProductsTable(products) {
    let html = '';

    products.forEach(function(product) {
        const stockStatus = getStockStatus(product.inventory);
        const customizableBadge = product.is_customizable ?
            '<span class="badge bg-info">Customizable</span>' :
            '<span class="badge bg-secondary">Standard</span>';

        html += `
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input product-checkbox"
                           value="${product.id}" onchange="updateSelectedProducts()">
                </td>
                <td><code>${product.sku}</code></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">${product.name}</h6>
                            <small class="text-muted">${product.description || 'No description'}</small>
                        </div>
                    </div>
                </td>
                <td>${product.category ? product.category.name : 'Uncategorized'}</td>
                <td>₹${parseFloat(product.base_price).toFixed(2)}</td>
                <td>
                    <span class="badge bg-${stockStatus.class}">${stockStatus.text}</span>
                    <small class="d-block text-muted">${product.inventory ? product.inventory.quantity_in_stock : 0} units</small>
                </td>
                <td>${customizableBadge}</td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="/products/${product.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="/products/${product.id}/edit" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-info" onclick="duplicateProduct(${product.id})">
                            <i class="bi bi-files"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#productsTableBody').html(html);
}

function getStockStatus(inventory) {
    if (!inventory) {
        return { class: 'secondary', text: 'No Stock Data' };
    }

    const stock = inventory.quantity_in_stock;
    const reorderLevel = inventory.reorder_level || 10;

    if (stock === 0) {
        return { class: 'danger', text: 'Out of Stock' };
    } else if (stock <= reorderLevel) {
        return { class: 'warning', text: 'Low Stock' };
    } else {
        return { class: 'success', text: 'In Stock' };
    }
}

function renderPagination(data) {
    // Implementation for pagination rendering
    $('#pagination').html(data.links || '');
}

function updatePaginationInfo(data) {
    $('#showingFrom').text(data.from || 0);
    $('#showingTo').text(data.to || 0);
    $('#totalRecords').text(data.total || 0);
}

function updateSelectedProducts() {
    selectedProducts.clear();
    $('.product-checkbox:checked').each(function() {
        selectedProducts.add($(this).val());
    });

    $('#selectedCount').text(selectedProducts.size);
    $('#bulkActions').toggle(selectedProducts.size > 0);
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: `{{ route('products.destroy', '') }}/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                loadProducts();
            } else {
                showAlert(response.message, 'danger');
            }
        })
        .fail(function() {
            showAlert('Error deleting product', 'danger');
        });
    }
}

function duplicateProduct(id) {
    $.post(`{{ route('products.duplicate', '') }}/${id}`, {
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (response.success) {
            showAlert(response.message, 'success');
            loadProducts();
        } else {
            showAlert(response.message, 'danger');
        }
    })
    .fail(function() {
        showAlert('Error duplicating product', 'danger');
    });
}

function exportProducts() {
    window.location.href = '{{ route("products.export") }}';
}

function showImportModal() {
    $('#importModal').modal('show');
}

function importProducts() {
    const formData = new FormData($('#importForm')[0]);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: '{{ route("products.import") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            showAlert(response.message, 'success');
            $('#importModal').modal('hide');
            loadProducts();
        } else {
            showAlert(response.message, 'danger');
        }
    })
    .fail(function() {
        showAlert('Error importing products', 'danger');
    });
}

function showBulkUpdateModal() {
    $('#bulkUpdateModal').modal('show');
}

function bulkUpdateProducts() {
    const updates = {
        product_ids: Array.from(selectedProducts),
        updates: {}
    };

    // Collect non-empty update values
    $('#bulkUpdateForm').find('input, select').each(function() {
        const value = $(this).val();
        if (value !== '' && $(this).attr('name')) {
            updates.updates[$(this).attr('name')] = value;
        }
    });

    updates._token = $('meta[name="csrf-token"]').attr('content');

    $.post('{{ route("products.bulk-update") }}', updates)
        .done(function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                $('#bulkUpdateModal').modal('hide');
                loadProducts();
                selectedProducts.clear();
                updateSelectedProducts();
            } else {
                showAlert(response.message, 'danger');
            }
        })
        .fail(function() {
            showAlert('Error updating products', 'danger');
        });
}

function bulkDelete() {
    if (confirm(`Are you sure you want to delete ${selectedProducts.size} selected products?`)) {
        // Implementation for bulk delete
        showAlert('Bulk delete feature coming soon', 'info');
    }
}

function showAlert(message, type) {
    const alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.container-fluid').prepend(alert);

    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush
