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
                            <h5 class="card-title mb-1">Total Invoices</h5>
                            <h3 class="mb-0" id="totalInvoices">{{ $stats['total_invoices'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-file-invoice fa-2x"></i>
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
                            <h3 class="mb-0" id="pendingInvoices">{{ $stats['pending_invoices'] ?? 0 }}</h3>
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
                            <h5 class="card-title mb-1">Paid Invoices</h5>
                            <h3 class="mb-0" id="paidInvoices">{{ $stats['paid_invoices'] ?? 0 }}</h3>
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
                            <h5 class="card-title mb-1">Monthly Revenue</h5>
                            <h3 class="mb-0" id="monthlyRevenue">${{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Overdue</h5>
                            <h3 class="mb-0" id="overdueInvoices">{{ $stats['overdue_invoices'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <h5 class="card-title mb-1">Draft</h5>
                            <h3 class="mb-0" id="draftInvoices">{{ $stats['draft_invoices'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-edit fa-2x"></i>
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
                            <h5 class="card-title mb-1">Outstanding</h5>
                            <h3 class="mb-0" id="outstandingAmount">${{ number_format($stats['outstanding_amount'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h5 class="card-title mb-1">This Month</h5>
                            <h3 class="mb-0" id="monthlyInvoices">{{ $stats['monthly_invoices'] ?? 0 }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Invoice Management Card -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="card-title mb-0">Invoice Management</h3>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Invoice
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportInvoices('csv')">Export as CSV</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportInvoices('pdf')">Export as PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportInvoices('excel')">Export as Excel</a></li>
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
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                        <option value="viewed">Viewed</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFromFilter" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="dateFromFilter">
                </div>
                <div class="col-md-3">
                    <label for="dateToFilter" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="dateToFilter">
                </div>
                <div class="col-md-3">
                    <label for="searchFilter" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchFilter" placeholder="Invoice #, customer...">
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
                        <span class="text-muted">Selected: <span id="selectedCount">0</span> invoices</span>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Bulk Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('send')">
                                    <i class="fas fa-paper-plane"></i> Send Invoices
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('mark_paid')">
                                    <i class="fas fa-check"></i> Mark as Paid
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('download')">
                                    <i class="fas fa-download"></i> Download PDFs
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                                    <i class="fas fa-trash"></i> Delete Invoices
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="invoicesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>
                                <a href="#" class="text-decoration-none text-dark" onclick="sortTable('invoice_number')">
                                    Invoice # <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>Customer</th>
                            <th>
                                <a href="#" class="text-decoration-none text-dark" onclick="sortTable('issue_date')">
                                    Issue Date <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>Due Date</th>
                            <th>
                                <a href="#" class="text-decoration-none text-dark" onclick="sortTable('total')">
                                    Amount <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTableBody">
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
            <nav aria-label="Invoice pagination" class="mt-3">
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
                <h5 class="modal-title">Invoice Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <!-- Quick view content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="openInvoiceDetails()">View Full Details</button>
            </div>
        </div>
    </div>
</div>

<!-- Send Invoice Modal -->
<div class="modal fade" id="sendInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sendInvoiceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipientEmail" class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" id="recipientEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="emailSubject" value="Invoice from Your Company">
                    </div>
                    <div class="mb-3">
                        <label for="emailMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="emailMessage" rows="4">Please find attached your invoice. Payment is due within 30 days.</textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="attachPDF" checked>
                        <label class="form-check-label" for="attachPDF">
                            Attach PDF copy
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Invoice
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
    let selectedInvoices = [];
    let currentInvoiceId = null;

    // Load invoices on page load
    loadInvoices();

    // Filter change handlers
    $('#statusFilter, #dateFromFilter, #dateToFilter').change(function() {
        currentPage = 1;
        loadInvoices();
    });

    // Search with debounce
    let searchTimeout;
    $('#searchFilter').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            loadInvoices();
        }, 500);
    });

    // Select all checkbox
    $('#selectAll').change(function() {
        const isChecked = $(this).is(':checked');
        $('.invoice-checkbox').prop('checked', isChecked);
        updateSelectedInvoices();
    });

    // Individual invoice checkbox
    $(document).on('change', '.invoice-checkbox', function() {
        updateSelectedInvoices();
    });

    // Load invoices function
    function loadInvoices() {
        $('#loadingIndicator').show();
        $('#invoicesTableBody').hide();

        const filters = {
            page: currentPage,
            status: $('#statusFilter').val(),
            date_from: $('#dateFromFilter').val(),
            date_to: $('#dateToFilter').val(),
            search: $('#searchFilter').val(),
            sort: sortField,
            direction: sortDirection
        };

        $.ajax({
            url: '{{ route("invoices.index") }}',
            method: 'GET',
            data: filters,
            success: function(response) {
                renderInvoicesTable(response.invoices);
                renderPagination(response);
                updateStatistics();
                $('#loadingIndicator').hide();
                $('#invoicesTableBody').show();
            },
            error: function(xhr) {
                console.error('Error loading invoices:', xhr);
                $('#loadingIndicator').hide();
                alert('Error loading invoices. Please try again.');
            }
        });
    }

    // Render invoices table
    function renderInvoicesTable(invoices) {
        let html = '';

        if (invoices.length === 0) {
            html = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-file-invoice fa-3x mb-3"></i>
                            <p>No invoices found matching your criteria.</p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Invoice
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            invoices.forEach(function(invoice) {
                const statusBadge = getStatusBadge(invoice.status);
                const dueDate = invoice.due_date ? new Date(invoice.due_date).toLocaleDateString() : 'N/A';
                const issueDate = new Date(invoice.issue_date).toLocaleDateString();
                const customerName = invoice.customer_name || invoice.walk_in_name || 'Walk-in Customer';

                html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input invoice-checkbox" value="${invoice.id}">
                        </td>
                        <td>
                            <a href="{{ route('invoices.show', '') }}/${invoice.id}" class="text-decoration-none">
                                #${invoice.invoice_number}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    ${customerName.substring(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <div class="fw-medium">${customerName}</div>
                                    <small class="text-muted">${invoice.customer_email || ''}</small>
                                </div>
                            </div>
                        </td>
                        <td>${issueDate}</td>
                        <td>
                            ${invoice.status === 'overdue' ?
                                `<span class="text-danger fw-bold">${dueDate}</span>` :
                                dueDate
                            }
                        </td>
                        <td>
                            <span class="fw-bold">$${parseFloat(invoice.total).toFixed(2)}</span>
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="quickView(${invoice.id})">
                                        <i class="fas fa-eye"></i> Quick View
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('invoices.show', '') }}/${invoice.id}">
                                        <i class="fas fa-file-alt"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('invoices.edit', '') }}/${invoice.id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="sendInvoice(${invoice.id})">
                                        <i class="fas fa-paper-plane"></i> Send
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('invoices.download', '') }}/${invoice.id}">
                                        <i class="fas fa-download"></i> Download PDF
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('invoices.print', '') }}/${invoice.id}" target="_blank">
                                        <i class="fas fa-print"></i> Print
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    ${invoice.status !== 'paid' ?
                                        `<li><a class="dropdown-item" href="#" onclick="markAsPaid(${invoice.id})">
                                            <i class="fas fa-check"></i> Mark as Paid
                                        </a></li>` : ''
                                    }
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteInvoice(${invoice.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        $('#invoicesTableBody').html(html);
    }

    // Get status badge HTML
    function getStatusBadge(status) {
        const badges = {
            'draft': '<span class="badge bg-secondary">Draft</span>',
            'sent': '<span class="badge bg-primary">Sent</span>',
            'viewed': '<span class="badge bg-info">Viewed</span>',
            'paid': '<span class="badge bg-success">Paid</span>',
            'overdue': '<span class="badge bg-danger">Overdue</span>',
            'cancelled': '<span class="badge bg-dark">Cancelled</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
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

    // Update selected invoices
    function updateSelectedInvoices() {
        selectedInvoices = [];
        $('.invoice-checkbox:checked').each(function() {
            selectedInvoices.push($(this).val());
        });

        if (selectedInvoices.length > 0) {
            $('#bulkActions').show();
            $('#selectedCount').text(selectedInvoices.length);
        } else {
            $('#bulkActions').hide();
        }

        // Update select all checkbox
        const totalCheckboxes = $('.invoice-checkbox').length;
        const checkedCheckboxes = $('.invoice-checkbox:checked').length;
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
    }

    // Update statistics
    function updateStatistics() {
        $.get('{{ route("invoices.index") }}?stats=1', function(response) {
            if (response.stats) {
                $('#totalInvoices').text(response.stats.total_invoices);
                $('#pendingInvoices').text(response.stats.pending_invoices);
                $('#paidInvoices').text(response.stats.paid_invoices);
                $('#monthlyRevenue').text('$' + parseFloat(response.stats.monthly_revenue).toFixed(2));
                $('#overdueInvoices').text(response.stats.overdue_invoices);
                $('#draftInvoices').text(response.stats.draft_invoices);
                $('#outstandingAmount').text('$' + parseFloat(response.stats.outstanding_amount).toFixed(2));
                $('#monthlyInvoices').text(response.stats.monthly_invoices);
            }
        });
    }

    // Global functions
    window.changePage = function(page) {
        if (page < 1) return;
        currentPage = page;
        loadInvoices();
    };

    window.sortTable = function(field) {
        if (sortField === field) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortField = field;
            sortDirection = 'asc';
        }
        currentPage = 1;
        loadInvoices();
    };

    window.clearFilters = function() {
        $('#statusFilter').val('');
        $('#dateFromFilter').val('');
        $('#dateToFilter').val('');
        $('#searchFilter').val('');
        currentPage = 1;
        loadInvoices();
    };

    window.quickView = function(invoiceId) {
        currentInvoiceId = invoiceId;
        $.get(`{{ route('invoices.show', '') }}/${invoiceId}`, function(response) {
            $('#quickViewContent').html(response);
            $('#quickViewModal').modal('show');
        });
    };

    window.openInvoiceDetails = function() {
        if (currentInvoiceId) {
            window.location.href = `{{ route('invoices.show', '') }}/${currentInvoiceId}`;
        }
    };

    window.sendInvoice = function(invoiceId) {
        currentInvoiceId = invoiceId;
        // Pre-fill customer email if available
        $.get(`{{ route('invoices.show', '') }}/${invoiceId}?format=json`, function(response) {
            if (response.customer_email) {
                $('#recipientEmail').val(response.customer_email);
            }
            $('#sendInvoiceModal').modal('show');
        });
    };

    window.markAsPaid = function(invoiceId) {
        if (confirm('Mark this invoice as paid?')) {
            $.post(`{{ route('invoices.mark-paid', '') }}/${invoiceId}`, {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                loadInvoices();
                alert('Invoice marked as paid successfully!');
            });
        }
    };

    window.deleteInvoice = function(invoiceId) {
        if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
            $.ajax({
                url: `{{ route('invoices.destroy', '') }}/${invoiceId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    loadInvoices();
                    alert('Invoice deleted successfully!');
                }
            });
        }
    };

    window.bulkAction = function(action) {
        if (selectedInvoices.length === 0) {
            alert('Please select invoices first.');
            return;
        }

        let confirmMessage = '';
        switch (action) {
            case 'send':
                confirmMessage = `Send ${selectedInvoices.length} invoice(s)?`;
                break;
            case 'mark_paid':
                confirmMessage = `Mark ${selectedInvoices.length} invoice(s) as paid?`;
                break;
            case 'delete':
                confirmMessage = `Delete ${selectedInvoices.length} invoice(s)? This cannot be undone.`;
                break;
            case 'download':
                // Direct download, no confirmation needed
                break;
        }

        if (confirmMessage && !confirm(confirmMessage)) {
            return;
        }

        // Perform bulk action
        $.post('{{ route("invoices.bulk-send") }}', {
            action: action,
            invoice_ids: selectedInvoices,
            _token: '{{ csrf_token() }}'
        }, function(response) {
            loadInvoices();
            selectedInvoices = [];
            updateSelectedInvoices();
            alert(response.message || 'Bulk action completed successfully!');
        });
    };

    window.exportInvoices = function(format) {
        const filters = {
            status: $('#statusFilter').val(),
            date_from: $('#dateFromFilter').val(),
            date_to: $('#dateToFilter').val(),
            search: $('#searchFilter').val(),
            format: format
        };

        const queryString = $.param(filters);
        window.open(`{{ route('invoices.index') }}?export=1&${queryString}`, '_blank');
    };

    // Send invoice form submission
    $('#sendInvoiceForm').on('submit', function(e) {
        e.preventDefault();

        $.post(`{{ route('invoices.send', '') }}/${currentInvoiceId}`, {
            email: $('#recipientEmail').val(),
            subject: $('#emailSubject').val(),
            message: $('#emailMessage').val(),
            attach_pdf: $('#attachPDF').is(':checked'),
            _token: '{{ csrf_token() }}'
        }, function(response) {
            $('#sendInvoiceModal').modal('hide');
            loadInvoices();
            alert('Invoice sent successfully!');
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
</style>
@endpush