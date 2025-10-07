@extends('layouts.app')

@section('title', 'Categories')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard.index') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">
            <i class="bi bi-tags me-1"></i>Categories
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Categories</h2>
                    <p class="text-muted mb-0">Manage product categories and subcategories</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Category
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search categories...">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filter</label>
                            <select class="form-select" id="filterSelect">
                                <option value="">All Categories</option>
                                <option value="parent_only">Parent Categories Only</option>
                                <option value="active_only">Active Only</option>
                                <option value="inactive_only">Inactive Only</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" id="sortSelect">
                                <option value="name">Name</option>
                                <option value="created_at">Date Created</option>
                                <option value="updated_at">Last Updated</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary flex-fill" onclick="applyFilters()">
                                    <i class="bi bi-funnel"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary flex-fill" onclick="resetFilters()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>Category List
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="toggleHierarchy()">
                            <i class="bi bi-diagram-3" id="hierarchyIcon"></i>
                            <span id="hierarchyText">Tree View</span>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" onclick="bulkAction('activate')">
                                    <i class="bi bi-check-circle me-2"></i>Bulk Activate
                                </a></li>
                                <li><a class="dropdown-item" onclick="bulkAction('deactivate')">
                                    <i class="bi bi-x-circle me-2"></i>Bulk Deactivate
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" onclick="bulkAction('delete')">
                                    <i class="bi bi-trash me-2"></i>Bulk Delete
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="categoriesTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Category Name</th>
                                    <th>Parent</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categoriesTableBody">
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing <span id="showingStart">0</span> to <span id="showingEnd">0</span> of <span id="totalCount">0</span> categories
                        </div>
                        <nav id="pagination">
                            <!-- Pagination will be inserted here -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-tags me-2"></i>
                    <span id="modalTitle">Add Category</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Category</label>
                            <select class="form-select" id="categoryParent" name="parent_id">
                                <option value="">Select parent category (optional)</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="categoryDescription" name="description" rows="3" placeholder="Enter category description..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="categoryActive" name="is_active" checked>
                                <label class="form-check-label" for="categoryActive">
                                    Active Category
                                </label>
                            </div>
                            <div class="form-text">Inactive categories won't be available for product assignment</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveButton">
                        <i class="bi bi-check me-1"></i>Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let isHierarchyView = false;
let categories = [];
let editingCategoryId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadParentOptions();
    bindEventListeners();
});

function bindEventListeners() {
    // Search input
    document.getElementById('searchInput').addEventListener('input', debounce(applyFilters, 500));

    // Filter and sort changes
    document.getElementById('filterSelect').addEventListener('change', applyFilters);
    document.getElementById('sortSelect').addEventListener('change', applyFilters);

    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.category-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Category form submission
    document.getElementById('categoryForm').addEventListener('submit', handleFormSubmit);

    // Modal reset
    document.getElementById('categoryModal').addEventListener('hidden.bs.modal', resetForm);
}

function loadCategories(page = 1) {
    showLoading(document.getElementById('categoriesTable'));

    const params = new URLSearchParams({
        page: page,
        search: document.getElementById('searchInput').value,
        filter: document.getElementById('filterSelect').value,
        sort: document.getElementById('sortSelect').value,
        paginate: 'true'
    });

    fetch(`/categories?${params}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            categories = data.data.data || data.data;
            renderCategoriesTable(data.data);
            updatePagination(data.data);
        } else {
            showToast('Failed to load categories', 'danger');
        }
    })
    .catch(error => {
        console.error('Error loading categories:', error);
        showToast('Failed to load categories', 'danger');
    })
    .finally(() => {
        hideLoading(document.getElementById('categoriesTable'));
    });
}

function renderCategoriesTable(data) {
    const tbody = document.getElementById('categoriesTableBody');
    tbody.innerHTML = '';

    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-tags fs-1 d-block mb-3"></i>
                    <h5>No categories found</h5>
                    <p class="mb-0">Create your first category to get started</p>
                </td>
            </tr>
        `;
        return;
    }

    data.data.forEach(category => {
        const row = createCategoryRow(category);
        tbody.insertAdjacentHTML('beforeend', row);
    });

    // Update stats
    document.getElementById('showingStart').textContent = data.from || 1;
    document.getElementById('showingEnd').textContent = data.to || data.data.length;
    document.getElementById('totalCount').textContent = data.total || data.data.length;
}

function createCategoryRow(category) {
    const statusBadge = category.is_active
        ? '<span class="badge bg-success">Active</span>'
        : '<span class="badge bg-secondary">Inactive</span>';

    const parentName = category.parent ? category.parent.name : '-';
    const productCount = category.products_count || 0;
    const createdDate = formatDate(category.created_at);

    const indent = isHierarchyView && category.parent_id ? 'style="padding-left: 2rem;"' : '';

    return `
        <tr data-category-id="${category.id}">
            <td>
                <input type="checkbox" class="form-check-input category-checkbox" value="${category.id}">
            </td>
            <td ${indent}>
                <div class="d-flex align-items-center">
                    ${category.parent_id && isHierarchyView ? '<i class="bi bi-arrow-return-right text-muted me-2"></i>' : ''}
                    <div>
                        <h6 class="mb-0">${category.name}</h6>
                        ${category.description ? `<small class="text-muted">${category.description}</small>` : ''}
                    </div>
                </div>
            </td>
            <td>${parentName}</td>
            <td>
                <span class="badge bg-light text-dark">${productCount}</span>
            </td>
            <td>${statusBadge}</td>
            <td>
                <small class="text-muted">${createdDate}</small>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editCategory(${category.id})" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-${category.is_active ? 'warning' : 'success'}"
                            onclick="toggleStatus(${category.id})"
                            title="${category.is_active ? 'Deactivate' : 'Activate'}">
                        <i class="bi bi-${category.is_active ? 'pause' : 'play'}"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteCategory(${category.id})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
}

function updatePagination(data) {
    const pagination = document.getElementById('pagination');

    if (!data.last_page || data.last_page <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHtml = '<ul class="pagination pagination-sm mb-0">';

    // Previous button
    if (data.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" onclick="loadCategories(${data.current_page - 1})">Previous</a>
            </li>
        `;
    }

    // Page numbers
    for (let i = Math.max(1, data.current_page - 2); i <= Math.min(data.last_page, data.current_page + 2); i++) {
        paginationHtml += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" onclick="loadCategories(${i})">${i}</a>
            </li>
        `;
    }

    // Next button
    if (data.current_page < data.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" onclick="loadCategories(${data.current_page + 1})">Next</a>
            </li>
        `;
    }

    paginationHtml += '</ul>';
    pagination.innerHTML = paginationHtml;
}

function loadParentOptions() {
    fetch('/categories/hierarchy', {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('categoryParent');
            select.innerHTML = '<option value="">Select parent category (optional)</option>';

            data.data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                select.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Error loading parent options:', error);
    });
}

function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);

    // Handle checkbox value properly
    const isActive = document.getElementById('categoryActive').checked;
    formData.set('is_active', isActive ? '1' : '0');

    const url = editingCategoryId ? `/categories/${editingCategoryId}` : '/categories';
    const method = editingCategoryId ? 'PUT' : 'POST';

    // Add method override for PUT requests
    if (method === 'PUT') {
        formData.append('_method', 'PUT');
    }

    showLoading(document.getElementById('saveButton'));

    fetch(url, {
        method: 'POST', // Always use POST with method override for CSRF
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData // Use FormData instead of JSON to include CSRF token
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Category saved successfully');
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            loadCategories(currentPage);
            loadParentOptions();
        } else {
            if (data.errors) {
                displayFormErrors(data.errors);
            } else {
                showToast(data.message || 'Failed to save category', 'danger');
            }
        }
    })
    .catch(error => {
        console.error('Error saving category:', error);
        showToast('Failed to save category', 'danger');
    })
    .finally(() => {
        hideLoading(document.getElementById('saveButton'));
    });
}

function editCategory(id) {
    const category = categories.find(c => c.id === id);
    if (!category) return;

    editingCategoryId = id;
    document.getElementById('modalTitle').textContent = 'Edit Category';
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = category.name;
    document.getElementById('categoryDescription').value = category.description || '';
    document.getElementById('categoryParent').value = category.parent_id || '';
    document.getElementById('categoryActive').checked = category.is_active;

    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    modal.show();
}

function toggleStatus(id) {
    fetch(`/categories/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Category status updated successfully');
            loadCategories(currentPage);
        } else {
            showToast(data.message || 'Failed to update status', 'danger');
        }
    })
    .catch(error => {
        console.error('Error toggling status:', error);
        showToast('Failed to update status', 'danger');
    });
}

function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        return;
    }

    fetch(`/categories/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Category deleted successfully');
            loadCategories(currentPage);
            loadParentOptions();
        } else {
            showToast(data.message || 'Failed to delete category', 'danger');
        }
    })
    .catch(error => {
        console.error('Error deleting category:', error);
        showToast('Failed to delete category', 'danger');
    });
}

function toggleHierarchy() {
    isHierarchyView = !isHierarchyView;
    const icon = document.getElementById('hierarchyIcon');
    const text = document.getElementById('hierarchyText');

    if (isHierarchyView) {
        icon.className = 'bi bi-list-ul';
        text.textContent = 'List View';
    } else {
        icon.className = 'bi bi-diagram-3';
        text.textContent = 'Tree View';
    }

    loadCategories(currentPage);
}

function applyFilters() {
    currentPage = 1;
    loadCategories(1);
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterSelect').value = '';
    document.getElementById('sortSelect').value = 'name';
    applyFilters();
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    applyFilters();
}

function resetForm() {
    editingCategoryId = null;
    document.getElementById('modalTitle').textContent = 'Add Category';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryActive').checked = true;
    clearFormErrors();
}

function displayFormErrors(errors) {
    clearFormErrors();

    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = errors[field][0];
            }
        }
    });
}

function clearFormErrors() {
    document.querySelectorAll('.is-invalid').forEach(element => {
        element.classList.remove('is-invalid');
    });
    document.querySelectorAll('.invalid-feedback').forEach(element => {
        element.textContent = '';
    });
}

function bulkAction(action) {
    const selectedIds = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);

    if (selectedIds.length === 0) {
        showToast('Please select categories first', 'warning');
        return;
    }

    const actions = {
        activate: 'activate selected categories',
        deactivate: 'deactivate selected categories',
        delete: 'delete selected categories'
    };

    if (!confirm(`Are you sure you want to ${actions[action]}?`)) {
        return;
    }

    fetch(`/categories/bulk-${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Categories ${action}d successfully`);
            loadCategories(currentPage);
            document.getElementById('selectAll').checked = false;
        } else {
            showToast(data.message || `Failed to ${action} categories`, 'danger');
        }
    })
    .catch(error => {
        console.error(`Error ${action}ing categories:`, error);
        showToast(`Failed to ${action} categories`, 'danger');
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush
