@extends('layouts.app')

@section('title', 'Shipment Global Orders')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">
                        <i class="bi bi-globe me-2"></i>Shipment Global Orders
                    </h1>
                    <p class="text-muted mb-0">View and manage orders from BH Bazar Shipment Global</p>
                </div>
                <div>
                    <form method="POST" action="{{ route('shipment-global.refresh') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!$success && isset($error))
        <div class="alert alert-danger" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Error Loading Data</h5>
            <p class="mb-0">{{ $error }}</p>
        </div>
    @endif

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>Orders List
                @if(is_array($orders) || $orders instanceof \Countable)
                    <span class="badge bg-primary ms-2">{{ count($orders) }} orders</span>
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if($success && !empty($orders))
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="ordersTable">
                        <thead class="table-light">
                            <tr>
                                <th>AWB No.</th>
                                <th>Creation Date</th>
                                <th>ORDER ID</th>
                                <th>PRODUCT DETAILS</th>
                                <th>PAYMENT</th>
                                <th>CUSTOMER DETAILS</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>
                                    @if(isset($order['awb']) || isset($order['tracking_number']) || isset($order['awb_number']))
                                        <strong class="text-primary">
                                            {{ $order['awb'] ?? $order['tracking_number'] ?? $order['awb_number'] ?? 'N/A' }}
                                        </strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        {{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('d M Y') : (isset($order['date']) ? \Carbon\Carbon::parse($order['date'])->format('d M Y') : 'N/A') }}
                                        @if(isset($order['created_at']))
                                            <br><span class="text-muted">{{ \Carbon\Carbon::parse($order['created_at'])->format('h:i A') }}</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <strong class="text-dark">
                                        {{ $order['order_id'] ?? $order['id'] ?? $order['order_number'] ?? 'N/A' }}
                                    </strong>
                                </td>
                                <td>
                                    <div style="max-width: 250px;">
                                        @if(isset($order['product_name']) || isset($order['products']))
                                            <strong>{{ $order['product_name'] ?? 'N/A' }}</strong>
                                        @elseif(isset($order['items']) && is_array($order['items']))
                                            @foreach($order['items'] as $index => $item)
                                                @if($index < 2)
                                                    <div class="mb-1">
                                                        <small><strong>{{ $item['name'] ?? $item['product_name'] ?? 'Product' }}</strong></small>
                                                        @if(isset($item['quantity']))
                                                            <br><small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if(count($order['items']) > 2)
                                                <small class="text-muted">+{{ count($order['items']) - 2 }} more</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-success">
                                            ₹{{ number_format($order['amount'] ?? $order['total'] ?? $order['total_amount'] ?? 0, 2) }}
                                        </strong>
                                        @if(isset($order['payment_method']))
                                            <br><small class="text-muted">{{ ucfirst($order['payment_method']) }}</small>
                                        @endif
                                        @if(isset($order['payment_status']))
                                            <br><span class="badge badge-sm {{ strtolower($order['payment_status']) == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($order['payment_status']) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 200px;">
                                        <strong>{{ $order['customer_name'] ?? $order['name'] ?? $order['buyer_name'] ?? 'N/A' }}</strong>
                                        @if(isset($order['customer_phone']) || isset($order['phone']) || isset($order['mobile']))
                                            <br><small class="text-muted">
                                                <i class="bi bi-telephone"></i> {{ $order['customer_phone'] ?? $order['phone'] ?? $order['mobile'] }}
                                            </small>
                                        @endif
                                        @if(isset($order['customer_city']) || isset($order['city']))
                                            <br><small class="text-muted">
                                                <i class="bi bi-geo-alt"></i> {{ $order['customer_city'] ?? $order['city'] }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $status = strtolower($order['status'] ?? $order['order_status'] ?? 'pending');
                                        $badgeClass = match($status) {
                                            'delivered' => 'bg-success',
                                            'shipped', 'in_transit', 'in transit' => 'bg-info',
                                            'processing', 'confirmed' => 'bg-warning',
                                            'cancelled', 'failed' => 'bg-danger',
                                            'pending' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($order['status'] ?? $order['order_status'] ?? 'Pending') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" title="View Details"
                                                onclick="viewOrderDetails({{ json_encode($order) }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No Orders Found</h4>
                    <p class="text-muted">
                        @if(!$success)
                            There was an error loading the orders. Please try refreshing the page.
                        @else
                            There are no orders available at the moment.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">
                    <i class="bi bi-file-text me-2"></i>Order Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewOrderDetails(order) {
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    const content = document.getElementById('orderDetailsContent');

    let html = '<div class="row">';

    // Order Information
    html += '<div class="col-md-6 mb-3">';
    html += '<h6 class="border-bottom pb-2">Order Information</h6>';
    html += '<table class="table table-sm">';
    html += `<tr><td><strong>Order ID:</strong></td><td>${order.order_id || order.id || 'N/A'}</td></tr>`;
    html += `<tr><td><strong>Status:</strong></td><td><span class="badge bg-primary">${order.status || 'N/A'}</span></td></tr>`;
    html += `<tr><td><strong>Amount:</strong></td><td>₹${parseFloat(order.amount || order.total || 0).toFixed(2)}</td></tr>`;
    html += `<tr><td><strong>Date:</strong></td><td>${order.created_at || 'N/A'}</td></tr>`;
    html += '</table>';
    html += '</div>';

    // Customer Information
    html += '<div class="col-md-6 mb-3">';
    html += '<h6 class="border-bottom pb-2">Customer Information</h6>';
    html += '<table class="table table-sm">';
    html += `<tr><td><strong>Name:</strong></td><td>${order.customer_name || order.name || 'N/A'}</td></tr>`;
    html += `<tr><td><strong>Phone:</strong></td><td>${order.customer_phone || order.phone || 'N/A'}</td></tr>`;
    if (order.customer_email || order.email) {
        html += `<tr><td><strong>Email:</strong></td><td>${order.customer_email || order.email}</td></tr>`;
    }
    if (order.customer_address || order.address) {
        html += `<tr><td><strong>Address:</strong></td><td>${order.customer_address || order.address}</td></tr>`;
    }
    html += '</table>';
    html += '</div>';

    // Shipping Information
    if (order.tracking_number || order.courier || order.awb) {
        html += '<div class="col-12 mb-3">';
        html += '<h6 class="border-bottom pb-2">Shipping Information</h6>';
        html += '<table class="table table-sm">';
        if (order.tracking_number || order.awb) {
            html += `<tr><td><strong>Tracking Number:</strong></td><td>${order.tracking_number || order.awb || 'N/A'}</td></tr>`;
        }
        if (order.courier) {
            html += `<tr><td><strong>Courier:</strong></td><td>${order.courier}</td></tr>`;
        }
        if (order.shipped_date) {
            html += `<tr><td><strong>Shipped Date:</strong></td><td>${order.shipped_date}</td></tr>`;
        }
        if (order.expected_delivery) {
            html += `<tr><td><strong>Expected Delivery:</strong></td><td>${order.expected_delivery}</td></tr>`;
        }
        html += '</table>';
        html += '</div>';
    }

    // Additional Details
    html += '<div class="col-12">';
    html += '<h6 class="border-bottom pb-2">Additional Details</h6>';
    html += '<pre class="bg-light p-3 rounded"><code>' + JSON.stringify(order, null, 2) + '</code></pre>';
    html += '</div>';

    html += '</div>';

    content.innerHTML = html;
    modal.show();
}

// Initialize DataTable if available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.DataTable !== 'undefined' && document.getElementById('ordersTable')) {
        $('#ordersTable').DataTable({
            order: [[1, 'desc']], // Sort by Creation Date column
            pageLength: 25,
            responsive: true,
            language: {
                search: "Search orders:",
                lengthMenu: "Show _MENU_ orders per page"
            },
            columnDefs: [
                { orderable: false, targets: 7 } // Disable sorting on Actions column
            ]
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}

#orderDetailsContent pre {
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endpush
