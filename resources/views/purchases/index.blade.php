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

    .status-draft { background: #e2e3e5; color: #6c757d; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d1ecf1; color: #0c5460; }
    .status-ordered { background: #cce7ff; color: #004085; }
    .status-partial_received { background: #d1ecf1; color: #0c5460; }
    .status-received { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-completed { background: #d4edda; color: #155724; }

    .priority-low { color: #28a745; }
    .priority-medium { color: #ffc107; }
    .priority-high { color: #dc3545; }
    .priority-urgent { color: #dc3545; font-weight: bold; }

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

    .progress {
        height: 6px;
    }

    .purchase-row {
        cursor: pointer;
    }

    .purchase-row:hover {
        background-color: #f8f9fc !important;
    }

    .overdue-indicator {
        background: #ff4757;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    .urgent-indicator {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
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
                <div class="stat-number text-info">{{ $stats['total_purchases'] ?? 0 }}</div>
                <div class="stat-label">Total Orders</div>
                @if(isset($stats['total_purchases_trend']))
                <div class="stat-trend {{ $stats['total_purchases_trend'] > 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $stats['total_purchases_trend'] > 0 ? 'up' : 'down' }}"></i>
                    {{ abs($stats['total_purchases_trend']) }}% from last month
                </div>
                @endif
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-success">₹{{ number_format($stats['total_value'] ?? 0, 2) }}</div>
                <div class="stat-label">Total Value</div>
                @if(isset($stats['total_value_trend']))
                <div class="stat-trend {{ $stats['total_value_trend'] > 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $stats['total_value_trend'] > 0 ? 'up' : 'down' }}"></i>
                    {{ abs($stats['total_value_trend']) }}% from last month
                </div>
                @endif
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-warning">{{ $stats['pending_orders'] ?? 0 }}</div>
                <div class="stat-label">Pending Orders</div>
                @if(isset($stats['pending_orders_trend']))
                <div class="stat-trend {{ $stats['pending_orders_trend'] > 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $stats['pending_orders_trend'] > 0 ? 'up' : 'down' }}"></i>
                    {{ abs($stats['pending_orders_trend']) }} from last week
                </div>
                @endif
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-danger">{{ $stats['overdue_orders'] ?? 0 }}</div>
                <div class="stat-label">Overdue Orders</div>
                @if(isset($stats['overdue_orders_trend']))
                <div class="stat-trend {{ $stats['overdue_orders_trend'] > 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $stats['overdue_orders_trend'] > 0 ? 'up' : 'down' }}"></i>
                    {{ abs($stats['overdue_orders_trend']) }} from last week
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Statistics Row -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-primary">{{ $stats['monthly_orders'] ?? 0 }}</div>
                <div class="stat-label">This Month</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-secondary">{{ $stats['avg_delivery_days'] ?? 0 }} Days</div>
                <div class="stat-label">Avg Delivery</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-info">{{ $stats['active_suppliers'] ?? 0 }}</div>
                <div class="stat-label">Active Suppliers</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-number text-success">₹{{ number_format($stats['cost_savings'] ?? 0, 2) }}</div>
                <div class="stat-label">Cost Savings</div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-card">
        <form method="GET" action="{{ route('purchases.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search PO number, supplier...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Supplier</label>
                    <select class="form-select" name="supplier_id">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Priority</label>
                    <select class="form-select" name="priority">
                        <option value="">All Priority</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="Date To">
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="urgent" id="urgentFilter" value="1" {{ request('urgent') ? 'checked' : '' }}>
                        <label class="form-check-label" for="urgentFilter">
                            Urgent Only
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-undo"></i> Clear
                    </a>
                </div>
            </div>
        </form>
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
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                        <a href="{{ route('purchases.export') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download"></i> Export
                        </a>
                        @if($purchases->count() > 0)
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleBulkActions()">
                            <i class="fas fa-tasks"></i> Bulk Actions
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0" id="purchasesTable">
                <thead>
                    <tr>
                        @if($purchases->count() > 0)
                        <th width="5%">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        @endif
                        <th>Order Details</th>
                        <th>Supplier</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Dates</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr class="purchase-row" data-id="{{ $purchase->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input purchase-checkbox" value="{{ $purchase->id }}">
                        </td>
                        <td>
                            <div>
                                <strong>{{ $purchase->purchase_order_number }}</strong>
                                @if($purchase->reference_number)
                                <br><small class="text-muted">Ref: {{ $purchase->reference_number }}</small>
                                @endif
                                <div class="mt-1">
                                    <span class="badge bg-{{ $purchase->priority_color }} priority-{{ $purchase->priority }}">
                                        {{ $purchase->priority_label }}
                                        @if($purchase->urgent)
                                        <i class="fas fa-exclamation urgent-indicator"></i>
                                        @endif
                                    </span>
                                    @if($purchase->is_overdue)
                                    <span class="overdue-indicator ms-1">
                                        {{ $purchase->days_overdue }} days overdue
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $purchase->supplier->company_name ?? 'N/A' }}</strong>
                                @if($purchase->supplier->email)
                                <br><small class="text-muted">{{ $purchase->supplier->email }}</small>
                                @endif
                                @if($purchase->supplier->phone)
                                <br><small class="text-muted">{{ $purchase->supplier->phone }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $purchase->items->count() }} Item{{ $purchase->items->count() !== 1 ? 's' : '' }}</strong>
                                @if($purchase->items->count() > 0)
                                <br><small class="text-muted">Total Qty: {{ $purchase->total_items }}</small>
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-{{ $purchase->receive_progress == 100 ? 'success' : ($purchase->receive_progress > 0 ? 'info' : 'warning') }}"
                                         style="width: {{ $purchase->receive_progress }}%"></div>
                                </div>
                                <small class="text-muted">{{ $purchase->receive_progress }}% Received</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $purchase->formatted_total }}</strong>
                                @if($purchase->paid_amount > 0)
                                <br><small class="text-success">Paid: {{ $purchase->currency }} {{ number_format($purchase->paid_amount, 2) }}</small>
                                <br><small class="text-{{ ($purchase->total_amount - $purchase->paid_amount) > 0 ? 'danger' : 'success' }}">
                                    Balance: {{ $purchase->currency }} {{ number_format($purchase->total_amount - $purchase->paid_amount, 2) }}
                                </small>
                                @else
                                <br><small class="text-danger">Unpaid</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $purchase->status }}">{{ $purchase->status_label }}</span>
                            @if($purchase->status == 'pending' && $purchase->requires_approval)
                            <br><small class="text-muted">Awaiting approval</small>
                            @elseif($purchase->status == 'ordered')
                            <br><small class="text-muted">In progress</small>
                            @elseif($purchase->status == 'received')
                            <br><small class="text-success">Completed</small>
                            @endif
                        </td>
                        <td>
                            <div>
                                <strong>Ordered:</strong> {{ $purchase->purchase_date->format('M d, Y') }}
                                @if($purchase->expected_delivery_date)
                                <br><small class="text-muted">Expected: {{ $purchase->expected_delivery_date->format('M d, Y') }}</small>
                                @endif
                                @if($purchase->actual_delivery_date)
                                <br><small class="text-success">Delivered: {{ $purchase->actual_delivery_date->format('M d, Y') }}</small>
                                @elseif($purchase->expected_delivery_date)
                                    @if($purchase->is_overdue)
                                    <br><small class="text-danger">{{ $purchase->days_overdue }} days overdue</small>
                                    @else
                                    @php
                                        $daysRemaining = now()->diffInDays($purchase->expected_delivery_date, false);
                                    @endphp
                                    @if($daysRemaining >= 0)
                                    <br><small class="text-info">{{ $daysRemaining }} days remaining</small>
                                    @endif
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('purchases.show', $purchase) }}">
                                        <i class="fas fa-eye"></i> View Details
                                    </a></li>
                                    @if($purchase->can_be_edited)
                                    <li><a class="dropdown-item" href="{{ route('purchases.edit', $purchase) }}">
                                        <i class="fas fa-edit"></i> Edit Order
                                    </a></li>
                                    @endif
                                    @if($purchase->can_be_approved)
                                    <li><a class="dropdown-item" href="#" onclick="approveOrder('{{ $purchase->id }}')">
                                        <i class="fas fa-check"></i> Approve Order
                                    </a></li>
                                    @endif
                                    @if($purchase->status != 'received' && $purchase->status != 'cancelled')
                                    <li><a class="dropdown-item" href="#" onclick="receiveOrder('{{ $purchase->id }}')">
                                        <i class="fas fa-box"></i> Mark Received
                                    </a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('purchases.print', $purchase) }}">
                                        <i class="fas fa-print"></i> Print Order
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="duplicateOrder('{{ $purchase->id }}')">
                                        <i class="fas fa-copy"></i> Duplicate
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if($purchase->can_be_cancelled)
                                    <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder('{{ $purchase->id }}')">
                                        <i class="fas fa-times"></i> Cancel Order
                                    </a></li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>No Purchase Orders Found</h5>
                                <p>{{ request()->hasAny(['search', 'status', 'supplier_id', 'priority']) ? 'No orders match your current filters.' : 'Start by creating your first purchase order.' }}</p>
                                <a href="{{ route('purchases.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Create Purchase Order
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="text-muted">
                Showing {{ $purchases->firstItem() }}-{{ $purchases->lastItem() }} of {{ $purchases->total() }} purchase orders
            </div>
            {{ $purchases->links() }}
        </div>
        @endif
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
                    <button class="list-group-item list-group-item-action text-danger" onclick="bulkCancel()">
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
});

function bindEventListeners() {
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.purchase-checkbox').prop('checked', this.checked);
        updateBulkActionsButton();
    });

    // Individual checkboxes
    $(document).on('change', '.purchase-checkbox', function() {
        updateBulkActionsButton();

        // Update select all checkbox
        const totalCheckboxes = $('.purchase-checkbox').length;
        const checkedCheckboxes = $('.purchase-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Row click to navigate
    $('.purchase-row').on('click', function(e) {
        if (!$(e.target).is('input, button, a, .dropdown-toggle')) {
            const purchaseId = $(this).data('id');
            window.location.href = '{{ route("purchases.show", "") }}/' + purchaseId;
        }
    });
}

function updateBulkActionsButton() {
    const selectedCount = $('.purchase-checkbox:checked').length;
    if (selectedCount > 0) {
        if (!$('#bulkActionsBtn').length) {
            $('.table-header .col-md-6:last-child .btn-group').append(
                '<button type="button" class="btn btn-warning btn-sm ms-2" id="bulkActionsBtn" onclick="showBulkActions()">' +
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

function toggleBulkActions() {
    if ($('.purchase-checkbox:checked').length === 0) {
        alert('Please select at least one purchase order.');
        return;
    }
    showBulkActions();
}

function approveOrder(purchaseId) {
    if (confirm('Are you sure you want to approve this purchase order?')) {
        $.post('{{ route("purchases.approve", "") }}/' + purchaseId, {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred while approving the order.');
        });
    }
}

function receiveOrder(purchaseId) {
    if (confirm('Mark this purchase order as received?')) {
        $.post('{{ route("purchases.receive", "") }}/' + purchaseId, {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred while marking the order as received.');
        });
    }
}

function cancelOrder(purchaseId) {
    const reason = prompt('Please provide a reason for cancellation:');
    if (reason && confirm('Are you sure you want to cancel this purchase order? This action cannot be undone.')) {
        $.post('{{ route("purchases.cancel", "") }}/' + purchaseId, {
            _token: '{{ csrf_token() }}',
            reason: reason
        }).done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred while cancelling the order.');
        });
    }
}

function duplicateOrder(purchaseId) {
    if (confirm('Create a duplicate of this purchase order?')) {
        $.post('{{ route("purchases.duplicate", "") }}/' + purchaseId, {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                window.location.href = '{{ route("purchases.edit", "") }}/' + response.new_purchase_id;
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred while duplicating the order.');
        });
    }
}

function bulkApprove() {
    const selected = $('.purchase-checkbox:checked').map(function() { return this.value; }).get();
    if (confirm(`Are you sure you want to approve ${selected.length} selected purchase orders?`)) {
        $.post('{{ route("purchases.bulk-approve") }}', {
            _token: '{{ csrf_token() }}',
            purchase_ids: selected
        }).done(function(response) {
            if (response.success) {
                $('#bulkActionsModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred during bulk approval.');
        });
    }
}

function bulkReceive() {
    const selected = $('.purchase-checkbox:checked').map(function() { return this.value; }).get();
    if (confirm(`Mark ${selected.length} selected purchase orders as received?`)) {
        $.post('{{ route("purchases.bulk-receive") }}', {
            _token: '{{ csrf_token() }}',
            purchase_ids: selected
        }).done(function(response) {
            if (response.success) {
                $('#bulkActionsModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred during bulk receive.');
        });
    }
}

function bulkExport() {
    const selected = $('.purchase-checkbox:checked').map(function() { return this.value; }).get();
    const form = $('<form method="POST" action="{{ route("purchases.bulk-export") }}">');
    form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
    selected.forEach(id => {
        form.append('<input type="hidden" name="purchase_ids[]" value="' + id + '">');
    });
    $('body').append(form);
    form.submit();
    form.remove();
    $('#bulkActionsModal').modal('hide');
}

function bulkCancel() {
    const selected = $('.purchase-checkbox:checked').map(function() { return this.value; }).get();
    const reason = prompt('Please provide a reason for cancellation:');
    if (reason && confirm(`Are you sure you want to cancel ${selected.length} selected purchase orders? This action cannot be undone.`)) {
        $.post('{{ route("purchases.bulk-cancel") }}', {
            _token: '{{ csrf_token() }}',
            purchase_ids: selected,
            reason: reason
        }).done(function(response) {
            if (response.success) {
                $('#bulkActionsModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred during bulk cancellation.');
        });
    }
}
</script>
@endpush