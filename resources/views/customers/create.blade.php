@extends('layouts.app')

@section('title', 'Add New Customer')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
        <li class="breadcrumb-item active">Add New Customer</li>
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
                    <h2 class="mb-1">Add New Customer</h2>
                    <p class="text-muted mb-0">Create a new customer record with contact and address information</p>
                </div>
                <div>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Customers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="customerForm" method="POST" action="{{ route('customers.store') }}">
        @csrf
        <div class="row">
            <!-- Main Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="customer_type" id="customer_type" required>
                                    <option value="">Select Type</option>
                                    <option value="individual">Individual</option>
                                    <option value="business">Business</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer Code</label>
                                <input type="text" class="form-control" name="customer_code" id="customer_code" placeholder="Auto-generated if empty">
                                <small class="text-muted">Leave empty for auto-generation</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="Enter full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" placeholder="customer@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" placeholder="+91 9876543210">
                            </div>
                            <div class="col-md-6" id="businessFields" style="display: none;">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control" name="company_name" placeholder="Company name">
                            </div>
                            <div class="col-md-6" id="gstField" style="display: none;">
                                <label class="form-label">GST Number</label>
                                <input type="text" class="form-control" name="gst_number" placeholder="GST registration number">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes about the customer"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Address Information</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAddress()">
                            <i class="bi bi-plus"></i> Add Address
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="addressContainer">
                            <!-- Default address form -->
                            <div class="address-form border rounded p-3 mb-3" data-index="0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Primary Address</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="addresses[0][is_default]" value="1" checked>
                                        <label class="form-check-label">Default Address</label>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Address Type</label>
                                        <select class="form-select" name="addresses[0][type]">
                                            <option value="billing">Billing</option>
                                            <option value="shipping">Shipping</option>
                                            <option value="both" selected>Both</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address Label</label>
                                        <input type="text" class="form-control" name="addresses[0][label]" placeholder="Home, Office, etc.">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Street Address</label>
                                        <input type="text" class="form-control address-autocomplete" name="addresses[0][street_address]" placeholder="Start typing address..." data-index="0">
                                        <small class="text-muted">Start typing to get address suggestions</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="addresses[0][city]" placeholder="City name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" name="addresses[0][state]" placeholder="State name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">PIN Code</label>
                                        <input type="text" class="form-control" name="addresses[0][postal_code]" placeholder="PIN code">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Country</label>
                                        <input type="text" class="form-control" name="addresses[0][country]" value="India" placeholder="Country">
                                    </div>
                                </div>
                            </div>
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
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Credit Limit</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="credit_limit" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <small class="text-muted">Maximum credit allowed</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Terms (Days)</label>
                            <input type="number" class="form-control" name="payment_terms" min="0" placeholder="30">
                            <small class="text-muted">Payment due days</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="discount_percentage" step="0.01" min="0" max="100" placeholder="0.00">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Default discount for this customer</small>
                        </div>
                    </div>
                </div>

                <!-- Customer Preferences -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Preferences</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="email_notifications" value="1" checked>
                                <label class="form-check-label">Email Notifications</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sms_notifications" value="1">
                                <label class="form-check-label">SMS Notifications</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="marketing_emails" value="1">
                                <label class="form-check-label">Marketing Emails</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preferred Contact Method</label>
                            <select class="form-select" name="preferred_contact_method">
                                <option value="email">Email</option>
                                <option value="phone">Phone</option>
                                <option value="sms">SMS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Save Customer
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                                <i class="bi bi-save me-1"></i>Save as Draft
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-lg me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Address Template (Hidden) -->
<template id="addressTemplate">
    <div class="address-form border rounded p-3 mb-3" data-index="__INDEX__">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Address __NUM__</h6>
            <div>
                <div class="form-check d-inline-block me-3">
                    <input class="form-check-input" type="checkbox" name="addresses[__INDEX__][is_default]" value="1">
                    <label class="form-check-label">Default</label>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAddress(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Address Type</label>
                <select class="form-select" name="addresses[__INDEX__][type]">
                    <option value="billing">Billing</option>
                    <option value="shipping">Shipping</option>
                    <option value="both">Both</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Address Label</label>
                <input type="text" class="form-control" name="addresses[__INDEX__][label]" placeholder="Home, Office, etc.">
            </div>
            <div class="col-md-12">
                <label class="form-label">Street Address</label>
                <input type="text" class="form-control address-autocomplete" name="addresses[__INDEX__][street_address]" placeholder="Start typing address..." data-index="__INDEX__">
                <small class="text-muted">Start typing to get address suggestions</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <input type="text" class="form-control" name="addresses[__INDEX__][city]" placeholder="City name">
            </div>
            <div class="col-md-6">
                <label class="form-label">State</label>
                <input type="text" class="form-control" name="addresses[__INDEX__][state]" placeholder="State name">
            </div>
            <div class="col-md-6">
                <label class="form-label">PIN Code</label>
                <input type="text" class="form-control" name="addresses[__INDEX__][postal_code]" placeholder="PIN code">
            </div>
            <div class="col-md-6">
                <label class="form-label">Country</label>
                <input type="text" class="form-control" name="addresses[__INDEX__][country]" value="India" placeholder="Country">
            </div>
        </div>
    </div>
</template>

@endsection

@push('styles')
<style>
/* Google Places Autocomplete Styling */
.pac-container {
    background-color: #fff;
    z-index: 1051 !important;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    font-family: inherit;
}

.pac-item {
    border-top: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    cursor: pointer;
    font-size: 0.875rem;
}

.pac-item:first-child {
    border-top: none;
}

.pac-item:hover {
    background-color: #f8f9fa;
}

.pac-item-selected {
    background-color: #e9ecef;
}

.pac-matched {
    font-weight: 600;
    color: #0d6efd;
}

.address-autocomplete {
    position: relative;
}

.address-loading {
    background-image: url("data:image/svg+xml,%3csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M10 3a7 7 0 100 14 7 7 0 000-14zM2 10a8 8 0 1116 0 8 8 0 01-16 0z' fill='%236c757d'/%3e%3cpath d='M10 6a1 1 0 011 1v3a1 1 0 11-2 0V7a1 1 0 011-1z' fill='%236c757d'/%3e%3cpath d='M10 13a1 1 0 100 2 1 1 0 000-2z' fill='%236c757d'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px;
}
</style>
@endpush

@push('scripts')
<script>
let addressIndex = 1;
let autocompleteInstances = {};

$(document).ready(function() {
    // Customer type change handler
    $('#customer_type').on('change', function() {
        const customerType = $(this).val();
        if (customerType === 'business') {
            $('#businessFields, #gstField').show();
        } else {
            $('#businessFields, #gstField').hide();
        }
    });

    // Form submission
    $('#customerForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });

    // Auto-generate customer code
    generateCustomerCode();

    // Initialize Google Places autocomplete when Google Maps API is loaded
    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        initializeAutocomplete();
    } else {
        // Wait for Google Maps API to load
        window.initMap = function() {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                initializeAutocomplete();
            } else {
                console.warn('Google Places API not available. Address autocomplete disabled.');
            }
        };

        // Fallback if Google Maps API fails to load after 10 seconds
        setTimeout(function() {
            if (typeof google === 'undefined') {
                console.warn('Google Maps API failed to load. Please check your API key and internet connection.');
                $('.address-autocomplete').attr('placeholder', 'Enter full address manually');
            }
        }, 10000);
    }
});

function initializeAutocomplete() {
    // Initialize autocomplete for existing address inputs
    $('.address-autocomplete').each(function() {
        setupAddressAutocomplete(this);
    });
}

function setupAddressAutocomplete(input) {
    const index = $(input).data('index');
    const $input = $(input);

    try {
        // Show loading state
        $input.addClass('address-loading');

        // Create autocomplete instance
        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['address'],
            componentRestrictions: {
                country: 'IN' // Restrict to India only
            },
            fields: ['address_components', 'formatted_address', 'geometry']
        });

        // Store the instance
        autocompleteInstances[index] = autocomplete;

        // Remove loading state
        $input.removeClass('address-loading');

        // Handle place selection
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();

            if (!place.address_components) {
                console.log('No details available for input: \'' + place.name + '\'');
                return;
            }

            // Parse address components
            const addressComponents = parseAddressComponents(place.address_components);

            // Fill in the address fields
            fillAddressFields(index, addressComponents, place.formatted_address);
        });

    } catch (error) {
        console.error('Error setting up address autocomplete:', error);
        $input.removeClass('address-loading');
        $input.attr('placeholder', 'Enter address manually');
    }
}

function parseAddressComponents(components) {
    const parsed = {
        street_number: '',
        route: '',
        locality: '',
        administrative_area_level_2: '', // District
        administrative_area_level_1: '', // State
        postal_code: '',
        country: ''
    };

    components.forEach(function(component) {
        const types = component.types;

        if (types.includes('street_number')) {
            parsed.street_number = component.long_name;
        }
        if (types.includes('route')) {
            parsed.route = component.long_name;
        }
        if (types.includes('locality')) {
            parsed.locality = component.long_name;
        }
        if (types.includes('administrative_area_level_2')) {
            parsed.administrative_area_level_2 = component.long_name;
        }
        if (types.includes('administrative_area_level_1')) {
            parsed.administrative_area_level_1 = component.long_name;
        }
        if (types.includes('postal_code')) {
            parsed.postal_code = component.long_name;
        }
        if (types.includes('country')) {
            parsed.country = component.long_name;
        }
    });

    return parsed;
}

function fillAddressFields(index, addressComponents, formattedAddress) {
    const addressForm = $(`.address-form[data-index="${index}"]`);

    // Build street address
    let streetAddress = '';
    if (addressComponents.street_number) {
        streetAddress += addressComponents.street_number + ' ';
    }
    if (addressComponents.route) {
        streetAddress += addressComponents.route;
    }

    // If no street components, use the input value
    if (!streetAddress.trim()) {
        streetAddress = addressForm.find(`input[name*="street_address"]`).val();
    }

    // Fill the fields
    addressForm.find(`input[name*="street_address"]`).val(streetAddress);
    addressForm.find(`input[name*="city"]`).val(addressComponents.locality || addressComponents.administrative_area_level_2);
    addressForm.find(`input[name*="state"]`).val(addressComponents.administrative_area_level_1);
    addressForm.find(`input[name*="postal_code"]`).val(addressComponents.postal_code);
    addressForm.find(`input[name*="country"]`).val(addressComponents.country || 'India');

    // Show success message
    showAlert('Address auto-filled successfully!', 'success');
}

function generateCustomerCode() {
    const prefix = 'CUST';
    const timestamp = Date.now().toString().slice(-6);
    const random = Math.floor(Math.random() * 100).toString().padStart(2, '0');
    $('#customer_code').attr('placeholder', `${prefix}${timestamp}${random}`);
}

function addAddress() {
    const template = document.getElementById('addressTemplate').innerHTML;
    const addressHtml = template.replace(/__INDEX__/g, addressIndex).replace(/__NUM__/g, addressIndex + 1);

    $('#addressContainer').append(addressHtml);

    // Initialize autocomplete for the new address input
    const newAddressInput = $(`.address-form[data-index="${addressIndex}"] .address-autocomplete`)[0];
    if (newAddressInput && typeof google !== 'undefined' && google.maps && google.maps.places) {
        setupAddressAutocomplete(newAddressInput);
    }

    addressIndex++;
}

function removeAddress(button) {
    const addressForm = $(button).closest('.address-form');
    const isDefault = addressForm.find('input[type="checkbox"][name*="is_default"]').is(':checked');

    if (isDefault) {
        showAlert('Cannot remove default address. Please set another address as default first.', 'warning');
        return;
    }

    addressForm.remove();
}

function submitForm() {
    const formData = new FormData($('#customerForm')[0]);

    // Handle empty numeric fields - set defaults
    if (!formData.get('credit_limit') || formData.get('credit_limit') === '') {
        formData.set('credit_limit', '0');
    }
    if (!formData.get('payment_terms') || formData.get('payment_terms') === '') {
        formData.set('payment_terms', '30');
    }
    if (!formData.get('discount_percentage') || formData.get('discount_percentage') === '') {
        formData.set('discount_percentage', '0');
    }

    // Handle checkboxes properly
    formData.set('email_notifications', $('input[name="email_notifications"]').is(':checked') ? '1' : '0');
    formData.set('sms_notifications', $('input[name="sms_notifications"]').is(':checked') ? '1' : '0');
    formData.set('marketing_emails', $('input[name="marketing_emails"]').is(':checked') ? '1' : '0');

    // Generate customer code if empty
    if (!formData.get('customer_code') || formData.get('customer_code') === '') {
        const prefix = 'CUST';
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.floor(Math.random() * 100).toString().padStart(2, '0');
        formData.set('customer_code', `${prefix}${timestamp}${random}`);
    }

    // Show loading state
    const submitBtn = $('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="bi bi-spinner-border spinner-border-sm me-1"></i>Saving...').prop('disabled', true);

    $.ajax({
        url: $('#customerForm').attr('action'),
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
            showAlert('Customer created successfully!', 'success');
            setTimeout(function() {
                window.location.href = response.redirect || '{{ route("customers.index") }}';
            }, 1000);
        } else {
            showAlert(response.message || 'Error creating customer', 'danger');
            submitBtn.html(originalText).prop('disabled', false);
        }
    })
    .fail(function(xhr) {
        let message = 'Error creating customer';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = Object.values(xhr.responseJSON.errors).flat();
            message = errors.join('<br>');
        }
        showAlert(message, 'danger');
        submitBtn.html(originalText).prop('disabled', false);
    });
}

function saveDraft() {
    const formData = new FormData($('#customerForm')[0]);
    formData.append('save_as_draft', '1');

    $.ajax({
        url: $('#customerForm').attr('action'),
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
            showAlert('Customer saved as draft!', 'success');
        } else {
            showAlert(response.message || 'Error saving draft', 'danger');
        }
    })
    .fail(function() {
        showAlert('Error saving draft', 'danger');
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