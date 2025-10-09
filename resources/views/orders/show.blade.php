@extends('layouts.app')

@php
    function formatDate($date, $default = 'N/A') {
        if (!$date) return $default;
        if (is_string($date)) {
            try {
                return \Carbon\Carbon::parse($date)->format('M d, Y h:i A');
            } catch (\Exception $e) {
                return $default;
            }
        }
        return $date->format('M d, Y h:i A');
    }
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">Order #{{ $order->order_number }}</h3>
                        <small class="text-muted">Created on {{ formatDate($order->created_at) }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if($order->status !== 'cancelled')
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}">
                                    <i class="fas fa-edit"></i> Edit Order
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="duplicateOrder()">
                                    <i class="fas fa-copy"></i> Duplicate Order
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="printOrder()">
                                    <i class="fas fa-print"></i> Print Order
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="emailOrder()">
                                    <i class="fas fa-envelope"></i> Email to Customer
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder()">
                                    <i class="fas fa-times"></i> Cancel Order
                                </a></li>
                            </ul>
                        </div>
                        @endif
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{
                                    $order->status === 'completed' ? 'success' :
                                    ($order->status === 'in_progress' ? 'warning' :
                                    ($order->status === 'cancelled' ? 'danger' : 'primary'))
                                }} fs-6 me-2">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                                <span class="badge bg-{{
                                    $order->priority === 'urgent' ? 'danger' :
                                    ($order->priority === 'high' ? 'warning' : 'secondary')
                                }}">
                                    {{ ucfirst($order->priority) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <strong>Order Date:</strong><br>
                            <span class="text-muted">{{ $order->order_date }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Expected Delivery:</strong><br>
                            <span class="text-muted">{{ $order->expected_delivery ?? 'Not specified' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Amount:</strong><br>
                            <h5 class="text-success mb-0">₹{{ number_format($order->total_amount, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Customer Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Customer Information</h5>
                            @if($order->customer_id)
                            <a href="{{ route('customers.show', $order->customer_id) }}" class="btn btn-sm btn-outline-primary">
                                View Profile
                            </a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($order->customer_id)
                            <div class="d-flex align-items-start">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($order->customer_name, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $order->customer_name }}</h6>
                                    <p class="text-muted mb-2">
                                        @if($order->customer_phone)
                                        <i class="fas fa-phone"></i> {{ $order->customer_phone }}<br>
                                        @endif
                                        @if($order->customer_email)
                                        <i class="fas fa-envelope"></i> {{ $order->customer_email }}<br>
                                        @endif
                                        @if($order->delivery_address)
                                        <i class="fas fa-map-marker-alt"></i> {{ $order->delivery_address }}
                                        @if($order->delivery_city), {{ $order->delivery_city }}@endif
                                        @if($order->delivery_state), {{ $order->delivery_state }}@endif
                                        @if($order->delivery_postal_code) {{ $order->delivery_postal_code }}@endif
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @elseif($order->customer_name)
                            <div class="d-flex align-items-start">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($order->customer_name, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $order->customer_name }}</h6>
                                    <p class="text-muted mb-2">
                                        @if($order->customer_phone)
                                        <i class="fas fa-phone"></i> {{ $order->customer_phone }}<br>
                                        @endif
                                        @if($order->customer_email)
                                        <i class="fas fa-envelope"></i> {{ $order->customer_email }}<br>
                                        @endif
                                        @if($order->customer_address)
                                        <i class="fas fa-map-marker-alt"></i> {{ $order->customer_address }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-3">
                                <i class="fas fa-user-circle fa-3x text-muted mb-2"></i>
                                <h6>Walk-in Customer</h6>
                                <p class="text-muted mb-0">No customer profile linked</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Payment Method:</strong><br>
                                    @if($order->payment_method)
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-{{
                                            $order->payment_method === 'card' ? 'credit-card' :
                                            ($order->payment_method === 'bank_transfer' ? 'university' :
                                            ($order->payment_method === 'upi' ? 'mobile-alt' : 'money-bill'))
                                        }}"></i>
                                        {{ ucwords(str_replace('_', ' ', $order->payment_method)) }}
                                    </span>
                                    @else
                                    <span class="text-muted">Not specified</span>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <strong>Payment Status:</strong><br>
                                    <span class="badge bg-{{
                                        $order->payment_status === 'paid' ? 'success' :
                                        ($order->payment_status === 'partial' ? 'warning' : 'danger')
                                    }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Total Amount:</strong><br>
                                    <span class="h5 text-primary">₹{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Paid Amount:</strong><br>
                                    <span class="h5 text-success">₹{{ number_format($order->paid_amount, 2) }}</span>
                                </div>
                            </div>
                            @if($order->total_amount > $order->paid_amount)
                            <div class="mt-3">
                                <div class="alert alert-warning">
                                    <strong>Balance Due:</strong> ₹{{ number_format($order->total_amount - $order->paid_amount, 2) }}
                                </div>
                                <button type="button" class="btn btn-success btn-sm" onclick="recordPayment()">
                                    <i class="fas fa-plus"></i> Record Payment
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Order Items</h5>
                    <span class="badge bg-primary">{{ count($order->items) }} Items</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($order->items) > 0)
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="product-image me-2">
                                                    <div class="placeholder-image">
                                                        <i class="fas fa-cube"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $item->product_name }}</strong><br>
                                                    <small class="text-muted">{{ $item->product_description }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code>{{ $item->product_sku }}</code></td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₹{{ number_format($item->price, 2) }}</td>
                                        <td>₹{{ number_format($item->discount, 2) }}</td>
                                        <td><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No items found in this order</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Summary -->
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td class="text-end">₹{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Discount:</strong></td>
                                    <td class="text-end text-success">-₹{{ number_format($order->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tax:</strong></td>
                                    <td class="text-end">₹{{ number_format($order->tax, 2) }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Order Notes -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Order Notes</h5>
                        </div>
                        <div class="card-body">
                            @if($order->notes)
                                <p class="mb-0">{{ $order->notes }}</p>
                            @else
                                <p class="text-muted mb-0">No notes for this order.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Order Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Order Created</h6>
                                        <small class="text-muted">{{ formatDate($order->created_at) }}</small>
                                    </div>
                                </div>
                                @if($order->payment_status === 'paid' || $order->payment_status === 'partial')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Payment Received</h6>
                                        <small class="text-muted">{{ formatDate($order->updated_at) }}</small>
                                    </div>
                                </div>
                                @endif
                                @if($order->status === 'in_progress' || $order->status === 'completed')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Processing Started</h6>
                                        <small class="text-muted">{{ formatDate($order->updated_at) }}</small>
                                    </div>
                                </div>
                                @endif
                                @if($order->status === 'completed')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Order Completed</h6>
                                        <small class="text-muted">{{ formatDate($order->updated_at) }}</small>
                                    </div>
                                </div>
                                @else
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-light"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-muted">Processing</h6>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-light"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-muted">Completed</h6>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="updateStatus()">
                                <i class="fas fa-sync"></i> Update Status
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-success w-100" onclick="addTracking()">
                                <i class="fas fa-truck"></i> Add Tracking
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-info w-100" onclick="sendNotification()">
                                <i class="fas fa-bell"></i> Notify Customer
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-warning w-100" onclick="generateInvoice()">
                                <i class="fas fa-file-invoice"></i> Generate Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_note" class="form-label">Note (optional)</label>
                        <textarea class="form-control" id="status_note" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount</label>
                        <input type="number" step="0.01" class="form-control" id="payment_amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method_new" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method_new" name="payment_method">
                            <option value="cash">Cash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_note" class="form-label">Note (optional)</label>
                        <textarea class="form-control" id="payment_note" name="note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 18px;
}

.product-image {
    width: 40px;
    height: 40px;
}

.placeholder-image {
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 1px #dee2e6;
}

.timeline-content {
    padding-left: 20px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Update Status
    window.updateStatus = function() {
        $('#status').val('{{ $order->status }}');
        $('#statusModal').modal('show');
    };

    $('#statusForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            status: $('#status').val(),
            note: $('#status_note').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route("orders.update-status", $order->id) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#statusModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error updating status: ' + xhr.responseJSON.message);
            }
        });
    });

    // Record Payment
    window.recordPayment = function() {
        const balance = {{ $order->total_amount - $order->paid_amount }};
        $('#payment_amount').val(balance.toFixed(2));
        $('#paymentModal').modal('show');
    };

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            amount: $('#payment_amount').val(),
            payment_method: $('#payment_method_new').val(),
            note: $('#payment_note').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route("orders.record-payment", $order->id) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#paymentModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error recording payment: ' + xhr.responseJSON.message);
            }
        });
    });

    // Other actions
    window.duplicateOrder = function() {
        if (confirm('Create a duplicate of this order?')) {
            window.location.href = '{{ route("orders.duplicate", $order->id) }}';
        }
    };

    window.cancelOrder = function() {
        if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
            $.ajax({
                url: '{{ route("orders.cancel", $order->id) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                }
            });
        }
    };

    window.printOrder = function() {
        window.open('{{ route("orders.print", $order->id) }}', '_blank');
    };

    window.emailOrder = function() {
        $.ajax({
            url: '{{ route("orders.email", $order->id) }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                alert('Order details sent to customer successfully!');
            }
        });
    };

    window.addTracking = function() {
        const tracking = prompt('Enter tracking number:');
        if (tracking) {
            $.ajax({
                url: '{{ route("orders.add-tracking", $order->id) }}',
                method: 'POST',
                data: {
                    tracking_number: tracking,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Tracking number added successfully!');
                    location.reload();
                }
            });
        }
    };

    window.sendNotification = function() {
        $.ajax({
            url: '{{ route("orders.notify", $order->id) }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                alert('Notification sent to customer!');
            }
        });
    };

    window.generateInvoice = function() {
        window.open('{{ route("orders.invoice", $order->id) }}', '_blank');
    };
});
</script>
@endpush