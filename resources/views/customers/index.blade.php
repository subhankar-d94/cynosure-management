@extends('layouts.app')

@section('title', 'Customers')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Customers</li>
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
                    <h2 class="mb-1">Customers</h2>
                    <p class="text-muted mb-0">Manage your customer database and relationships</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="importCustomers()">
                        <i class="bi bi-upload me-1"></i>Import
                    </button>
                    <button class="btn btn-outline-secondary" onclick="exportCustomers()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Add Customer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-primary text-uppercase mb-1">Total Customers</h6>
                            <h3 class="mb-0 text-primary" id="totalCustomers">-</h3>
                            <small class="text-muted">Active customers</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-success text-uppercase mb-1">New This Month</h6>
                            <h3 class="mb-0 text-success" id="newCustomers">-</h3>
                            <small class="text-muted">Recent additions</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-person-plus" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-info text-uppercase mb-1">Active Orders</h6>
                            <h3 class="mb-0 text-info" id="activeOrders">-</h3>
                            <small class="text-muted">In progress</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-cart-check" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-warning text-uppercase mb-1">Total Revenue</h6>
                            <h3 class="mb-0 text-warning" id="totalRevenue">-</h3>
                            <small class="text-muted">All time</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-currency-rupee" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
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
                            <input type="text" class="form-control" id="search" placeholder="Search customers...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Customer Type</label>
                            <select class="form-select" id="customer_type">
                                <option value="">All Types</option>
                                <option value="individual">Individual</option>
                                <option value="business">Business</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" id="sort_by">
                                <option value="name">Name</option>
                                <option value="created_at">Date Added</option>
                                <option value="total_orders">Total Orders</option>
                                <option value="total_spent">Total Spent</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Order</label>
                            <select class="form-select" id="sort_order">
                                <option value="asc">Ascending</option>
                                <option value="desc">Descending</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Customer List</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" style="display: none;">
                            <i class="bi bi-trash"></i> Delete Selected
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshTable()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Type</th>
                                    <th>Total Orders</th>
                                    <th>Total Spent</th>
                                    <th>Last Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> customers
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
                <h5 class="modal-title">Import Customers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">CSV File</label>
                        <input type="file" class="form-control" name="file" accept=".csv" required>
                        <small class="text-muted">Upload a CSV file with customer data</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <input type="checkbox" name="update_existing" class="form-check-input me-1">
                            Update existing customers
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected customer(s)? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentPage = 1;
let perPage = 15;
let selectedCustomers = [];

$(document).ready(function() {
    loadStats();
    loadCustomers();

    // Filter change handlers
    $('#search, #status, #customer_type, #sort_by, #sort_order').on('change input', function() {
        currentPage = 1;
        loadCustomers();
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.customer-checkbox').prop('checked', isChecked);
        updateSelectedCustomers();
    });

    // Individual checkbox change
    $(document).on('change', '.customer-checkbox', function() {
        updateSelectedCustomers();
    });

    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        if (selectedCustomers.length > 0) {
            $('#deleteModal').modal('show');
        }
    });

    // Confirm delete
    $('#confirmDeleteBtn').on('click', function() {
        deleteSelectedCustomers();
    });

    // Import form
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        submitImport();
    });
});

function loadStats() {
    $.get('{{ route("customers.data") }}?stats=1')
        .done(function(response) {
            if (response.success) {
                $('#totalCustomers').text(response.stats.total_customers || 0);
                $('#newCustomers').text(response.stats.new_customers || 0);
                $('#activeOrders').text(response.stats.active_orders || 0);
                $('#totalRevenue').text('₹' + (response.stats.total_revenue || 0).toLocaleString());
            }
        })
        .fail(function() {
            // Set default values on error
            $('#totalCustomers').text('0');
            $('#newCustomers').text('0');
            $('#activeOrders').text('0');
            $('#totalRevenue').text('₹0');
        });
}

function getFilters() {
    return {
        search: $('#search').val(),
        status: $('#status').val(),
        customer_type: $('#customer_type').val(),
        sort_by: $('#sort_by').val(),
        sort_order: $('#sort_order').val(),
        page: currentPage,
        per_page: perPage
    };
}

function loadCustomers() {
    const filters = getFilters();

    $.get('{{ route("customers.data") }}', filters)
        .done(function(response) {
            if (response.success) {
                renderCustomersTable(response.data.data || response.data);
                if (response.data.links) {
                    renderPagination(response.data);
                    updatePaginationInfo(response.data);
                }
            }
        })
        .fail(function() {
            showAlert('Error loading customers', 'danger');
        });
}

function renderCustomersTable(customers) {
    let html = '';

    if (customers.length === 0) {
        html = '<tr><td colspan="9" class="text-center py-4">No customers found</td></tr>';
    } else {
        customers.forEach(function(customer) {
            const statusBadge = customer.status === 'active' ? 'bg-success' : 'bg-secondary';
            const typeBadge = customer.customer_type === 'business' ? 'bg-primary' : 'bg-info';

            html += `
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input customer-checkbox" value="${customer.id}">
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                ${customer.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <h6 class="mb-0">${customer.name}</h6>
                                <small class="text-muted">${customer.customer_code || 'N/A'}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <small class="d-block">${customer.email || 'No email'}</small>
                            <small class="text-muted">${customer.phone || 'No phone'}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${typeBadge}">${customer.customer_type === 'business' ? 'Business' : 'Individual'}</span>
                    </td>
                    <td>
                        <span class="fw-bold">${customer.total_orders || 0}</span>
                        <small class="d-block text-muted">orders</small>
                    </td>
                    <td>
                        <span class="fw-bold text-success">₹${(customer.total_spent || 0).toLocaleString()}</span>
                    </td>
                    <td>
                        <small>${customer.last_order_date ? new Date(customer.last_order_date).toLocaleDateString() : 'Never'}</small>
                    </td>
                    <td>
                        <span class="badge ${statusBadge}">${customer.status === 'active' ? 'Active' : 'Inactive'}</span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('customers.show', '') }}/${customer.id}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('customers.edit', '') }}/${customer.id}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteCustomer(${customer.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#customersTableBody').html(html);
}

function updateSelectedCustomers() {
    selectedCustomers = [];
    $('.customer-checkbox:checked').each(function() {
        selectedCustomers.push($(this).val());
    });

    if (selectedCustomers.length > 0) {
        $('#bulkDeleteBtn').show();
    } else {
        $('#bulkDeleteBtn').hide();
    }

    // Update select all checkbox
    const totalCheckboxes = $('.customer-checkbox').length;
    const checkedCheckboxes = $('.customer-checkbox:checked').length;

    if (checkedCheckboxes === 0) {
        $('#selectAll').prop('indeterminate', false).prop('checked', false);
    } else if (checkedCheckboxes === totalCheckboxes) {
        $('#selectAll').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#selectAll').prop('indeterminate', true);
    }
}

function renderPagination(data) {
    $('#pagination').html(data.links || '');
}

function updatePaginationInfo(data) {
    $('#showingFrom').text(data.from || 0);
    $('#showingTo').text(data.to || 0);
    $('#totalRecords').text(data.total || 0);
}

function refreshTable() {
    loadCustomers();
    loadStats();
}

function deleteCustomer(customerId) {
    selectedCustomers = [customerId];
    $('#deleteModal').modal('show');
}

function deleteSelectedCustomers() {
    if (selectedCustomers.length === 0) return;

    const deletePromises = selectedCustomers.map(id => {
        return $.ajax({
            url: `{{ route('customers.destroy', '') }}/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    Promise.all(deletePromises)
        .then(() => {
            showAlert('Customer(s) deleted successfully', 'success');
            $('#deleteModal').modal('hide');
            selectedCustomers = [];
            $('#bulkDeleteBtn').hide();
            $('#selectAll').prop('checked', false);
            loadCustomers();
            loadStats();
        })
        .catch(() => {
            showAlert('Error deleting customer(s)', 'danger');
        });
}

function importCustomers() {
    $('#importModal').modal('show');
}

function submitImport() {
    const formData = new FormData($('#importForm')[0]);

    $.ajax({
        url: '{{ route("customers.import") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Customers imported successfully', 'success');
            $('#importModal').modal('hide');
            loadCustomers();
            loadStats();
        } else {
            showAlert(response.message || 'Import failed', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error importing customers', 'danger');
    });
}

function exportCustomers() {
    const filters = getFilters();
    const params = new URLSearchParams(filters);
    window.location.href = `{{ route('customers.export') }}?${params}`;
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