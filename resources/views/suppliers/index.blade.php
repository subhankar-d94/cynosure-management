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
                            <h3 class="mb-0" id="totalSuppliers">{{ $stats['total_suppliers'] ?? 0 }}</h3>
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
                            <h5 class="card-title mb-1">Active Suppliers</h5>
                            <h3 class="mb-0" id="activeSuppliers">{{ $stats['active_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h5 class="card-title mb-1">Total Purchases</h5>
                            <h3 class="mb-0" id="totalPurchases">${{ number_format($stats['total_purchases'] ?? 0, 2) }}</h3>
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
                            <h5 class="card-title mb-1">This Month</h5>
                            <h3 class="mb-0" id="monthlyPurchases">${{ number_format($stats['monthly_purchases'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Top Rated</h5>
                            <h3 class="mb-0" id="topRatedSuppliers">{{ $stats['top_rated_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Materials</h5>
                            <h3 class="mb-0" id="totalMaterials">{{ $stats['total_materials'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Avg Rating</h5>
                            <h3 class="mb-0" id="avgRating">{{ number_format($stats['avg_rating'] ?? 0, 1) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Overdue</h5>
                            <h3 class="mb-0" id="overdueSuppliers">{{ $stats['overdue_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                        <a href="{{ route('suppliers.performance') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Performance
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportSuppliers('csv')">Export as CSV</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportSuppliers('excel')">Export as Excel</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportSuppliers('pdf')">Export as PDF</a></li>
                            </ul>
                        </div>
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
                        <option value="blocked">Blocked</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="categoryFilter" class="form-label">Category</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="raw_materials">Raw Materials</option>
                        <option value="components">Components</option>
                        <option value="packaging">Packaging</option>
                        <option value="services">Services</option>
                        <option value="equipment">Equipment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ratingFilter" class="form-label">Min Rating</label>
                    <select class="form-select" id="ratingFilter">
                        <option value="">All Ratings</option>
                        <option value="4">4+ Stars</option>
                        <option value="3">3+ Stars</option>
                        <option value="2">2+ Stars</option>
                        <option value="1">1+ Stars</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchFilter" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchFilter" placeholder="Name, email, phone...">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearFilters()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2" id="bulkActions" style="display: none !important;">
                        <span class="text-muted">Selected: <span id="selectedCount">0</span> suppliers</span>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Bulk Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('activate')">
                                    <i class="fas fa-check"></i> Activate
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">
                                    <i class="fas fa-pause"></i> Deactivate
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('export')">
                                    <i class="fas fa-download"></i> Export Selected
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                                    <i class="fas fa-trash"></i> Delete Suppliers
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="suppliersTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>
                                <a href="#" class="text-decoration-none text-dark" onclick="sortTable('company_name')">
                                    Company <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>Contact Person</th>
                            <th>Category</th>
                            <th>
                                <a href="#" class="text-decoration-none text-dark" onclick="sortTable('rating')">
                                    Rating <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="#" class="text-decoration-none text-dark" onclick="sortTable('total_purchases')">
                                    Total Purchases <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="suppliersTableBody">
                        <!-- Table content will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Loading indicator -->
            <div class="text-center py-4" id="loadingIndicator">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Supplier pagination" class="mt-3">
                <ul class="pagination justify-content-center" id="pagination">
                    <!-- Pagination will be generated dynamically -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <!-- Quick view content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="openSupplierDetails()">View Full Details</button>
            </div>
        </div>
    </div>
</div>

<!-- Rate Supplier Modal -->
<div class="modal fade" id="rateSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rate Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rateSupplierForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1-5 stars)</label>
                        <div class="rating-input">
                            <input type="radio" id="star5" name="rating" value="5">
                            <label for="star5" class="star"></label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" class="star"></label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" class="star"></label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" class="star"></label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" class="star"></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ratingComment" class="form-label">Comment (optional)</label>
                        <textarea class="form-control" id="ratingComment" name="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-star"></i> Submit Rating
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let sortField = 'created_at';
    let sortDirection = 'desc';
    let selectedSuppliers = [];
    let currentSupplierId = null;

    // Load suppliers on page load
    loadSuppliers();

    // Filter change handlers
    $('#statusFilter, #categoryFilter, #ratingFilter').change(function() {
        currentPage = 1;
        loadSuppliers();
    });

    // Search with debounce
    let searchTimeout;
    $('#searchFilter').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            loadSuppliers();
        }, 500);
    });

    // Select all checkbox
    $('#selectAll').change(function() {
        const isChecked = $(this).is(':checked');
        $('.supplier-checkbox').prop('checked', isChecked);
        updateSelectedSuppliers();
    });

    // Individual supplier checkbox
    $(document).on('change', '.supplier-checkbox', function() {
        updateSelectedSuppliers();
    });

    // Load suppliers function
    function loadSuppliers() {
        $('#loadingIndicator').show();
        $('#suppliersTableBody').hide();

        const filters = {
            page: currentPage,
            status: $('#statusFilter').val(),
            category: $('#categoryFilter').val(),
            rating: $('#ratingFilter').val(),
            search: $('#searchFilter').val(),
            sort: sortField,
            direction: sortDirection
        };

        $.ajax({
            url: '{{ route("suppliers.index") }}',
            method: 'GET',
            data: filters,
            success: function(response) {
                renderSuppliersTable(response.suppliers);
                renderPagination(response);
                updateStatistics();
                $('#loadingIndicator').hide();
                $('#suppliersTableBody').show();
            },
            error: function(xhr) {
                console.error('Error loading suppliers:', xhr);
                $('#loadingIndicator').hide();
                alert('Error loading suppliers. Please try again.');
            }
        });
    }

    // Render suppliers table
    function renderSuppliersTable(suppliers) {
        let html = '';

        if (suppliers.length === 0) {
            html = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-truck fa-3x mb-3"></i>
                            <p>No suppliers found matching your criteria.</p>
                            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Supplier
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            suppliers.forEach(function(supplier) {
                const statusBadge = getStatusBadge(supplier.status);
                const rating = getRatingDisplay(supplier.rating);
                const categoryBadge = getCategoryBadge(supplier.category);

                html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input supplier-checkbox" value="${supplier.id}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    ${supplier.company_name.substring(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <a href="{{ route('suppliers.show', '') }}/${supplier.id}" class="text-decoration-none">
                                        <strong>${supplier.company_name}</strong>
                                    </a>
                                    <br><small class="text-muted">${supplier.email || ''}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>${supplier.contact_person || 'N/A'}</strong>
                                <br><small class="text-muted">${supplier.phone || ''}</small>
                            </div>
                        </td>
                        <td>${categoryBadge}</td>
                        <td>${rating}</td>
                        <td>
                            <span class="fw-bold">$${parseFloat(supplier.total_purchases || 0).toFixed(2)}</span>
                            <br><small class="text-muted">${supplier.purchase_count || 0} orders</small>
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="quickView(${supplier.id})">
                                        <i class="fas fa-eye"></i> Quick View
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('suppliers.show', '') }}/${supplier.id}">
                                        <i class="fas fa-file-alt"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('suppliers.edit', '') }}/${supplier.id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('suppliers.purchases', '') }}/${supplier.id}">
                                        <i class="fas fa-shopping-cart"></i> View Purchases
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('suppliers.materials', '') }}/${supplier.id}">
                                        <i class="fas fa-boxes"></i> Materials
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="rateSupplier(${supplier.id})">
                                        <i class="fas fa-star"></i> Rate Supplier
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteSupplier(${supplier.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        $('#suppliersTableBody').html(html);
    }

    // Get status badge HTML
    function getStatusBadge(status) {
        const badges = {
            'active': '<span class="badge bg-success">Active</span>',
            'inactive': '<span class="badge bg-secondary">Inactive</span>',
            'pending': '<span class="badge bg-warning">Pending</span>',
            'blocked': '<span class="badge bg-danger">Blocked</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
    }

    // Get category badge HTML
    function getCategoryBadge(category) {
        const badges = {
            'raw_materials': '<span class="badge bg-primary">Raw Materials</span>',
            'components': '<span class="badge bg-info">Components</span>',
            'packaging': '<span class="badge bg-warning">Packaging</span>',
            'services': '<span class="badge bg-success">Services</span>',
            'equipment': '<span class="badge bg-dark">Equipment</span>'
        };
        return badges[category] || '<span class="badge bg-secondary">Other</span>';
    }

    // Get rating display HTML
    function getRatingDisplay(rating) {
        if (!rating) return '<span class="text-muted">No rating</span>';

        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="fas fa-star text-warning"></i>';
            } else {
                stars += '<i class="far fa-star text-muted"></i>';
            }
        }
        return `${stars} <small>(${rating})</small>`;
    }

    // Render pagination
    function renderPagination(response) {
        let html = '';
        const totalPages = response.last_page;
        const currentPage = response.current_page;

        if (totalPages > 1) {
            // Previous button
            html += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
                </li>
            `;

            // Page numbers
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                html += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `;
            }

            // Next button
            html += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
                </li>
            `;
        }

        $('#pagination').html(html);
    }

    // Update selected suppliers
    function updateSelectedSuppliers() {
        selectedSuppliers = [];
        $('.supplier-checkbox:checked').each(function() {
            selectedSuppliers.push($(this).val());
        });

        if (selectedSuppliers.length > 0) {
            $('#bulkActions').show();
            $('#selectedCount').text(selectedSuppliers.length);
        } else {
            $('#bulkActions').hide();
        }

        // Update select all checkbox
        const totalCheckboxes = $('.supplier-checkbox').length;
        const checkedCheckboxes = $('.supplier-checkbox:checked').length;
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
    }

    // Update statistics
    function updateStatistics() {
        $.get('{{ route("suppliers.index") }}?stats=1', function(response) {
            if (response.stats) {
                $('#totalSuppliers').text(response.stats.total_suppliers);
                $('#activeSuppliers').text(response.stats.active_suppliers);
                $('#totalPurchases').text('$' + parseFloat(response.stats.total_purchases).toFixed(2));
                $('#monthlyPurchases').text('$' + parseFloat(response.stats.monthly_purchases).toFixed(2));
                $('#topRatedSuppliers').text(response.stats.top_rated_suppliers);
                $('#totalMaterials').text(response.stats.total_materials);
                $('#avgRating').text(parseFloat(response.stats.avg_rating).toFixed(1));
                $('#overdueSuppliers').text(response.stats.overdue_suppliers);
            }
        });
    }

    // Global functions
    window.changePage = function(page) {
        if (page < 1) return;
        currentPage = page;
        loadSuppliers();
    };

    window.sortTable = function(field) {
        if (sortField === field) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortField = field;
            sortDirection = 'asc';
        }
        currentPage = 1;
        loadSuppliers();
    };

    window.clearFilters = function() {
        $('#statusFilter').val('');
        $('#categoryFilter').val('');
        $('#ratingFilter').val('');
        $('#searchFilter').val('');
        currentPage = 1;
        loadSuppliers();
    };

    window.quickView = function(supplierId) {
        currentSupplierId = supplierId;
        $.get(`{{ route('suppliers.show', '') }}/${supplierId}?format=json`, function(response) {
            $('#quickViewContent').html(generateQuickViewContent(response));
            $('#quickViewModal').modal('show');
        });
    };

    window.openSupplierDetails = function() {
        if (currentSupplierId) {
            window.location.href = `{{ route('suppliers.show', '') }}/${currentSupplierId}`;
        }
    };

    window.rateSupplier = function(supplierId) {
        currentSupplierId = supplierId;
        $('#rateSupplierModal').modal('show');
    };

    window.deleteSupplier = function(supplierId) {
        if (confirm('Are you sure you want to delete this supplier? This action cannot be undone.')) {
            $.ajax({
                url: `{{ route('suppliers.destroy', '') }}/${supplierId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    loadSuppliers();
                    alert('Supplier deleted successfully!');
                }
            });
        }
    };

    window.bulkAction = function(action) {
        if (selectedSuppliers.length === 0) {
            alert('Please select suppliers first.');
            return;
        }

        let confirmMessage = '';
        switch (action) {
            case 'activate':
                confirmMessage = `Activate ${selectedSuppliers.length} supplier(s)?`;
                break;
            case 'deactivate':
                confirmMessage = `Deactivate ${selectedSuppliers.length} supplier(s)?`;
                break;
            case 'delete':
                confirmMessage = `Delete ${selectedSuppliers.length} supplier(s)? This cannot be undone.`;
                break;
            case 'export':
                // Direct export, no confirmation needed
                break;
        }

        if (confirmMessage && !confirm(confirmMessage)) {
            return;
        }

        // Perform bulk action
        $.post('{{ route("suppliers.index") }}', {
            action: action,
            supplier_ids: selectedSuppliers,
            _token: '{{ csrf_token() }}'
        }, function(response) {
            loadSuppliers();
            selectedSuppliers = [];
            updateSelectedSuppliers();
            alert(response.message || 'Bulk action completed successfully!');
        });
    };

    window.exportSuppliers = function(format) {
        const filters = {
            status: $('#statusFilter').val(),
            category: $('#categoryFilter').val(),
            rating: $('#ratingFilter').val(),
            search: $('#searchFilter').val(),
            format: format
        };

        const queryString = $.param(filters);
        window.open(`{{ route('suppliers.index') }}?export=1&${queryString}`, '_blank');
    };

    // Generate quick view content
    function generateQuickViewContent(supplier) {
        return `
            <div class="row">
                <div class="col-md-6">
                    <h6>Company Information</h6>
                    <p><strong>Company:</strong> ${supplier.company_name}</p>
                    <p><strong>Contact Person:</strong> ${supplier.contact_person || 'N/A'}</p>
                    <p><strong>Email:</strong> ${supplier.email || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${supplier.phone || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                    <h6>Business Details</h6>
                    <p><strong>Category:</strong> ${supplier.category || 'N/A'}</p>
                    <p><strong>Status:</strong> ${supplier.status || 'N/A'}</p>
                    <p><strong>Rating:</strong> ${supplier.rating ? supplier.rating + '/5' : 'No rating'}</p>
                    <p><strong>Total Purchases:</strong> $${parseFloat(supplier.total_purchases || 0).toFixed(2)}</p>
                </div>
            </div>
        `;
    }

    // Rate supplier form submission
    $('#rateSupplierForm').on('submit', function(e) {
        e.preventDefault();

        $.post(`{{ route('suppliers.rate', '') }}/${currentSupplierId}`, {
            rating: $('input[name="rating"]:checked').val(),
            comment: $('#ratingComment').val(),
            _token: '{{ csrf_token() }}'
        }, function(response) {
            $('#rateSupplierModal').modal('hide');
            loadSuppliers();
            alert('Rating submitted successfully!');
        });
    });
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

.table th a {
    color: inherit;
}

.table th a:hover {
    color: #0d6efd;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input .star {
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: block;
    color: #ddd;
    font-size: 24px;
    line-height: 30px;
    text-align: center;
    transition: color 0.2s;
}

.rating-input .star:hover,
.rating-input .star:hover ~ .star,
.rating-input input[type="radio"]:checked ~ .star {
    color: #ffc107;
}
</style>
@endpush