@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">Order #{{ $order->order_number ?? 'ORD-2024-12345' }}</h3>
                        <small class="text-muted">Created on {{ $order->created_at->format('M d, Y h:i A') ?? 'Jan 15, 2024 10:30 AM' }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if(($order->status ?? 'pending') !== 'cancelled')
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('orders.edit', $order->id ?? 1) }}">
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
                                    ($order->status ?? 'pending') === 'completed' ? 'success' :
                                    (($order->status ?? 'pending') === 'processing' ? 'warning' :
                                    (($order->status ?? 'pending') === 'cancelled' ? 'danger' : 'primary'))
                                }} fs-6 me-2">
                                    {{ ucfirst($order->status ?? 'pending') }}
                                </span>
                                <span class="badge bg-{{
                                    ($order->priority ?? 'medium') === 'urgent' ? 'danger' :
                                    (($order->priority ?? 'medium') === 'high' ? 'warning' : 'secondary')
                                }}">
                                    {{ ucfirst($order->priority ?? 'medium') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <strong>Order Date:</strong><br>
                            <span class="text-muted">{{ $order->order_date ?? '2024-01-15' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Expected Delivery:</strong><br>
                            <span class="text-muted">{{ $order->expected_delivery ?? 'Not specified' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Amount:</strong><br>
                            <h5 class="text-success mb-0">${{ number_format($order->total ?? 1299.99, 2) }}</h5>
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
                            @if(isset($order->customer))
                            <a href="{{ route('customers.show', $order->customer->id) }}" class="btn btn-sm btn-outline-primary">
                                View Profile
                            </a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if(isset($order->customer))
                            <div class="d-flex align-items-start">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($order->customer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $order->customer->name }}</h6>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-phone"></i> {{ $order->customer->phone }}<br>
                                        <i class="fas fa-envelope"></i> {{ $order->customer->email }}<br>
                                        <i class="fas fa-map-marker-alt"></i> {{ $order->customer->address }}
                                    </p>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-light text-dark">{{ $order->customer->orders_count ?? 15 }} Orders</span>
                                        <span class="badge bg-light text-dark">${{ number_format($order->customer->total_spent ?? 5429.50, 2) }} Total</span>
                                    </div>
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
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-{{
                                            ($order->payment_method ?? 'cash') === 'card' ? 'credit-card' :
                                            (($order->payment_method ?? 'cash') === 'bank_transfer' ? 'university' : 'money-bill')
                                        }}"></i>
                                        {{ ucwords(str_replace('_', ' ', $order->payment_method ?? 'cash')) }}
                                    </span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Payment Status:</strong><br>
                                    <span class="badge bg-{{
                                        ($order->payment_status ?? 'pending') === 'paid' ? 'success' :
                                        (($order->payment_status ?? 'pending') === 'partial' ? 'warning' : 'danger')
                                    }}">
                                        {{ ucfirst($order->payment_status ?? 'pending') }}
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Total Amount:</strong><br>
                                    <span class="h5 text-primary">${{ number_format($order->total ?? 1299.99, 2) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Paid Amount:</strong><br>
                                    <span class="h5 text-success">${{ number_format($order->paid_amount ?? 1299.99, 2) }}</span>
                                </div>
                            </div>
                            @if(($order->total ?? 1299.99) > ($order->paid_amount ?? 1299.99))
                            <div class="mt-3">
                                <div class="alert alert-warning">
                                    <strong>Balance Due:</strong> ${{ number_format(($order->total ?? 1299.99) - ($order->paid_amount ?? 1299.99), 2) }}
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
                    <span class="badge bg-primary">{{ $order->items->count() ?? 3 }} Items</span>
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
                                @if(isset($order->items) && $order->items->count() > 0)
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
                                                    <strong>{{ $item->product->name ?? 'Sample Product' }}</strong><br>
                                                    <small class="text-muted">{{ $item->product->description ?? 'Product description' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code>{{ $item->product->sku ?? 'SKU-001' }}</code></td>
                                        <td>{{ $item->quantity ?? 2 }}</td>
                                        <td>${{ number_format($item->price ?? 599.99, 2) }}</td>
                                        <td>${{ number_format($item->discount ?? 0, 2) }}</td>
                                        <td><strong>${{ number_format($item->total ?? 1199.98, 2) }}</strong></td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="product-image me-2">
                                                    <div class="placeholder-image">
                                                        <i class="fas fa-cube"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>Sample Product A</strong><br>
                                                    <small class="text-muted">High-quality sample product</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code>SKU-001</code></td>
                                        <td>2</td>
                                        <td>$599.99</td>
                                        <td>$0.00</td>
                                        <td><strong>$1,199.98</strong></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="product-image me-2">
                                                    <div class="placeholder-image">
                                                        <i class="fas fa-cube"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>Sample Product B</strong><br>
                                                    <small class="text-muted">Premium sample product</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code>SKU-002</code></td>
                                        <td>1</td>
                                        <td>$99.99</td>
                                        <td>$0.00</td>
                                        <td><strong>$99.99</strong></td>
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
                                    <td class="text-end">${{ number_format($order->subtotal ?? 1299.97, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Discount:</strong></td>
                                    <td class="text-end text-success">-${{ number_format($order->discount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tax (8%):</strong></td>
                                    <td class="text-end">${{ number_format($order->tax ?? 103.98, 2) }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong>${{ number_format($order->total ?? 1403.95, 2) }}</strong></td>
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
                            @if($order->notes ?? 'Customer requested expedited shipping. Handle with care.')
                                <p class="mb-0">{{ $order->notes ?? 'Customer requested expedited shipping. Handle with care.' }}</p>
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
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y h:i A') ?? 'Jan 15, 2024 10:30 AM' }}</small>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Payment Received</h6>
                                        <small class="text-muted">{{ $order->updated_at->format('M d, Y h:i A') ?? 'Jan 15, 2024 10:35 AM' }}</small>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Processing Started</h6>
                                        <small class="text-muted">Jan 15, 2024 2:15 PM</small>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-light"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-muted">Shipping</h6>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-light"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-muted">Delivered</h6>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
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
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
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
        $('#status').val('{{ $order->status ?? "pending" }}');
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
            url: '{{ route("orders.update-status", $order->id ?? 1) }}',
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
        const balance = {{ ($order->total ?? 1299.99) - ($order->paid_amount ?? 0) }};
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
            url: '{{ route("orders.record-payment", $order->id ?? 1) }}',
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
            window.location.href = '{{ route("orders.duplicate", $order->id ?? 1) }}';
        }
    };

    window.cancelOrder = function() {
        if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
            $.ajax({
                url: '{{ route("orders.cancel", $order->id ?? 1) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                }
            });
        }
    };

    window.printOrder = function() {
        window.open('{{ route("orders.print", $order->id ?? 1) }}', '_blank');
    };

    window.emailOrder = function() {
        $.ajax({
            url: '{{ route("orders.email", $order->id ?? 1) }}',
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
                url: '{{ route("orders.add-tracking", $order->id ?? 1) }}',
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
            url: '{{ route("orders.notify", $order->id ?? 1) }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                alert('Notification sent to customer!');
            }
        });
    };

    window.generateInvoice = function() {
        window.open('{{ route("orders.invoice", $order->id ?? 1) }}', '_blank');
    };
});
</script>
@endpush