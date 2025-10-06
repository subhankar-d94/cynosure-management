@extends('layouts.app')

@section('title', 'Purchase Orders')

@push('styles')
<style>
    .purchases-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        border-left: 4px solid #17a2b8;
        margin-bottom: 20px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-trend {
        margin-top: 10px;
        font-size: 0.85rem;
    }

    .trend-up { color: #28a745; }
    .trend-down { color: #dc3545; }

    .filter-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .purchases-table {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .table-header {
        background: #f8f9fa;
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
    }

    .table th {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 15px;
        font-weight: 600;
    }

    .table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-draft { background: #fff3cd; color: #856404; }
    .status-pending { background: #cce7ff; color: #004085; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-ordered { background: #d1ecf1; color: #0c5460; }
    .status-received { background: #c3e6cb; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-paid { background: #d4edda; color: #155724; }

    .priority-high { color: #dc3545; }
    .priority-medium { color: #ffc107; }
    .priority-low { color: #28a745; }

    .btn-create {
        background: #17a2b8;
        border-color: #17a2b8;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
    }

    .btn-create:hover {
        background: #138496;
        border-color: #138496;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4);
    }

    .quick-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .purchases-header {
            padding: 20px;
            text-align: center;
        }

        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .quick-actions {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="purchases-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0">Purchase Orders</h1>
                <p class="mb-0 opacity-75">Manage purchase orders, supplier relationships, and procurement workflows</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('purchases.create') }}" class="btn btn-create">
                    <i class="fas fa-plus"></i> New Purchase Order
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-info">{{ $stats['total_purchases'] ?? '287' }}</div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i> +12% from last month
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-success">${{ number_format($stats['total_value'] ?? 1247650, 0) }}</div>
                <div class="stat-label">Total Value</div>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i> +8% from last month
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-warning">{{ $stats['pending_orders'] ?? '23' }}</div>
                <div class="stat-label">Pending Orders</div>
                <div class="stat-trend trend-down">
                    <i class="fas fa-arrow-down"></i> -3% from last month
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-danger">{{ $stats['overdue_orders'] ?? '7' }}</div>
                <div class="stat-label">Overdue Orders</div>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i> +2 from last week
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Statistics Row -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-primary">{{ $stats['monthly_orders'] ?? '45' }}</div>
                <div class="stat-label">This Month</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-secondary">{{ $stats['avg_delivery_days'] ?? '12' }} Days</div>
                <div class="stat-label">Avg Delivery</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-info">{{ $stats['active_suppliers'] ?? '89' }}</div>
                <div class="stat-label">Active Suppliers</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-success">${{ number_format($stats['cost_savings'] ?? 47250, 0) }}</div>
                <div class="stat-label">Cost Savings</div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-card">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" id="searchPurchases" placeholder="Search PO number, supplier...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="ordered">Ordered</option>
                    <option value="received">Received</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Supplier</label>
                <select class="form-select" id="supplierFilter">
                    <option value="">All Suppliers</option>
                    <option value="1">TechCorp Solutions</option>
                    <option value="2">Global Manufacturing</option>
                    <option value="3">Premium Services</option>
                    <option value="4">Industrial Supplies</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Priority</label>
                <select class="form-select" id="priorityFilter">
                    <option value="">All Priority</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date Range</label>
                <select class="form-select" id="dateFilter">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Table -->
    <div class="purchases-table">
        <div class="table-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Purchase Orders</h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportData()">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="bulkActions()">
                            <i class="fas fa-tasks"></i> Bulk Actions
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0" id="purchasesTable">
                <thead>
                    <tr>
                        <th width="5%">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Order Details</th>
                        <th>Supplier</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Dates</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="purchasesTableBody">
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input purchase-checkbox" value="PO-2024-001">
                        </td>
                        <td>
                            <div>
                                <strong>PO-2024-001</strong>
                                <br><small class="text-muted">Enterprise Software Licenses</small>
                                <div class="mt-1">
                                    <span class="badge bg-danger priority-high">High Priority</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>TechCorp Solutions</strong>
                                <br><small class="text-muted">john.anderson@techcorp.com</small>
                                <br><small class="text-muted">+1 (555) 123-4567</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>3 Items</strong>
                                <br><small class="text-muted">Software Licenses (3)</small>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                                <small class="text-muted">100% Complete</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>$24,500.00</strong>
                                <br><small class="text-success">Paid: $24,500.00</small>
                                <br><small class="text-muted">Balance: $0.00</small>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-paid">Paid</span>
                            <br><small class="text-muted">Completed successfully</small>
                        </td>
                        <td>
                            <div>
                                <strong>Ordered:</strong> Jan 15, 2024
                                <br><small class="text-muted">Expected: Jan 29, 2024</small>
                                <br><small class="text-success">Delivered: Jan 28, 2024</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('purchases.show', 'PO-2024-001') }}">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('purchases.print', 'PO-2024-001') }}">
                                        <i class="fas fa-print"></i> Print Order
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="duplicateOrder('PO-2024-001')">
                                        <i class="fas fa-copy"></i> Duplicate
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="downloadPdf('PO-2024-001')">
                                        <i class="fas fa-file-pdf"></i> Download PDF
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input purchase-checkbox" value="PO-2024-002">
                        </td>
                        <td>
                            <div>
                                <strong>PO-2024-002</strong>
                                <br><small class="text-muted">Raw Materials Procurement</small>
                                <div class="mt-1">
                                    <span class="badge bg-warning priority-medium">Medium Priority</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>Global Manufacturing Inc</strong>
                                <br><small class="text-muted">sarah.wilson@globalmanuf.com</small>
                                <br><small class="text-muted">+1 (555) 987-6543</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>8 Items</strong>
                                <br><small class="text-muted">Steel Components (5), Plastic Parts (3)</small>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: 65%"></div>
                                </div>
                                <small class="text-muted">65% Complete</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>$45,750.00</strong>
                                <br><small class="text-warning">Paid: $20,000.00</small>
                                <br><small class="text-danger">Balance: $25,750.00</small>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-ordered">Ordered</span>
                            <br><small class="text-muted">In production</small>
                        </td>
                        <td>
                            <div>
                                <strong>Ordered:</strong> Feb 01, 2024
                                <br><small class="text-muted">Expected: Feb 20, 2024</small>
                                <br><small class="text-warning">3 days remaining</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('purchases.show', 'PO-2024-002') }}">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('purchases.edit', 'PO-2024-002') }}">
                                        <i class="fas fa-edit"></i> Edit Order
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="receiveOrder('PO-2024-002')">
                                        <i class="fas fa-check"></i> Mark Received
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder('PO-2024-002')">
                                        <i class="fas fa-times"></i> Cancel Order
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input purchase-checkbox" value="PO-2024-003">
                        </td>
                        <td>
                            <div>
                                <strong>PO-2024-003</strong>
                                <br><small class="text-muted">Consulting Services Package</small>
                                <div class="mt-1">
                                    <span class="badge bg-secondary priority-low">Low Priority</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>Premium Services Ltd</strong>
                                <br><small class="text-muted">mike.brown@premiumservices.com</small>
                                <br><small class="text-muted">+1 (555) 456-7890</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>1 Service</strong>
                                <br><small class="text-muted">Business Consultation (40 Hours)</small>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">Not started</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>$8,000.00</strong>
                                <br><small class="text-danger">Paid: $0.00</small>
                                <br><small class="text-danger">Balance: $8,000.00</small>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-pending">Pending</span>
                            <br><small class="text-muted">Awaiting approval</small>
                        </td>
                        <td>
                            <div>
                                <strong>Created:</strong> Feb 10, 2024
                                <br><small class="text-muted">Expected: Feb 25, 2024</small>
                                <br><small class="text-info">Needs approval</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('purchases.show', 'PO-2024-003') }}">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('purchases.edit', 'PO-2024-003') }}">
                                        <i class="fas fa-edit"></i> Edit Order
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="approveOrder('PO-2024-003')">
                                        <i class="fas fa-check"></i> Approve Order
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="rejectOrder('PO-2024-003')">
                                        <i class="fas fa-times"></i> Reject Order
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="text-muted">
                Showing 1-3 of {{ $stats['total_purchases'] ?? '287' }} purchase orders
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Select an action to perform on <span id="selectedCount">0</span> selected purchase orders:</p>
                <div class="list-group">
                    <button class="list-group-item list-group-item-action" onclick="bulkApprove()">
                        <i class="fas fa-check text-success"></i> Approve Selected Orders
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="bulkReceive()">
                        <i class="fas fa-box text-info"></i> Mark as Received
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="bulkExport()">
                        <i class="fas fa-download text-primary"></i> Export Selected to Excel
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="bulkCancel()">
                        <i class="fas fa-times text-danger"></i> Cancel Selected Orders
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    bindEventListeners();
    loadPurchasesData();
});

function bindEventListeners() {
    // Search functionality
    $('#searchPurchases').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterTable();
    });

    // Filter dropdowns
    $('#statusFilter, #supplierFilter, #priorityFilter, #dateFilter').on('change', function() {
        filterTable();
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.purchase-checkbox').prop('checked', this.checked);
        updateBulkActionsButton();
    });

    // Individual checkboxes
    $(document).on('change', '.purchase-checkbox', function() {
        updateBulkActionsButton();
    });
}

function filterTable() {
    const searchTerm = $('#searchPurchases').val().toLowerCase();
    const status = $('#statusFilter').val();
    const supplier = $('#supplierFilter').val();
    const priority = $('#priorityFilter').val();

    $('#purchasesTableBody tr').each(function() {
        let showRow = true;
        const row = $(this);
        const text = row.text().toLowerCase();

        // Search filter
        if (searchTerm && text.indexOf(searchTerm) === -1) {
            showRow = false;
        }

        // Status filter
        if (status && !row.find('.status-badge').hasClass('status-' + status)) {
            showRow = false;
        }

        // Priority filter
        if (priority && !row.find('.badge').hasClass('priority-' + priority)) {
            showRow = false;
        }

        row.toggle(showRow);
    });
}

function updateBulkActionsButton() {
    const selectedCount = $('.purchase-checkbox:checked').length;
    if (selectedCount > 0) {
        if (!$('#bulkActionsBtn').length) {
            $('.table-header .col-md-6:last-child .btn-group').append(
                '<button type="button" class="btn btn-outline-warning btn-sm" id="bulkActionsBtn" onclick="showBulkActions()">' +
                '<i class="fas fa-tasks"></i> Bulk (' + selectedCount + ')' +
                '</button>'
            );
        } else {
            $('#bulkActionsBtn').html('<i class="fas fa-tasks"></i> Bulk (' + selectedCount + ')');
        }
    } else {
        $('#bulkActionsBtn').remove();
    }
}

function showBulkActions() {
    const selectedCount = $('.purchase-checkbox:checked').length;
    $('#selectedCount').text(selectedCount);
    $('#bulkActionsModal').modal('show');
}

function loadPurchasesData() {
    // Simulate AJAX call to load purchase orders
    console.log('Loading purchase orders data...');
}

function refreshTable() {
    window.location.reload();
}

function clearFilters() {
    $('#searchPurchases').val('');
    $('#statusFilter, #supplierFilter, #priorityFilter, #dateFilter').val('');
    filterTable();
}

function exportData() {
    alert('Exporting purchase orders data to Excel...');
}

function bulkActions() {
    if ($('.purchase-checkbox:checked').length === 0) {
        alert('Please select at least one purchase order.');
        return;
    }
    showBulkActions();
}

function approveOrder(poNumber) {
    if (confirm(`Are you sure you want to approve purchase order ${poNumber}?`)) {
        alert(`Purchase order ${poNumber} has been approved.`);
        location.reload();
    }
}

function receiveOrder(poNumber) {
    if (confirm(`Mark purchase order ${poNumber} as received?`)) {
        alert(`Purchase order ${poNumber} has been marked as received.`);
        location.reload();
    }
}

function cancelOrder(poNumber) {
    if (confirm(`Are you sure you want to cancel purchase order ${poNumber}? This action cannot be undone.`)) {
        alert(`Purchase order ${poNumber} has been cancelled.`);
        location.reload();
    }
}

function rejectOrder(poNumber) {
    if (confirm(`Are you sure you want to reject purchase order ${poNumber}?`)) {
        alert(`Purchase order ${poNumber} has been rejected.`);
        location.reload();
    }
}

function duplicateOrder(poNumber) {
    if (confirm(`Create a duplicate of purchase order ${poNumber}?`)) {
        alert(`Duplicate purchase order created based on ${poNumber}.`);
    }
}

function downloadPdf(poNumber) {
    alert(`Downloading PDF for purchase order ${poNumber}...`);
}

function bulkApprove() {
    const selected = $('.purchase-checkbox:checked').length;
    if (confirm(`Are you sure you want to approve ${selected} selected purchase orders?`)) {
        alert(`${selected} purchase orders have been approved.`);
        $('#bulkActionsModal').modal('hide');
        location.reload();
    }
}

function bulkReceive() {
    const selected = $('.purchase-checkbox:checked').length;
    if (confirm(`Mark ${selected} selected purchase orders as received?`)) {
        alert(`${selected} purchase orders have been marked as received.`);
        $('#bulkActionsModal').modal('hide');
        location.reload();
    }
}

function bulkExport() {
    const selected = $('.purchase-checkbox:checked').length;
    alert(`Exporting ${selected} selected purchase orders to Excel...`);
    $('#bulkActionsModal').modal('hide');
}

function bulkCancel() {
    const selected = $('.purchase-checkbox:checked').length;
    if (confirm(`Are you sure you want to cancel ${selected} selected purchase orders? This action cannot be undone.`)) {
        alert(`${selected} purchase orders have been cancelled.`);
        $('#bulkActionsModal').modal('hide');
        location.reload();
    }
}
</script>
@endpush