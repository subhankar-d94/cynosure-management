@extends('layouts.app')

@section('title', 'Edit Customer')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.show', $customer->id ?? 1) }}">Customer Details</a></li>
        <li class="breadcrumb-item active">Edit Customer</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Edit Customer</h2>
                    <p class="text-muted mb-0">Update customer information and settings</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('customers.show', $customer->id ?? 1) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-1"></i>View Customer
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Customers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="customerEditForm" method="POST" action="{{ route('customers.update', $customer->id ?? 1) }}">
        @csrf
        @method('PUT')
        <!-- Hidden fields for required backend fields -->
        <input type="hidden" name="customer_type" value="{{ old('customer_type', $customer->customer_type ?? 'individual') }}">
        <input type="hidden" name="customer_code" value="{{ old('customer_code', $customer->customer_code ?? '') }}">
        <input type="hidden" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit ?? 0) }}">
        <input type="hidden" name="payment_terms" value="{{ old('payment_terms', $customer->payment_terms ?? 30) }}">
        <input type="hidden" name="discount_percentage" value="{{ old('discount_percentage', $customer->discount_percentage ?? 0) }}">

        <div class="row">
            <!-- Main Information -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                       value="{{ old('name', $customer->name ?? '') }}"
                                       required placeholder="Enter full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email"
                                       value="{{ old('email', $customer->email ?? '') }}"
                                       placeholder="customer@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone"
                                       value="{{ old('phone', $customer->phone ?? '') }}"
                                       placeholder="+91 9876543210">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3"
                                          placeholder="Additional notes about the customer">{{ old('notes', $customer->notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Management -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Address Management</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadAddresses()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="addressContainer">
                            <!-- Addresses will be loaded here -->
                        </div>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="addAddress()">
                            <i class="bi bi-plus"></i> Add New Address
                        </button>
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div id="activityLog">
                            <!-- Activity will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status & Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Status & Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" {{ (old('status', $customer->status ?? 'active') === 'active') ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ (old('status', $customer->status ?? 'active') === 'inactive') ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Customer Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h6 class="text-primary mb-1" id="totalOrders">0</h6>
                                    <small class="text-muted">Total Orders</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h6 class="text-success mb-1" id="totalSpent">₹0</h6>
                                    <small class="text-muted">Total Spent</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h6 class="text-info mb-1" id="averageOrder">₹0</h6>
                                    <small class="text-muted">Avg Order</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h6 class="text-warning mb-1" id="lastOrder">Never</h6>
                                    <small class="text-muted">Last Order</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Update Customer
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                                <i class="bi bi-save me-1"></i>Save as Draft
                            </button>
                            <a href="{{ route('customers.show', $customer->id ?? 1) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>View Customer
                            </a>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteCustomer()">
                                <i class="bi bi-trash me-1"></i>Delete Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
                <p class="text-danger"><strong>Warning:</strong> All associated orders and data will be affected.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Customer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentCustomerId = {{ $customer->id ?? 'null' }};

$(document).ready(function() {
    // Load data if editing existing customer
    if (currentCustomerId) {
        loadCustomerStats();
        loadAddresses();
        loadActivityLog();
    }

    // Form submission
    $('#customerEditForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });

    // Add address form submission
    $('#addAddressForm').on('submit', function(e) {
        e.preventDefault();
        submitAddAddress();
    });

    // Delete confirmation
    $('#confirmDeleteBtn').on('click', function() {
        confirmDelete();
    });
});

function loadCustomerStats() {
    $.get(`/customers/${currentCustomerId}?include_stats=1`)
        .done(function(response) {
            if (response.success && response.data.stats) {
                const stats = response.data.stats;
                $('#totalOrders').text(stats.total_orders || 0);
                $('#totalSpent').text('₹' + (stats.total_spent || 0).toLocaleString());
                $('#averageOrder').text('₹' + (stats.average_order || 0).toLocaleString());

                const lastOrderDays = stats.last_order_days;
                $('#lastOrder').text(lastOrderDays ? lastOrderDays + 'd ago' : 'Never');
            }
        })
        .fail(function() {
            console.log('Error loading customer stats');
        });
}

function loadAddresses() {
    $.get(`/customers/${currentCustomerId}/addresses`)
        .done(function(response) {
            if (response.success) {
                displayAddresses(response.data);
            }
        })
        .fail(function() {
            $('#addressContainer').html('<p class="text-muted">Error loading addresses</p>');
        });
}

function displayAddresses(addresses) {
    let html = '';

    if (addresses.length === 0) {
        html = '<p class="text-muted">No addresses found</p>';
    } else {
        addresses.forEach(function(address) {
            const typeClass = address.type === 'billing' ? 'text-primary' : address.type === 'shipping' ? 'text-info' : 'text-success';
            const typeIcon = address.type === 'billing' ? 'bi-receipt' : address.type === 'shipping' ? 'bi-truck' : 'bi-geo-alt';

            html += `
                <div class="address-item border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi ${typeIcon} ${typeClass} me-2"></i>
                                <h6 class="mb-0">${address.label || address.type.charAt(0).toUpperCase() + address.type.slice(1)}</h6>
                                ${address.is_default ? '<span class="badge bg-primary ms-2">Default</span>' : ''}
                            </div>
                            <p class="mb-1">${address.street_address}</p>
                            <p class="mb-1">${address.city}, ${address.state} ${address.postal_code}</p>
                            <p class="mb-0 text-muted">${address.country}</p>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="editAddress(${address.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteAddress(${address.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    $('#addressContainer').html(html);
}

function loadActivityLog() {
    $.get(`/customers/${currentCustomerId}/activity`)
        .done(function(response) {
            if (response.success) {
                displayActivityLog(response.data);
            }
        })
        .fail(function() {
            $('#activityLog').html('<p class="text-muted">Error loading activity log</p>');
        });
}

function displayActivityLog(activities) {
    let html = '';

    if (activities.length === 0) {
        html = '<p class="text-muted">No recent activity</p>';
    } else {
        activities.slice(0, 5).forEach(function(activity) {
            html += `
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-light rounded-circle p-2">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1">${activity.description}</p>
                        <small class="text-muted">${new Date(activity.created_at).toLocaleDateString()}</small>
                    </div>
                </div>
            `;
        });
    }

    $('#activityLog').html(html);
}

function addAddress() {
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

function submitForm() {
    const formData = new FormData($('#customerEditForm')[0]);

    // Show loading state
    const submitBtn = $('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="bi bi-spinner-border spinner-border-sm me-1"></i>Updating...').prop('disabled', true);

    $.ajax({
        url: $('#customerEditForm').attr('action'),
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
            showAlert('Customer updated successfully!', 'success');
            setTimeout(function() {
                window.location.href = response.redirect || `/customers/${currentCustomerId}`;
            }, 1000);
        } else {
            showAlert(response.message || 'Error updating customer', 'danger');
            submitBtn.html(originalText).prop('disabled', false);
        }
    })
    .fail(function(xhr) {
        let message = 'Error updating customer';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = Object.values(xhr.responseJSON.errors).flat();
            message = errors.join('<br>');
        }
        showAlert(message, 'danger');
        submitBtn.html(originalText).prop('disabled', false);
    });
}

function saveDraft() {
    const formData = new FormData($('#customerEditForm')[0]);
    formData.append('save_as_draft', '1');

    $.ajax({
        url: $('#customerEditForm').attr('action'),
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
            showAlert('Customer draft saved!', 'success');
        } else {
            showAlert(response.message || 'Error saving draft', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error saving draft', 'danger');
    });
}

function deleteCustomer() {
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    $.ajax({
        url: `/customers/${currentCustomerId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Customer deleted successfully', 'success');
            setTimeout(function() {
                window.location.href = '/customers';
            }, 1000);
        } else {
            showAlert(response.message || 'Error deleting customer', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error deleting customer', 'danger');
    })
    .always(function() {
        $('#deleteModal').modal('hide');
    });
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