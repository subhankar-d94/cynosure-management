@extends('layouts.app')

@section('title', 'Customer Details')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
        <li class="breadcrumb-item active">Customer Details</li>
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
                        <span id="customerInitial">C</span>
                    </div>
                    <div>
                        <h2 class="mb-1" id="customerName">Customer Name</h2>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2" id="customerStatus">Active</span>
                            <span class="text-muted" id="customerCode">CUST001</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="createOrder()">
                        <i class="bi bi-plus-lg me-1"></i>New Order
                    </button>
                    <a href="" class="btn btn-outline-secondary" id="editBtn">
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
                            <h3 class="mb-0 text-primary" id="totalOrders">-</h3>
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
                            <h3 class="mb-0 text-success" id="totalSpent">-</h3>
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
                            <h3 class="mb-0 text-info" id="averageOrder">-</h3>
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
                            <h3 class="mb-0 text-warning" id="lastOrderDate">-</h3>
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
                            <p class="mb-3" id="customerEmail">-</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Phone Number</h6>
                            <p class="mb-3" id="customerPhone">-</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Type</h6>
                            <p class="mb-3">
                                <span class="badge bg-secondary" id="customerType">Individual</span>
                            </p>
                        </div>
                        <div class="col-md-6" id="companyInfo" style="display: none;">
                            <h6 class="text-muted">Company</h6>
                            <p class="mb-3" id="companyName">-</p>
                        </div>
                        <div class="col-md-12" id="notesSection" style="display: none;">
                            <h6 class="text-muted">Notes</h6>
                            <p class="mb-0" id="customerNotes">-</p>
                        </div>
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
                        <!-- Addresses will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Order History</h5>
                    <a href="" class="btn btn-sm btn-outline-primary" id="viewAllOrdersBtn">
                        View All Orders
                    </a>
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
                            <tbody id="ordersTableBody">
                                <!-- Orders will be loaded here -->
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
                        <p class="mb-0" id="creditLimit">₹0</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Payment Terms</h6>
                        <p class="mb-0" id="paymentTerms">30 days</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Discount</h6>
                        <p class="mb-0" id="discount">0%</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Preferred Contact</h6>
                        <p class="mb-0" id="preferredContact">Email</p>
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
                        <input class="form-check-input" type="checkbox" id="emailNotifications" disabled>
                        <label class="form-check-label">Email Notifications</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="smsNotifications" disabled>
                        <label class="form-check-label">SMS Notifications</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="marketingEmails" disabled>
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
                        <input type="email" class="form-control" id="emailTo" readonly>
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
let currentCustomerId = {{ $customer->id ?? 'null' }};
let customerData = null;

$(document).ready(function() {
    if (currentCustomerId) {
        loadCustomerData();
        loadAddresses();
        loadOrders();
    }

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

function loadCustomerData() {
    $.get(`/customers/${currentCustomerId}`)
        .done(function(response) {
            if (response.success) {
                customerData = response.data;
                displayCustomerInfo(customerData);
            }
        })
        .fail(function() {
            showAlert('Error loading customer data', 'danger');
        });
}

function displayCustomerInfo(customer) {
    // Basic info
    $('#customerName').text(customer.name || 'N/A');
    $('#customerCode').text(customer.customer_code || 'N/A');
    $('#customerInitial').text(customer.name ? customer.name.charAt(0).toUpperCase() : 'C');

    // Status
    const statusClass = customer.status === 'active' ? 'bg-success' : 'bg-secondary';
    $('#customerStatus').removeClass('bg-success bg-secondary').addClass(statusClass).text(customer.status || 'Unknown');

    // Contact info
    $('#customerEmail').text(customer.email || 'Not provided');
    $('#customerPhone').text(customer.phone || 'Not provided');

    // Customer type
    const typeText = customer.customer_type === 'business' ? 'Business' : 'Individual';
    $('#customerType').text(typeText);

    // Company info for business customers
    if (customer.customer_type === 'business' && customer.company_name) {
        $('#companyInfo').show();
        $('#companyName').text(customer.company_name);
    }

    // Notes
    if (customer.notes) {
        $('#notesSection').show();
        $('#customerNotes').text(customer.notes);
    }

    // Settings
    $('#creditLimit').text('₹' + (customer.credit_limit || 0).toLocaleString());
    $('#paymentTerms').text((customer.payment_terms || 30) + ' days');
    $('#discount').text((customer.discount_percentage || 0) + '%');
    $('#preferredContact').text(customer.preferred_contact_method || 'Email');

    // Notification preferences
    $('#emailNotifications').prop('checked', customer.email_notifications);
    $('#smsNotifications').prop('checked', customer.sms_notifications);
    $('#marketingEmails').prop('checked', customer.marketing_emails);

    // Update edit button
    $('#editBtn').attr('href', `/customers/${customer.id}/edit`);
    $('#viewAllOrdersBtn').attr('href', `/customers/${customer.id}/orders`);
    $('#emailTo').val(customer.email);

    // Stats
    $('#totalOrders').text(customer.stats?.total_orders || 0);
    $('#totalSpent').text('₹' + (customer.stats?.total_spent || 0).toLocaleString());
    $('#averageOrder').text('₹' + (customer.stats?.average_order || 0).toLocaleString());

    const lastOrderDays = customer.stats?.last_order_days || 'Never';
    $('#lastOrderDate').text(lastOrderDays === 'Never' ? 'Never' : lastOrderDays + 'd');
}

function loadAddresses() {
    $.get(`/customers/${currentCustomerId}/addresses`)
        .done(function(response) {
            if (response.success) {
                displayAddresses(response.data);
            }
        })
        .fail(function() {
            $('#addressesList').html('<p class="text-muted">Error loading addresses</p>');
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

    $('#addressesList').html(html);
}

function loadOrders() {
    $.get(`/customers/${currentCustomerId}/orders`, { limit: 5 })
        .done(function(response) {
            if (response.success) {
                displayOrders(response.data);
            }
        })
        .fail(function() {
            $('#ordersTableBody').html('<tr><td colspan="6" class="text-center">Error loading orders</td></tr>');
        });
}

function displayOrders(orders) {
    let html = '';

    if (orders.length === 0) {
        html = '<tr><td colspan="6" class="text-center text-muted">No orders found</td></tr>';
    } else {
        orders.forEach(function(order) {
            const statusClass = getOrderStatusClass(order.status);

            html += `
                <tr>
                    <td>
                        <a href="/orders/${order.id}" class="fw-bold text-decoration-none">
                            #${order.order_number}
                        </a>
                    </td>
                    <td>${new Date(order.created_at).toLocaleDateString()}</td>
                    <td>${order.items_count || 0} items</td>
                    <td>₹${(order.total_amount || 0).toLocaleString()}</td>
                    <td>
                        <span class="badge ${statusClass}">${order.status}</span>
                    </td>
                    <td>
                        <a href="/orders/${order.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }

    $('#ordersTableBody').html(html);
}

function getOrderStatusClass(status) {
    const statusClasses = {
        'pending': 'bg-warning',
        'confirmed': 'bg-info',
        'processing': 'bg-primary',
        'shipped': 'bg-secondary',
        'delivered': 'bg-success',
        'cancelled': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
}

function createOrder() {
    if (customerData) {
        window.location.href = `/orders/create?customer_id=${currentCustomerId}`;
    }
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
    if (customerData && customerData.email) {
        $('#sendEmailModal').modal('show');
    } else {
        showAlert('Customer email not available', 'warning');
    }
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
    if (customerData && customerData.phone) {
        showAlert('SMS functionality will be implemented', 'info');
    } else {
        showAlert('Customer phone number not available', 'warning');
    }
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