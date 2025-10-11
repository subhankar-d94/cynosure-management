@extends('layouts.app')

@section('title', 'Custom Report Builder')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 text-dark font-weight-bold">🔧 Custom Report Builder</h1>
                        <p class="text-muted mb-0">Create custom reports with filters and advanced options</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <button class="btn btn-success" onclick="generateReport()" id="generateBtn" disabled>
                            <i class="fas fa-play"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Builder Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-4"><i class="fas fa-cogs me-2"></i>Report Configuration</h5>

                <form id="customReportForm">
                    @csrf

                    <!-- Report Type Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Report Type <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="form-check report-type-card">
                                    <input class="form-check-input" type="radio" name="report_type" id="sales_report" value="sales">
                                    <label class="form-check-label w-100" for="sales_report">
                                        <i class="fas fa-chart-line me-2 text-primary"></i>
                                        <strong>Sales Report</strong>
                                        <small class="d-block text-muted">Revenue, orders, and sales analysis</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check report-type-card">
                                    <input class="form-check-input" type="radio" name="report_type" id="inventory_report" value="inventory">
                                    <label class="form-check-label w-100" for="inventory_report">
                                        <i class="fas fa-boxes me-2 text-success"></i>
                                        <strong>Inventory Report</strong>
                                        <small class="d-block text-muted">Stock levels and movements</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check report-type-card">
                                    <input class="form-check-input" type="radio" name="report_type" id="customer_report" value="customers">
                                    <label class="form-check-label w-100" for="customer_report">
                                        <i class="fas fa-users me-2 text-info"></i>
                                        <strong>Customer Report</strong>
                                        <small class="d-block text-muted">Customer insights and behavior</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check report-type-card">
                                    <input class="form-check-input" type="radio" name="report_type" id="financial_report" value="financial">
                                    <label class="form-check-label w-100" for="financial_report">
                                        <i class="fas fa-chart-pie me-2 text-warning"></i>
                                        <strong>Financial Report</strong>
                                        <small class="d-block text-muted">Revenue and financial analysis</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Date Range</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="start_date">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="end_date">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="setDateRange('today')">Today</button>
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="setDateRange('week')">This Week</button>
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="setDateRange('month')">This Month</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('year')">This Year</button>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Advanced Filters</label>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Status Filter</label>
                                <select class="form-select" name="status_filter">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category Filter</label>
                                <select class="form-select" name="category_filter">
                                    <option value="">All Categories</option>
                                    <!-- Categories will be populated dynamically -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Amount Range</label>
                                <select class="form-select" name="amount_range">
                                    <option value="">All Amounts</option>
                                    <option value="0-1000">₹0 - ₹1,000</option>
                                    <option value="1000-5000">₹1,000 - ₹5,000</option>
                                    <option value="5000-10000">₹5,000 - ₹10,000</option>
                                    <option value="10000+">₹10,000+</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Output Format -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Output Format</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="output_format" id="format_web" value="web" checked>
                                    <label class="form-check-label" for="format_web">
                                        <i class="fas fa-desktop me-1"></i> Web View
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="output_format" id="format_pdf" value="pdf">
                                    <label class="form-check-label" for="format_pdf">
                                        <i class="fas fa-file-pdf me-1"></i> PDF Export
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="output_format" id="format_excel" value="excel">
                                    <label class="form-check-label" for="format_excel">
                                        <i class="fas fa-file-excel me-1"></i> Excel Export
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Preview -->
        <div class="col-lg-4">
            <div class="bg-white rounded-lg shadow-sm border-0 p-4">
                <h5 class="mb-3"><i class="fas fa-eye me-2"></i>Report Preview</h5>

                <div id="reportPreview" class="text-center text-muted">
                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                    <p>Select report type to see preview</p>
                </div>

                <!-- Report Templates -->
                <div class="mt-4">
                    <h6 class="mb-2">Quick Templates</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="loadTemplate('monthly_sales')">
                            <i class="fas fa-calendar me-1"></i> Monthly Sales
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="loadTemplate('low_stock')">
                            <i class="fas fa-exclamation-triangle me-1"></i> Low Stock Items
                        </button>
                        <button class="btn btn-sm btn-outline-info" onclick="loadTemplate('top_customers')">
                            <i class="fas fa-crown me-1"></i> Top Customers
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="loadTemplate('financial_summary')">
                            <i class="fas fa-chart-pie me-1"></i> Financial Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Under Development Notice -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-tools me-2"></i>
                <strong>Under Development:</strong> The custom report builder is currently being developed.
                The interface is ready, but report generation functionality will be available soon.
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.report-type-card {
    border: 2px solid #e3e6f0;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.report-type-card:hover {
    border-color: #4e73df;
    background: #f8f9fc;
}

.report-type-card input[type="radio"]:checked + label {
    color: #4e73df;
}

.report-type-card input[type="radio"]:checked {
    border-color: #4e73df;
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

.text-primary {
    color: #4e73df !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default date range
    setDateRange('month');

    // Enable generate button when report type is selected
    document.querySelectorAll('input[name="report_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.getElementById('generateBtn').disabled = false;
            updatePreview();
        });
    });
});

function setDateRange(period) {
    const today = new Date();
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    endDate.value = today.toISOString().split('T')[0];

    switch(period) {
        case 'today':
            startDate.value = today.toISOString().split('T')[0];
            break;
        case 'week':
            const weekAgo = new Date(today);
            weekAgo.setDate(today.getDate() - 7);
            startDate.value = weekAgo.toISOString().split('T')[0];
            break;
        case 'month':
            const monthAgo = new Date(today);
            monthAgo.setMonth(today.getMonth() - 1);
            startDate.value = monthAgo.toISOString().split('T')[0];
            break;
        case 'year':
            const yearAgo = new Date(today);
            yearAgo.setFullYear(today.getFullYear() - 1);
            startDate.value = yearAgo.toISOString().split('T')[0];
            break;
    }
}

function updatePreview() {
    const reportType = document.querySelector('input[name="report_type"]:checked');
    const preview = document.getElementById('reportPreview');

    if (!reportType) return;

    const previews = {
        'sales': `
            <div class="text-start">
                <h6 class="text-primary">Sales Report Preview</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-1"></i> Total Revenue</li>
                    <li><i class="fas fa-check text-success me-1"></i> Order Count</li>
                    <li><i class="fas fa-check text-success me-1"></i> Average Order Value</li>
                    <li><i class="fas fa-check text-success me-1"></i> Sales Trend</li>
                </ul>
            </div>
        `,
        'inventory': `
            <div class="text-start">
                <h6 class="text-success">Inventory Report Preview</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-1"></i> Stock Levels</li>
                    <li><i class="fas fa-check text-success me-1"></i> Low Stock Alerts</li>
                    <li><i class="fas fa-check text-success me-1"></i> Inventory Value</li>
                    <li><i class="fas fa-check text-success me-1"></i> Movement History</li>
                </ul>
            </div>
        `,
        'customers': `
            <div class="text-start">
                <h6 class="text-info">Customer Report Preview</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-1"></i> Customer Count</li>
                    <li><i class="fas fa-check text-success me-1"></i> New Customers</li>
                    <li><i class="fas fa-check text-success me-1"></i> Customer Value</li>
                    <li><i class="fas fa-check text-success me-1"></i> Purchase Patterns</li>
                </ul>
            </div>
        `,
        'financial': `
            <div class="text-start">
                <h6 class="text-warning">Financial Report Preview</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-1"></i> Revenue Summary</li>
                    <li><i class="fas fa-check text-success me-1"></i> Expense Breakdown</li>
                    <li><i class="fas fa-check text-success me-1"></i> Profit Margins</li>
                    <li><i class="fas fa-check text-success me-1"></i> Financial Trends</li>
                </ul>
            </div>
        `
    };

    preview.innerHTML = previews[reportType.value] || '<p>Select a report type</p>';
}

function loadTemplate(template) {
    switch(template) {
        case 'monthly_sales':
            document.getElementById('sales_report').checked = true;
            setDateRange('month');
            break;
        case 'low_stock':
            document.getElementById('inventory_report').checked = true;
            break;
        case 'top_customers':
            document.getElementById('customer_report').checked = true;
            setDateRange('month');
            break;
        case 'financial_summary':
            document.getElementById('financial_report').checked = true;
            setDateRange('month');
            break;
    }
    updatePreview();
    document.getElementById('generateBtn').disabled = false;
}

function generateReport() {
    const form = document.getElementById('customReportForm');
    const formData = new FormData(form);

    // For now, show an alert
    alert('Custom report generation is under development. This feature will be available soon!');

    // Future implementation will submit the form data
    // and generate the actual report
}
</script>
@endpush
@endsection