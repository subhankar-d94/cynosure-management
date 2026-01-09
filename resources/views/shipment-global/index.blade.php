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
                                    @if(isset($order['Awb_Number']) && $order['Awb_Number'])
                                        <strong class="text-primary">{{ $order['Awb_Number'] }}</strong>
                                    @else
                                        <span class="badge bg-secondary">Not Generated</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        {{ isset($order['order_date']) ? \Carbon\Carbon::parse($order['order_date'])->format('d M Y') : 'N/A' }}
                                        @if(isset($order['order_date']))
                                            <br><span class="text-muted">{{ \Carbon\Carbon::parse($order['order_date'])->format('h:i A') }}</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-dark">{{ $order['id'] ?? 'N/A' }}</strong>
                                        @if(isset($order['channel_order_id']))
                                            <br><small class="text-muted">{{ $order['channel_order_id'] }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 250px;">
                                        @if(isset($order['order_item_name']))
                                            <strong>{{ $order['order_item_name'] }}</strong>
                                            @if(isset($order['order_item_sku']))
                                                <br><small class="text-muted">SKU: {{ $order['order_item_sku'] }}</small>
                                            @endif
                                            @if(isset($order['order_item_units']))
                                                <br><small class="text-muted">Qty: {{ $order['order_item_units'] }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-success">
                                            ₹{{ number_format($order['sub_total'] ?? 0, 2) }}
                                        </strong>
                                        @if(isset($order['payment_method']))
                                            <br><span class="badge {{ strtolower($order['payment_method']) == 'prepaid' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $order['payment_method'] }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 200px;">
                                        <strong>{{ $order['billing_customer_name'] ?? 'N/A' }}</strong>
                                        @if(isset($order['billing_phone']))
                                            <br><small class="text-muted">
                                                <i class="bi bi-telephone"></i> {{ $order['billing_phone'] }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $status = strtolower($order['status'] ?? 'new');
                                        $badgeClass = match($status) {
                                            'delivered' => 'bg-success',
                                            'shipped', 'in_transit', 'in transit' => 'bg-info',
                                            'processing', 'confirmed' => 'bg-warning',
                                            'cancelled', 'failed' => 'bg-danger',
                                            'new', 'pending' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $order['status'] ?? 'NEW' }}</span>
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
    html += '<h6 class="border-bottom pb-2"><i class="bi bi-info-circle me-2"></i>Order Information</h6>';
    html += '<table class="table table-sm table-borderless">';
    html += `<tr><td width="40%"><strong>Order ID:</strong></td><td><span class="text-primary">${order.id || 'N/A'}</span></td></tr>`;
    if (order.channel_order_id) {
        html += `<tr><td><strong>Channel Order ID:</strong></td><td>${order.channel_order_id}</td></tr>`;
    }
    html += `<tr><td><strong>AWB Number:</strong></td><td><strong>${order.Awb_Number || '<span class="badge bg-secondary">Not Generated</span>'}</strong></td></tr>`;
    html += `<tr><td><strong>Status:</strong></td><td><span class="badge bg-primary">${order.status || 'NEW'}</span></td></tr>`;
    html += `<tr><td><strong>Order Date:</strong></td><td>${order.order_date || 'N/A'}</td></tr>`;
    if (order.service_provider) {
        html += `<tr><td><strong>Service Provider:</strong></td><td>${order.service_provider}</td></tr>`;
    }
    html += '</table>';
    html += '</div>';

    // Payment Information
    html += '<div class="col-md-6 mb-3">';
    html += '<h6 class="border-bottom pb-2"><i class="bi bi-cash me-2"></i>Payment Information</h6>';
    html += '<table class="table table-sm table-borderless">';
    html += `<tr><td width="40%"><strong>Sub Total:</strong></td><td><strong class="text-success">₹${parseFloat(order.sub_total || 0).toFixed(2)}</strong></td></tr>`;
    html += `<tr><td><strong>Payment Method:</strong></td><td>`;
    if (order.payment_method) {
        const paymentBadge = order.payment_method.toLowerCase() === 'prepaid' ? 'bg-success' : 'bg-warning';
        html += `<span class="badge ${paymentBadge}">${order.payment_method}</span>`;
    } else {
        html += 'N/A';
    }
    html += `</td></tr>`;
    html += '</table>';
    html += '</div>';

    // Customer Information
    html += '<div class="col-md-6 mb-3">';
    html += '<h6 class="border-bottom pb-2"><i class="bi bi-person me-2"></i>Customer Information</h6>';
    html += '<table class="table table-sm table-borderless">';
    html += `<tr><td width="40%"><strong>Name:</strong></td><td>${order.billing_customer_name || 'N/A'}</td></tr>`;
    html += `<tr><td><strong>Phone:</strong></td><td><i class="bi bi-telephone"></i> ${order.billing_phone || 'N/A'}</td></tr>`;
    html += '</table>';
    html += '</div>';

    // Product Details
    html += '<div class="col-md-6 mb-3">';
    html += '<h6 class="border-bottom pb-2"><i class="bi bi-box me-2"></i>Product Details</h6>';
    html += '<table class="table table-sm table-borderless">';
    html += `<tr><td width="40%"><strong>Product Name:</strong></td><td>${order.order_item_name || 'N/A'}</td></tr>`;
    if (order.order_item_sku) {
        html += `<tr><td><strong>SKU:</strong></td><td><span class="badge bg-secondary">${order.order_item_sku}</span></td></tr>`;
    }
    html += `<tr><td><strong>Quantity:</strong></td><td>${order.order_item_units || 'N/A'}</td></tr>`;
    html += '</table>';
    html += '</div>';

    // Shipping/Package Information
    if (order.length || order.breadth || order.height || order.weight) {
        html += '<div class="col-12 mb-3">';
        html += '<h6 class="border-bottom pb-2"><i class="bi bi-box-seam me-2"></i>Package Dimensions</h6>';
        html += '<table class="table table-sm table-borderless">';
        html += '<tr>';
        if (order.length) html += `<td><strong>Length:</strong> ${order.length} cm</td>`;
        if (order.breadth) html += `<td><strong>Breadth:</strong> ${order.breadth} cm</td>`;
        if (order.height) html += `<td><strong>Height:</strong> ${order.height} cm</td>`;
        if (order.weight) html += `<td><strong>Weight:</strong> ${order.weight} kg</td>`;
        html += '</tr>';
        html += '</table>';
        html += '</div>';
    }

    // Action Flags
    html += '<div class="col-12 mb-3">';
    html += '<h6 class="border-bottom pb-2"><i class="bi bi-flag me-2"></i>Order Flags</h6>';
    html += '<div class="d-flex gap-2">';
    if (order.canBeCancelled) {
        html += '<span class="badge bg-info">Can Be Cancelled</span>';
    }
    if (order.shipNowFlag) {
        html += '<span class="badge bg-success">Ready to Ship</span>';
    }
    if (order.labelPinrtFlag) {
        html += '<span class="badge bg-primary">Label Printable</span>';
    }
    if (order.new_label_pdf_url) {
        html += `<a href="${order.new_label_pdf_url}" target="_blank" class="badge bg-warning text-decoration-none">View Label PDF</a>`;
    }
    html += '</div>';
    html += '</div>';

    // Raw JSON Data
    html += '<div class="col-12">';
    html += '<h6 class="border-bottom pb-2"><i class="bi bi-code-square me-2"></i>Complete Order Data (JSON)</h6>';
    html += '<pre class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;"><code>' + JSON.stringify(order, null, 2) + '</code></pre>';
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
