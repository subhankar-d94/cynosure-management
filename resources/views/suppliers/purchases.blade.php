@extends('layouts.app')

@section('title', 'Supplier Purchases')

@push('styles')
<style>
    .purchases-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .supplier-info {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        border-left: 4px solid #007bff;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 5px;
    }

    .stat-card .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        background: #007bff;
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

    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-ordered { background: #cce7ff; color: #004085; }
    .status-received { background: #d1ecf1; color: #0c5460; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .filter-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn-export {
        background: #28a745;
        border-color: #28a745;
        color: white;
    }

    .btn-export:hover {
        background: #218838;
        border-color: #1e7e34;
        color: white;
    }

    .purchase-summary {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .chart-container {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .priority-high { color: #dc3545; }
    .priority-medium { color: #ffc107; }
    .priority-low { color: #28a745; }

    @media (max-width: 768px) {
        .purchases-header {
            padding: 20px;
            text-align: center;
        }

        .stat-card {
            margin-bottom: 15px;
        }

        .table-responsive {
            border-radius: 8px;
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
                <h1 class="h3 mb-0">Purchase Management</h1>
                <p class="mb-0 opacity-75">Track and manage supplier purchases, orders, and relationships</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog"></i> Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportPurchases()">
                            <i class="fas fa-download"></i> Export Data
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="generateReport()">
                            <i class="fas fa-chart-line"></i> Generate Report
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkActions()">
                            <i class="fas fa-tasks"></i> Bulk Actions
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Supplier Info -->
    @if(isset($supplier))
    <div class="supplier-info">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">{{ $supplier->company_name ?? 'TechCorp Solutions Ltd.' }}</h5>
                <p class="text-muted mb-0">
                    <i class="fas fa-user"></i> {{ $supplier->contact_person ?? 'John Anderson' }} •
                    <i class="fas fa-envelope"></i> {{ $supplier->email ?? 'john.anderson@techcorp.com' }} •
                    <i class="fas fa-phone"></i> {{ $supplier->phone ?? '+1 (555) 123-4567' }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('suppliers.show', $supplier->id ?? 1) }}" class="btn btn-outline-primary btn-sm me-2">
                    <i class="fas fa-eye"></i> View Profile
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> All Suppliers
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-number">{{ $totalPurchases ?? '156' }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-number">${{ number_format($totalValue ?? 890450, 0) }}</div>
                <div class="stat-label">Total Value</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-number">{{ $pendingOrders ?? '12' }}</div>
                <div class="stat-label">Pending Orders</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-number">{{ $avgDelivery ?? '14' }} Days</div>
                <div class="stat-label">Avg Delivery Time</div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-card">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Supplier</label>
                <select class="form-select" id="supplierFilter">
                    <option value="">All Suppliers</option>
                    <option value="1" {{ (request('supplier') == '1') ? 'selected' : '' }}>TechCorp Solutions</option>
                    <option value="2">Global Manufacturing Inc</option>
                    <option value="3">Premium Services Ltd</option>
                    <option value="4">Industrial Supplies Co</option>
                    <option value="5">Digital Systems Group</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="ordered">Ordered</option>
                    <option value="received">Received</option>
                    <option value="cancelled">Cancelled</option>
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
                    <option value="year">This Year</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" id="searchPurchases" placeholder="Search orders, materials...">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button class="btn btn-export" onclick="exportPurchases()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Summary -->
    <div class="purchase-summary">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary mb-3">Monthly Performance</h6>
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-success mb-1">${{ number_format($monthlyValue ?? 125680, 0) }}</div>
                            <div class="small text-muted">This Month</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-info mb-1">{{ $monthlyOrders ?? '28' }}</div>
                            <div class="small text-muted">Orders</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary mb-3">Payment Status</h6>
                <div class="row">
                    <div class="col-4">
                        <div class="text-center">
                            <div class="h6 text-success mb-1">${{ number_format($paidAmount ?? 745230, 0) }}</div>
                            <div class="small text-muted">Paid</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="h6 text-warning mb-1">${{ number_format($pendingAmount ?? 95420, 0) }}</div>
                            <div class="small text-muted">Pending</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="h6 text-danger mb-1">${{ number_format($overdueAmount ?? 49800, 0) }}</div>
                            <div class="small text-muted">Overdue</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
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
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleFilters()">
                            <i class="fas fa-filter"></i> Filters
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
                                <br><small class="text-muted">Software Licenses & Support</small>
                                <div class="mt-1">
                                    <span class="badge bg-info">High Priority</span>
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
                                <strong>5 Items</strong>
                                <br><small class="text-muted">Enterprise Licenses (3)</small>
                                <br><small class="text-muted">Support Package (2)</small>
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
                            <span class="status-badge status-received">Received</span>
                            <br><small class="text-muted">Delivered on time</small>
                        </td>
                        <td>
                            <div>
                                <strong>Order:</strong> Jan 15, 2024
                                <br><small class="text-muted">Delivery: Jan 29, 2024</small>
                                <br><small class="text-muted">Lead: 14 days</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="viewPurchase('PO-2024-001')">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="editPurchase('PO-2024-001')">
                                        <i class="fas fa-edit"></i> Edit Order
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="trackDelivery('PO-2024-001')">
                                        <i class="fas fa-truck"></i> Track Delivery
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="downloadInvoice('PO-2024-001')">
                                        <i class="fas fa-download"></i> Download Invoice
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
                                <br><small class="text-muted">Raw Materials Package</small>
                                <div class="mt-1">
                                    <span class="badge bg-warning">Medium Priority</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>Global Manufacturing</strong>
                                <br><small class="text-muted">sarah.wilson@globalmanuf.com</small>
                                <br><small class="text-muted">+1 (555) 987-6543</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>12 Items</strong>
                                <br><small class="text-muted">Steel Components (8)</small>
                                <br><small class="text-muted">Plastic Parts (4)</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>$45,750.00</strong>
                                <br><small class="text-warning">Paid: $20,000.00</small>
                                <br><small class="text-muted">Balance: $25,750.00</small>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-ordered">Ordered</span>
                            <br><small class="text-muted">In production</small>
                        </td>
                        <td>
                            <div>
                                <strong>Order:</strong> Feb 01, 2024
                                <br><small class="text-muted">Expected: Feb 20, 2024</small>
                                <br><small class="text-muted">Lead: 19 days</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="viewPurchase('PO-2024-002')">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="editPurchase('PO-2024-002')">
                                        <i class="fas fa-edit"></i> Edit Order
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="trackDelivery('PO-2024-002')">
                                        <i class="fas fa-truck"></i> Track Delivery
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
                                <br><small class="text-muted">Consulting Services</small>
                                <div class="mt-1">
                                    <span class="badge bg-secondary">Low Priority</span>
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
                                <br><small class="text-muted">Business Consultation</small>
                                <br><small class="text-muted">40 Hours</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>$8,000.00</strong>
                                <br><small class="text-danger">Paid: $0.00</small>
                                <br><small class="text-muted">Balance: $8,000.00</small>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-pending">Pending</span>
                            <br><small class="text-muted">Awaiting approval</small>
                        </td>
                        <td>
                            <div>
                                <strong>Order:</strong> Feb 10, 2024
                                <br><small class="text-muted">Expected: Feb 25, 2024</small>
                                <br><small class="text-muted">Lead: 15 days</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="viewPurchase('PO-2024-003')">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="approvePurchase('PO-2024-003')">
                                        <i class="fas fa-check"></i> Approve Order
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="editPurchase('PO-2024-003')">
                                        <i class="fas fa-edit"></i> Edit Order
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
                Showing 1-3 of {{ $totalPurchases ?? '156' }} purchases
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

    <!-- Chart Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="chart-container">
                <h6 class="text-primary mb-3">Purchase Trends (Last 6 Months)</h6>
                <canvas id="purchaseTrendsChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h6 class="text-primary mb-3">Supplier Distribution</h6>
                <canvas id="supplierDistributionChart" height="200"></canvas>
            </div>
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
                <p>Select an action to perform on <span id="selectedCount">0</span> selected purchases:</p>
                <div class="list-group">
                    <button class="list-group-item list-group-item-action" onclick="bulkApprove()">
                        <i class="fas fa-check text-success"></i> Approve Selected Orders
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="bulkExport()">
                        <i class="fas fa-download text-primary"></i> Export Selected to Excel
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="bulkUpdate()">
                        <i class="fas fa-edit text-info"></i> Update Status
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="bulkDelete()">
                        <i class="fas fa-trash text-danger"></i> Delete Selected Orders
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Details Modal -->
<div class="modal fade" id="purchaseDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchase Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="purchaseDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    initializeCharts();
    bindEventListeners();
    loadPurchasesData();
});

function initializeCharts() {
    // Purchase Trends Chart
    const trendsCtx = document.getElementById('purchaseTrendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
            datasets: [{
                label: 'Purchase Value',
                data: [65000, 75000, 95000, 85000, 110000, 125000],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Supplier Distribution Chart
    const distCtx = document.getElementById('supplierDistributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['TechCorp Solutions', 'Global Manufacturing', 'Premium Services', 'Others'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom'
            }
        }
    });
}

function bindEventListeners() {
    // Search functionality
    $('#searchPurchases').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterTable(searchTerm);
    });

    // Filter dropdowns
    $('#supplierFilter, #statusFilter, #dateFilter').on('change', function() {
        applyFilters();
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

function loadPurchasesData() {
    // Simulate AJAX call to load purchases
    console.log('Loading purchases data...');
}

function filterTable(searchTerm) {
    $('#purchasesTableBody tr').each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        row.toggle(text.indexOf(searchTerm) > -1);
    });
}

function applyFilters() {
    const supplier = $('#supplierFilter').val();
    const status = $('#statusFilter').val();
    const dateRange = $('#dateFilter').val();

    // Apply filters to table rows
    $('#purchasesTableBody tr').each(function() {
        let showRow = true;

        if (supplier && !$(this).find('td:nth-child(3)').text().includes(supplier)) {
            showRow = false;
        }

        if (status && !$(this).find('.status-badge').hasClass('status-' + status)) {
            showRow = false;
        }

        $(this).toggle(showRow);
    });
}

function updateBulkActionsButton() {
    const selectedCount = $('.purchase-checkbox:checked').length;
    if (selectedCount > 0) {
        if (!$('#bulkActionsBtn').length) {
            $('.table-header .col-md-6:last-child .btn-group').append(
                '<button type="button" class="btn btn-outline-warning btn-sm" id="bulkActionsBtn" onclick="showBulkActions()">' +
                '<i class="fas fa-tasks"></i> Bulk Actions (' + selectedCount + ')' +
                '</button>'
            );
        } else {
            $('#bulkActionsBtn').html('<i class="fas fa-tasks"></i> Bulk Actions (' + selectedCount + ')');
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

function refreshTable() {
    window.location.reload();
}

function toggleFilters() {
    $('.filter-card').slideToggle();
}

function exportPurchases() {
    // Simulate export functionality
    alert('Exporting purchases data to Excel...');
}

function generateReport() {
    // Simulate report generation
    alert('Generating purchase performance report...');
}

function viewPurchase(orderId) {
    // Load purchase details in modal
    $('#purchaseDetailsContent').html(`
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Loading purchase details for ${orderId}...</p>
        </div>
    `);
    $('#purchaseDetailsModal').modal('show');

    // Simulate loading purchase details
    setTimeout(function() {
        $('#purchaseDetailsContent').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Order Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Order ID:</strong></td><td>${orderId}</td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-success">Received</span></td></tr>
                        <tr><td><strong>Order Date:</strong></td><td>Jan 15, 2024</td></tr>
                        <tr><td><strong>Expected:</strong></td><td>Jan 29, 2024</td></tr>
                        <tr><td><strong>Delivered:</strong></td><td>Jan 29, 2024</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Financial Details</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Subtotal:</strong></td><td>$22,500.00</td></tr>
                        <tr><td><strong>Tax:</strong></td><td>$2,000.00</td></tr>
                        <tr><td><strong>Total:</strong></td><td>$24,500.00</td></tr>
                        <tr><td><strong>Paid:</strong></td><td>$24,500.00</td></tr>
                        <tr><td><strong>Balance:</strong></td><td>$0.00</td></tr>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                <h6 class="text-primary">Items Ordered</h6>
                <table class="table table-sm">
                    <thead><tr><th>Description</th><th>Qty</th><th>Rate</th><th>Amount</th></tr></thead>
                    <tbody>
                        <tr><td>Enterprise Software License</td><td>3</td><td>$5,000.00</td><td>$15,000.00</td></tr>
                        <tr><td>Premium Support Package</td><td>2</td><td>$3,750.00</td><td>$7,500.00</td></tr>
                    </tbody>
                </table>
            </div>
        `);
    }, 1000);
}

function editPurchase(orderId) {
    window.location.href = `/purchases/${orderId}/edit`;
}

function trackDelivery(orderId) {
    alert(`Tracking delivery for order ${orderId}...`);
}

function downloadInvoice(orderId) {
    alert(`Downloading invoice for order ${orderId}...`);
}

function approvePurchase(orderId) {
    if (confirm(`Are you sure you want to approve order ${orderId}?`)) {
        alert(`Order ${orderId} has been approved.`);
        location.reload();
    }
}

function cancelOrder(orderId) {
    if (confirm(`Are you sure you want to cancel order ${orderId}? This action cannot be undone.`)) {
        alert(`Order ${orderId} has been cancelled.`);
        location.reload();
    }
}

function rejectOrder(orderId) {
    if (confirm(`Are you sure you want to reject order ${orderId}?`)) {
        alert(`Order ${orderId} has been rejected.`);
        location.reload();
    }
}

function bulkApprove() {
    const selected = $('.purchase-checkbox:checked').length;
    if (confirm(`Are you sure you want to approve ${selected} selected orders?`)) {
        alert(`${selected} orders have been approved.`);
        $('#bulkActionsModal').modal('hide');
        location.reload();
    }
}

function bulkExport() {
    const selected = $('.purchase-checkbox:checked').length;
    alert(`Exporting ${selected} selected orders to Excel...`);
    $('#bulkActionsModal').modal('hide');
}

function bulkUpdate() {
    alert('Bulk status update functionality would be implemented here.');
    $('#bulkActionsModal').modal('hide');
}

function bulkDelete() {
    const selected = $('.purchase-checkbox:checked').length;
    if (confirm(`Are you sure you want to delete ${selected} selected orders? This action cannot be undone.`)) {
        alert(`${selected} orders have been deleted.`);
        $('#bulkActionsModal').modal('hide');
        location.reload();
    }
}
</script>
@endpush