@extends('layouts.app')

@section('title', 'Shipment Global Order Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">
                        <i class="bi bi-file-text me-2"></i>Order Details
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('shipment-global.index') }}">Shipment Global</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Order #{{ $order['id'] ?? 'N/A' }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('shipment-global.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($success)
        <div class="row">
            <!-- Order Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Order Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Order ID:</th>
                                <td><strong class="text-primary">{{ $order['order_id'] ?? $order['id'] ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @php
                                        $status = strtolower($order['status'] ?? 'pending');
                                        $badgeClass = match($status) {
                                            'delivered' => 'bg-success',
                                            'shipped', 'in_transit' => 'bg-info',
                                            'processing' => 'bg-warning',
                                            'cancelled', 'failed' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($order['status'] ?? 'Pending') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td><strong class="text-success">₹{{ number_format($order['amount'] ?? $order['total'] ?? 0, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Order Date:</th>
                                <td>
                                    {{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('d M Y, h:i A') : 'N/A' }}
                                </td>
                            </tr>
                            @if(isset($order['updated_at']))
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ \Carbon\Carbon::parse($order['updated_at'])->format('d M Y, h:i A') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person me-2"></i>Customer Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Name:</th>
                                <td>{{ $order['customer_name'] ?? $order['name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>
                                    @if(isset($order['customer_phone']) || isset($order['phone']))
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $order['customer_phone'] ?? $order['phone'] }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            @if(isset($order['customer_email']) || isset($order['email']))
                            <tr>
                                <th>Email:</th>
                                <td>
                                    <i class="bi bi-envelope me-1"></i>
                                    {{ $order['customer_email'] ?? $order['email'] }}
                                </td>
                            </tr>
                            @endif
                            @if(isset($order['customer_address']) || isset($order['address']))
                            <tr>
                                <th>Address:</th>
                                <td>{{ $order['customer_address'] ?? $order['address'] }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            @if(isset($order['tracking_number']) || isset($order['courier']) || isset($order['awb']))
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-truck me-2"></i>Shipping Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            @if(isset($order['tracking_number']) || isset($order['awb']))
                            <tr>
                                <th width="20%">Tracking Number:</th>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="bi bi-box-seam"></i>
                                        {{ $order['tracking_number'] ?? $order['awb'] }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                            @if(isset($order['courier']))
                            <tr>
                                <th>Courier Service:</th>
                                <td>{{ $order['courier'] }}</td>
                            </tr>
                            @endif
                            @if(isset($order['shipped_date']))
                            <tr>
                                <th>Shipped Date:</th>
                                <td>{{ \Carbon\Carbon::parse($order['shipped_date'])->format('d M Y, h:i A') }}</td>
                            </tr>
                            @endif
                            @if(isset($order['expected_delivery']))
                            <tr>
                                <th>Expected Delivery:</th>
                                <td>{{ \Carbon\Carbon::parse($order['expected_delivery'])->format('d M Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Raw Data -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-code-square me-2"></i>Complete Order Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($order, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <h5 class="alert-heading">Error Loading Order</h5>
            <p class="mb-0">Unable to load order details. Please try again later.</p>
        </div>
    @endif
</div>
@endsection

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

.table th {
    font-weight: 600;
    color: #495057;
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}
</style>
@endpush
