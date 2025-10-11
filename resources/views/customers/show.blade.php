@extends('layouts.app')

@section('title', $customer->name . ' - Customer Details')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
        <li class="breadcrumb-item active">{{ $customer->name }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        <span>{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h2 class="mb-1">{{ $customer->name }}</h2>
                        <div class="d-flex align-items-center">
                            <span class="badge {{ $customer->status === 'active' ? 'bg-success' : 'bg-secondary' }} me-2">
                                {{ ucfirst($customer->status) }}
                            </span>
                            <span class="text-muted">{{ $customer->customer_code }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="createOrder()">
                        <i class="bi bi-plus-lg me-1"></i>New Order
                    </button>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-primary text-uppercase mb-1">Total Orders</h6>
                            <h3 class="mb-0 text-primary">{{ $customer->orders_count ?? 0 }}</h3>
                            <small class="text-muted">Lifetime orders</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-cart3" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-success text-uppercase mb-1">Total Spent</h6>
                            <h3 class="mb-0 text-success">₹{{ number_format($customer->total_spent ?? 0, 2) }}</h3>
                            <small class="text-muted">Lifetime value</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-currency-rupee" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-info text-uppercase mb-1">Average Order</h6>
                            <h3 class="mb-0 text-info">₹{{ number_format($customer->average_order ?? 0, 2) }}</h3>
                            <small class="text-muted">Per order value</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-graph-up" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-warning text-uppercase mb-1">Last Order</h6>
                            <h3 class="mb-0 text-warning">{{ $customer->last_order_days ?? 'Never' }}</h3>
                            <small class="text-muted">Days ago</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-clock-history" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-lg-8">
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Email Address</h6>
                            <p class="mb-3">{{ $customer->email ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Phone Number</h6>
                            <p class="mb-3">{{ $customer->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Type</h6>
                            <p class="mb-3">
                                <span class="badge bg-secondary">{{ ucfirst($customer->customer_type ?? 'individual') }}</span>
                            </p>
                        </div>
                        @if($customer->customer_type === 'business' && $customer->company_name)
                        <div class="col-md-6">
                            <h6 class="text-muted">Company</h6>
                            <p class="mb-3">{{ $customer->company_name }}</p>
                        </div>
                        @endif
                        @if($customer->notes)
                        <div class="col-md-12">
                            <h6 class="text-muted">Notes</h6>
                            <p class="mb-0">{{ $customer->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Addresses -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Addresses</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="addNewAddress()">
                        <i class="bi bi-plus"></i> Add Address
                    </button>
                </div>
                <div class="card-body">
                    <div id="addressesList">
                        @if(isset($customer->addresses) && is_array($customer->addresses) && count($customer->addresses) > 0)
                            @foreach($customer->addresses as $address)
                            <div class="address-item border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi {{ ($address->type ?? '') === 'billing' ? 'bi-receipt text-primary' : (($address->type ?? '') === 'shipping' ? 'bi-truck text-info' : 'bi-geo-alt text-success') }} me-2"></i>
                                            <h6 class="mb-0">{{ $address->label ?? ucfirst($address->type ?? 'address') }}</h6>
                                            @if(($address->is_default ?? false))
                                                <span class="badge bg-primary ms-2">Default</span>
                                            @endif
                                        </div>
                                        <p class="mb-1">{{ $address->street_address ?? '' }}</p>
                                        <p class="mb-1">{{ $address->city ?? '' }}, {{ $address->state ?? '' }} {{ $address->postal_code ?? '' }}</p>
                                        <p class="mb-0 text-muted">{{ $address->country ?? '' }}</p>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary" onclick="editAddress({{ $address->id ?? 0 }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteAddress({{ $address->id ?? 0 }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted">No addresses found</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Order History</h5>
                    @if(Route::has('orders.index'))
                    <a href="{{ route('orders.index', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-outline-primary">
                        View All Orders
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($customer->orders) && is_array($customer->orders) && count($customer->orders) > 0)
                                    @foreach(array_slice($customer->orders, 0, 5) as $order)
                                    <tr>
                                        <td>
                                            @if(Route::has('orders.show'))
                                            <a href="{{ route('orders.show', $order->id ?? 0) }}" class="fw-bold text-decoration-none">
                                                #{{ $order->order_number ?? 'N/A' }}
                                            </a>
                                            @else
                                            <span class="fw-bold">#{{ $order->order_number ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($order->created_at))
                                                {{ is_string($order->created_at) ? \Carbon\Carbon::parse($order->created_at)->format('M d, Y') : $order->created_at->format('M d, Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $order->order_items_count ?? 0 }} items</td>
                                        <td>₹{{ number_format($order->total_amount ?? 0, 2) }}</td>
                                        <td>
                                            <span class="badge
                                                @switch($order->status)
                                                    @case('pending') bg-warning @break
                                                    @case('confirmed') bg-info @break
                                                    @case('processing') bg-primary @break
                                                    @case('shipped') bg-secondary @break
                                                    @case('delivered') bg-success @break
                                                    @case('cancelled') bg-danger @break
                                                    @default bg-secondary @break
                                                @endswitch
                                            ">{{ ucfirst($order->status ?? 'unknown') }}</span>
                                        </td>
                                        <td>
                                            @if(Route::has('orders.show'))
                                            <a href="{{ route('orders.show', $order->id ?? 0) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No orders found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="createOrder()">
                            <i class="bi bi-plus-lg me-1"></i>Create New Order
                        </button>
                        <button class="btn btn-outline-success" onclick="sendEmail()">
                            <i class="bi bi-envelope me-1"></i>Send Email
                        </button>
                        <button class="btn btn-outline-info" onclick="sendSMS()">
                            <i class="bi bi-chat-text me-1"></i>Send SMS
                        </button>
                        <button class="btn btn-outline-warning" onclick="exportCustomerData()">
                            <i class="bi bi-download me-1"></i>Export Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Credit Limit</h6>
                        <p class="mb-0">₹{{ number_format($customer->credit_limit ?? 0, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Payment Terms</h6>
                        <p class="mb-0">{{ $customer->payment_terms ?? 30 }} days</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Discount</h6>
                        <p class="mb-0">{{ $customer->discount_percentage ?? 0 }}%</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Preferred Contact</h6>
                        <p class="mb-0">{{ ucfirst($customer->preferred_contact_method ?? 'email') }}</p>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notification Preferences</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox"
                               {{ $customer->email_notifications ? 'checked' : '' }} disabled>
                        <label class="form-check-label">Email Notifications</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox"
                               {{ $customer->sms_notifications ? 'checked' : '' }} disabled>
                        <label class="form-check-label">SMS Notifications</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox"
                               {{ $customer->marketing_emails ? 'checked' : '' }} disabled>
                        <label class="form-check-label">Marketing Emails</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAddressForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Address Type</label>
                            <select class="form-select" name="type" required>
                                <option value="billing">Billing</option>
                                <option value="shipping">Shipping</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address Label</label>
                            <input type="text" class="form-control" name="label" placeholder="Home, Office, etc.">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Street Address</label>
                            <input type="text" class="form-control" name="street_address" placeholder="House/Building number, Street name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" placeholder="City name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" placeholder="State name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PIN Code</label>
                            <input type="text" class="form-control" name="postal_code" placeholder="PIN code" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" value="India" required>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_default" value="1">
                                <label class="form-check-label">Set as default address</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sendEmailForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <input type="email" class="form-control" value="{{ $customer->email }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentCustomerId = {{ $customer->id }};

$(document).ready(function() {
    // Add address form submission
    $('#addAddressForm').on('submit', function(e) {
        e.preventDefault();
        submitAddAddress();
    });

    // Send email form submission
    $('#sendEmailForm').on('submit', function(e) {
        e.preventDefault();
        submitSendEmail();
    });
});

function createOrder() {
    window.location.href = `/orders/create?customer_id=${currentCustomerId}`;
}

function addNewAddress() {
    $('#addAddressModal').modal('show');
}

function submitAddAddress() {
    const formData = new FormData($('#addAddressForm')[0]);

    $.ajax({
        url: `/customers/${currentCustomerId}/addresses`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Address added successfully', 'success');
            $('#addAddressModal').modal('hide');
            $('#addAddressForm')[0].reset();
            loadAddresses();
        } else {
            showAlert(response.message || 'Error adding address', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error adding address', 'danger');
    });
}

function editAddress(addressId) {
    // Redirect to edit address page or show edit modal
    showAlert('Edit address functionality will be implemented', 'info');
}

function deleteAddress(addressId) {
    if (confirm('Are you sure you want to delete this address?')) {
        $.ajax({
            url: `/customers/${currentCustomerId}/addresses/${addressId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                showAlert('Address deleted successfully', 'success');
                loadAddresses();
            } else {
                showAlert(response.message || 'Error deleting address', 'danger');
            }
        })
        .fail(function() {
            showAlert('Error deleting address', 'danger');
        });
    }
}

function sendEmail() {
    @if($customer->email)
    $('#sendEmailModal').modal('show');
    @else
    showAlert('Customer email not available', 'warning');
    @endif
}

function submitSendEmail() {
    const formData = new FormData($('#sendEmailForm')[0]);

    $.ajax({
        url: `/customers/${currentCustomerId}/send-email`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Email sent successfully', 'success');
            $('#sendEmailModal').modal('hide');
            $('#sendEmailForm')[0].reset();
        } else {
            showAlert(response.message || 'Error sending email', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error sending email', 'danger');
    });
}

function sendSMS() {
    @if($customer->phone)
    showAlert('SMS functionality will be implemented', 'info');
    @else
    showAlert('Customer phone number not available', 'warning');
    @endif
}

function exportCustomerData() {
    window.location.href = `/customers/${currentCustomerId}/export`;
}

function showAlert(message, type) {
    const alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.container-fluid').prepend(alert);

    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush