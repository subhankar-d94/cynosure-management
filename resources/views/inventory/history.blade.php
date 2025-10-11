@extends('layouts.app')

@section('title', 'Inventory History - ' . $inventory->product->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Inventory History</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">History</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success" onclick="exportHistory()">
                <i class="bi bi-file-excel"></i> Export History
            </button>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Inventory
            </a>
        </div>
    </div>

    <!-- Product Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if($inventory->product->image_url)
                        <img src="{{ $inventory->product->image_url }}" alt="{{ $inventory->product->name }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-image text-muted fs-2"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <h5 class="mb-1">{{ $inventory->product->name }}</h5>
                    <p class="text-muted mb-1">{{ $inventory->product->description }}</p>
                    <div class="d-flex align-items-center gap-3">
                        <span><strong>SKU:</strong> <code>{{ $inventory->product->sku }}</code></span>
                        <span><strong>Category:</strong> {{ $inventory->product->category->name ?? '-' }}</span>
                        <span><strong>Supplier:</strong> {{ $inventory->supplier->company_name ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="text-center">
                        <div class="fs-2 fw-bold text-primary">{{ $inventory->quantity_in_stock }}</div>
                        <small class="text-muted">Current Stock</small>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-warning">{{ $inventory->reserved_quantity }}</div>
                        <small class="text-muted">Reserved</small>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-success">{{ $inventory->available_quantity }}</div>
                        <small class="text-muted">Available</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0">Stock Movement Timeline</h6>
        </div>
        <div class="card-body">
            <canvas id="stockChart" height="100"></canvas>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <input type="text" class="form-control" id="dateRange" placeholder="Select date range">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Movement Type</label>
                    <select class="form-select" id="movementTypeFilter">
                        <option value="">All Types</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Reason</label>
                    <select class="form-select" id="reasonFilter">
                        <option value="">All Reasons</option>
                        <option value="sale">Sale</option>
                        <option value="purchase">Purchase</option>
                        <option value="return">Return</option>
                        <option value="adjustment">Manual Adjustment</option>
                        <option value="damage">Damage/Loss</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select class="form-select" id="userFilter">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                        <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Movement History</h6>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">Show</small>
                    <select class="form-select form-select-sm w-auto" id="perPage" onchange="changePerPage()">
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <small class="text-muted">entries</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Balance After</th>
                            <th>Reason</th>
                            <th>Reference</th>
                            <th>User</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <!-- History data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted" id="paginationInfo">
                    Loading...
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="pagination">
                        <!-- Pagination will be loaded here -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

<script>
let currentPage = 1;
let currentFilters = {};
let stockChart;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeDateRangePicker();
    initializeChart();
    loadHistoryData();
});

function initializeDateRangePicker() {
    $('#dateRange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}

function initializeChart() {
    const ctx = document.getElementById('stockChart').getContext('2d');

    stockChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Stock Level',
                data: [],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        title: function(context) {
                            return 'Date: ' + context[0].label;
                        },
                        label: function(context) {
                            return 'Stock Level: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Stock Quantity'
                    },
                    beginAtZero: true
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    loadChartData();
}

function loadChartData() {
    fetch(`/inventory/{{ $inventory->id }}/chart-data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                stockChart.data.labels = data.labels;
                stockChart.data.datasets[0].data = data.data;
                stockChart.update();
            }
        })
        .catch(error => console.error('Error loading chart data:', error));
}

function loadHistoryData(page = 1) {
    const perPage = document.getElementById('perPage').value;

    let url = `/inventory/{{ $inventory->id }}/history-data?page=${page}&per_page=${perPage}`;

    // Add filters
    Object.keys(currentFilters).forEach(key => {
        if (currentFilters[key]) {
            url += `&${key}=${encodeURIComponent(currentFilters[key])}`;
        }
    });

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderHistoryTable(data.history);
                renderPagination(data.history);
                currentPage = page;
            } else {
                showToast('Failed to load history data', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading history:', error);
            showToast('Error loading history data', 'error');
        });
}

function renderHistoryTable(history) {
    const tbody = document.getElementById('historyTableBody');

    if (history.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-clock-history fs-1"></i>
                        <div class="mt-2">No movement history found</div>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = history.data.map(movement => {
        const isIncrease = movement.movement_type === 'in' || (movement.movement_type === 'adjustment' && movement.quantity > 0);
        const quantityClass = isIncrease ? 'text-success' : 'text-danger';
        const quantityIcon = isIncrease ? '+' : '';

        return `
            <tr>
                <td>
                    <div>${formatDateTime(movement.created_at)}</div>
                    <small class="text-muted">${formatTimeAgo(movement.created_at)}</small>
                </td>
                <td>
                    ${getMovementTypeBadge(movement.movement_type)}
                </td>
                <td>
                    <span class="${quantityClass} fw-bold">
                        ${quantityIcon}${Math.abs(movement.quantity)}
                    </span>
                </td>
                <td class="fw-medium">${movement.balance_after}</td>
                <td>
                    <span class="badge bg-light text-dark">${movement.reason}</span>
                </td>
                <td>
                    ${movement.reference ? `<code class="small">${movement.reference}</code>` : '-'}
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-2">
                            <div class="avatar-title bg-primary text-white rounded-circle">
                                ${movement.user?.name?.charAt(0) || 'S'}
                            </div>
                        </div>
                        ${movement.user?.name || 'System'}
                    </div>
                </td>
                <td>
                    ${movement.notes ? `<span class="text-muted small">${movement.notes}</span>` : '-'}
                </td>
            </tr>
        `;
    }).join('');
}

function getMovementTypeBadge(type) {
    const badges = {
        'in': '<span class="badge bg-success"><i class="bi bi-arrow-down-circle me-1"></i>Stock In</span>',
        'out': '<span class="badge bg-danger"><i class="bi bi-arrow-up-circle me-1"></i>Stock Out</span>',
        'adjustment': '<span class="badge bg-warning"><i class="bi bi-pencil-square me-1"></i>Adjustment</span>'
    };
    return badges[type] || badges['adjustment'];
}

function renderPagination(history) {
    const pagination = document.getElementById('pagination');
    const info = document.getElementById('paginationInfo');

    // Update info
    const start = (history.current_page - 1) * history.per_page + 1;
    const end = Math.min(start + history.per_page - 1, history.total);
    info.textContent = `Showing ${start} to ${end} of ${history.total} entries`;

    // Generate pagination
    let paginationHTML = '';

    // Previous button
    if (history.current_page > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="loadHistoryData(${history.current_page - 1})">Previous</a></li>`;
    }

    // Page numbers
    const startPage = Math.max(1, history.current_page - 2);
    const endPage = Math.min(history.last_page, history.current_page + 2);

    if (startPage > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="loadHistoryData(1)">1</a></li>`;
        if (startPage > 2) {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const active = i === history.current_page ? 'active' : '';
        paginationHTML += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadHistoryData(${i})">${i}</a></li>`;
    }

    if (endPage < history.last_page) {
        if (endPage < history.last_page - 1) {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="loadHistoryData(${history.last_page})">${history.last_page}</a></li>`;
    }

    // Next button
    if (history.current_page < history.last_page) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="loadHistoryData(${history.current_page + 1})">Next</a></li>`;
    }

    pagination.innerHTML = paginationHTML;
}

function applyFilters() {
    const dateRange = document.getElementById('dateRange').value;
    let startDate = '', endDate = '';

    if (dateRange) {
        const dates = dateRange.split(' - ');
        startDate = dates[0];
        endDate = dates[1];
    }

    currentFilters = {
        start_date: startDate,
        end_date: endDate,
        movement_type: document.getElementById('movementTypeFilter').value,
        reason: document.getElementById('reasonFilter').value,
        user: document.getElementById('userFilter').value
    };

    loadHistoryData(1);
    loadChartData(); // Refresh chart with filters
}

function clearFilters() {
    document.getElementById('dateRange').val('');
    document.getElementById('movementTypeFilter').value = '';
    document.getElementById('reasonFilter').value = '';
    document.getElementById('userFilter').value = '';
    currentFilters = {};
    loadHistoryData(1);
    loadChartData();
}

function changePerPage() {
    loadHistoryData(1);
}

function exportHistory() {
    let url = `/inventory/{{ $inventory->id }}/export-history?`;

    // Add current filters to export
    Object.keys(currentFilters).forEach(key => {
        if (currentFilters[key]) {
            url += `${key}=${encodeURIComponent(currentFilters[key])}&`;
        }
    });

    window.open(url, '_blank');
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
    if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 86400) + ' days ago';
    return Math.floor(diffInSeconds / 2592000) + ' months ago';
}
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 500;
}
</style>
@endpush