@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">📊 Business Reports</h1>
                        <p class="text-muted mb-0">Generate comprehensive reports and export data for analysis</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-calendar-alt"></i> Quick Reports
                            </button>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="{{ route('reports.sales') }}?quick=today"><i class="fas fa-chart-line me-2"></i>Today's Sales</a></li>
                                <li><a class="dropdown-item" href="{{ route('reports.sales') }}?quick=week"><i class="fas fa-calendar-week me-2"></i>This Week</a></li>
                                <li><a class="dropdown-item" href="{{ route('reports.sales') }}?quick=month"><i class="fas fa-calendar me-2"></i>This Month</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('reports.inventory') }}"><i class="fas fa-boxes me-2"></i>Current Inventory</a></li>
                            </ul>
                        </div>
                        <a href="{{ route('analytics.index') }}" class="btn btn-primary">
                            <i class="fas fa-chart-pie"></i> Analytics Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row">
        <!-- Sales Reports -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="report-category-card">
                <div class="report-category-header bg-gradient-primary">
                    <div class="d-flex align-items-center">
                        <div class="report-category-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white">Sales Reports</h5>
                            <small class="text-white-50">Revenue, orders, and sales analysis</small>
                        </div>
                    </div>
                </div>
                <div class="report-category-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('reports.sales') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-chart-area me-2 text-primary"></i>
                                    <strong>Sales Overview</strong>
                                    <br><small class="text-muted">Comprehensive sales data and trends</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="{{ route('reports.sales.category') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-tags me-2 text-primary"></i>
                                    <strong>Sales by Category</strong>
                                    <br><small class="text-muted">Performance breakdown by product categories</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="{{ route('reports.sales.customer') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-users me-2 text-primary"></i>
                                    <strong>Sales by Customer</strong>
                                    <br><small class="text-muted">Top customers and purchase patterns</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="{{ route('reports.sales.product') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-box me-2 text-primary"></i>
                                    <strong>Sales by Product</strong>
                                    <br><small class="text-muted">Best-selling products and inventory turnover</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="report-category-card">
                <div class="report-category-header bg-gradient-info">
                    <div class="d-flex align-items-center">
                        <div class="report-category-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white">Financial Reports</h5>
                            <small class="text-white-50">Profit, loss, and financial analysis</small>
                        </div>
                    </div>
                </div>
                <div class="report-category-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('reports.financial') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-chart-bar me-2 text-info"></i>
                                    <strong>Financial Overview</strong>
                                    <br><small class="text-muted">Revenue, expenses, and profitability</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="{{ route('reports.profit-loss') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-balance-scale me-2 text-info"></i>
                                    <strong>Profit & Loss</strong>
                                    <br><small class="text-muted">P&L statements and profitability analysis</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="{{ route('reports.cash-flow') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-coins me-2 text-info"></i>
                                    <strong>Cash Flow</strong>
                                    <br><small class="text-muted">Cash in/out and liquidity analysis</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Reports -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="report-category-card">
                <div class="report-category-header bg-gradient-warning">
                    <div class="d-flex align-items-center">
                        <div class="report-category-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white">Customer Reports</h5>
                            <small class="text-white-50">Customer insights and behavior</small>
                        </div>
                    </div>
                </div>
                <div class="report-category-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('reports.customers') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-address-book me-2 text-warning"></i>
                                    <strong>Customer Overview</strong>
                                    <br><small class="text-muted">Customer database and demographics</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="{{ route('reports.customer-analytics') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-chart-line me-2 text-warning"></i>
                                    <strong>Customer Analytics</strong>
                                    <br><small class="text-muted">Purchase behavior and lifetime value</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Reports -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="report-category-card">
                <div class="report-category-header bg-gradient-danger">
                    <div class="d-flex align-items-center">
                        <div class="report-category-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white">Delivery Reports</h5>
                            <small class="text-white-50">Shipping and logistics performance</small>
                        </div>
                    </div>
                </div>
                <div class="report-category-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('reports.delivery') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-shipping-fast me-2 text-danger"></i>
                                    <strong>Delivery Overview</strong>
                                    <br><small class="text-muted">Shipment status and delivery times</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="{{ route('reports.delivery.performance') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-tachometer-alt me-2 text-danger"></i>
                                    <strong>Delivery Performance</strong>
                                    <br><small class="text-muted">On-time delivery and performance metrics</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="{{ route('reports.delivery.zones') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-map-marked-alt me-2 text-danger"></i>
                                    <strong>Delivery Zones</strong>
                                    <br><small class="text-muted">Geographic delivery analysis</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Reports -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="report-category-card">
                <div class="report-category-header bg-gradient-secondary">
                    <div class="d-flex align-items-center">
                        <div class="report-category-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white">Custom Reports</h5>
                            <small class="text-white-50">Build your own reports</small>
                        </div>
                    </div>
                </div>
                <div class="report-category-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('reports.custom') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-magic me-2 text-secondary"></i>
                                    <strong>Report Builder</strong>
                                    <br><small class="text-muted">Create custom reports with filters</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <a href="#" onclick="showExportModal()" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-download me-2 text-secondary"></i>
                                    <strong>Bulk Export</strong>
                                    <br><small class="text-muted">Export data in various formats</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-tachometer-alt me-2"></i>Quick Statistics</h5>
                <div class="row" id="quickStats">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-value" id="todaySales">-</div>
                            <div class="stat-label">Today's Sales</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-value" id="monthSales">-</div>
                            <div class="stat-label">This Month</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-value" id="totalOrders">-</div>
                            <div class="stat-label">Total Orders</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-value text-warning" id="pendingOrders">-</div>
                            <div class="stat-label">Pending Orders</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label class="form-label">Data Type</label>
                        <select class="form-select" name="type" required>
                            <option value="">Select data type...</option>
                            <option value="sales">Sales Data</option>
                            <option value="inventory">Inventory Data</option>
                            <option value="customers">Customer Data</option>
                            <option value="orders">Order Data</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select class="form-select" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="exportData()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.report-category-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
}

.report-category-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.report-category-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.report-category-header.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}

.report-category-header.bg-gradient-success {
    background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
}

.report-category-header.bg-gradient-info {
    background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
}

.report-category-header.bg-gradient-warning {
    background: linear-gradient(135deg, #f6c23e 0%, #d4a027 100%);
}

.report-category-header.bg-gradient-danger {
    background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
}

.report-category-header.bg-gradient-secondary {
    background: linear-gradient(135deg, #858796 0%, #60616f 100%);
}

.report-category-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.5rem;
    color: white;
}

.report-category-body {
    padding: 0;
}

.list-group-item-action {
    border: none;
    padding: 1rem 1.5rem;
    transition: all 0.2s ease;
}

.list-group-item-action:hover {
    background-color: #f8f9fc;
    padding-left: 2rem;
}

.stat-card {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border: 1px solid #e3e6f0;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: #718096;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bg-white {
    background-color: #fff !important;
}

.rounded-lg {
    border-radius: 12px !important;
}

.shadow-sm {
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .report-category-header {
        padding: 1rem;
    }

    .report-category-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }

    .list-group-item-action {
        padding: 0.75rem 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    loadQuickStats();
});

function loadQuickStats() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: '/reports/sales-summary',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#todaySales').text('₹' + formatNumber(data.total_sales || 0));
                $('#monthSales').text('₹' + formatNumber(data.total_sales || 0));
                $('#totalOrders').text(formatNumber(data.total_orders || 0));
                $('#pendingOrders').text(formatNumber(data.average_order_value || 0));
            }
        },
        error: function() {
            $('#todaySales, #monthSales, #totalOrders, #pendingOrders').text('Error');
        }
    });
}

function showExportModal() {
    $('#exportModal').modal('show');
}

function exportData() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);

    if (!formData.get('type') || !formData.get('format')) {
        alert('Please select both data type and format');
        return;
    }

    // Create download URL
    const params = new URLSearchParams(formData);
    const url = '/reports/export?' + params.toString();

    // Trigger download
    window.open(url, '_blank');

    $('#exportModal').modal('hide');
}

function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    return new Intl.NumberFormat('en-IN').format(num);
}
</script>
@endpush