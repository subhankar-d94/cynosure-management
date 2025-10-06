@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Add New Supplier</h3>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Suppliers
                    </a>
                </div>
                <div class="card-body">
                    <form id="supplierForm" action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Company Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Company Information</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name *</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="business_registration" class="form-label">Business Registration Number</label>
                                    <input type="text" class="form-control" id="business_registration" name="business_registration">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_id" class="form-label">Tax ID / VAT Number</label>
                                    <input type="text" class="form-control" id="tax_id" name="tax_id">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website" name="website" placeholder="https://example.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="raw_materials">Raw Materials</option>
                                        <option value="components">Components</option>
                                        <option value="packaging">Packaging</option>
                                        <option value="services">Services</option>
                                        <option value="equipment">Equipment</option>
                                        <option value="software">Software</option>
                                        <option value="logistics">Logistics</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pending">Pending</option>
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Primary Contact Information</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_person" class="form-label">Contact Person *</label>
                                    <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="designation" class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation" placeholder="e.g., Sales Manager">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mobile" class="form-label">Mobile Number</label>
                                    <input type="tel" class="form-control" id="mobile" name="mobile">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fax" class="form-label">Fax Number</label>
                                    <input type="tel" class="form-control" id="fax" name="fax">
                                </div>
                            </div>
                        </div>

                        <!-- Address Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Address Information</h5>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Street Address *</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State / Province</label>
                                    <input type="text" class="form-control" id="state" name="state">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country *</label>
                                    <select class="form-select" id="country" name="country" required>
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="MX">Mexico</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="DE">Germany</option>
                                        <option value="FR">France</option>
                                        <option value="IT">Italy</option>
                                        <option value="ES">Spain</option>
                                        <option value="CN">China</option>
                                        <option value="JP">Japan</option>
                                        <option value="KR">South Korea</option>
                                        <option value="IN">India</option>
                                        <option value="AU">Australia</option>
                                        <option value="BR">Brazil</option>
                                        <!-- Add more countries as needed -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Business Terms Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Business Terms</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_terms" class="form-label">Payment Terms</label>
                                    <select class="form-select" id="payment_terms" name="payment_terms">
                                        <option value="net_15">Net 15</option>
                                        <option value="net_30" selected>Net 30</option>
                                        <option value="net_45">Net 45</option>
                                        <option value="net_60">Net 60</option>
                                        <option value="cod">Cash on Delivery</option>
                                        <option value="advance">Advance Payment</option>
                                        <option value="custom">Custom Terms</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="credit_limit" class="form-label">Credit Limit</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" id="credit_limit" name="credit_limit" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Preferred Currency</label>
                                    <select class="form-select" id="currency" name="currency">
                                        <option value="USD" selected>USD - US Dollar</option>
                                        <option value="EUR">EUR - Euro</option>
                                        <option value="GBP">GBP - British Pound</option>
                                        <option value="CAD">CAD - Canadian Dollar</option>
                                        <option value="JPY">JPY - Japanese Yen</option>
                                        <option value="CNY">CNY - Chinese Yuan</option>
                                        <option value="INR">INR - Indian Rupee</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lead_time" class="form-label">Standard Lead Time (days)</label>
                                    <input type="number" class="form-control" id="lead_time" name="lead_time" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Bank Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Bank Information (Optional)</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="account_number" name="account_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="routing_number" class="form-label">Routing Number / Sort Code</label>
                                    <input type="text" class="form-control" id="routing_number" name="routing_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="swift_code" class="form-label">SWIFT / BIC Code</label>
                                    <input type="text" class="form-control" id="swift_code" name="swift_code">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Additional Information</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="products_services" class="form-label">Products / Services Offered</label>
                                    <textarea class="form-control" id="products_services" name="products_services" rows="3" placeholder="Brief description of products or services provided..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Internal Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Internal notes about this supplier..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="certifications" class="form-label">Certifications</label>
                                    <input type="text" class="form-control" id="certifications" name="certifications" placeholder="e.g., ISO 9001, FDA, CE">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="insurance_details" class="form-label">Insurance Details</label>
                                    <input type="text" class="form-control" id="insurance_details" name="insurance_details" placeholder="Insurance provider and policy details">
                                </div>
                            </div>
                        </div>

                        <!-- Documents Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Documents (Optional)</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="business_license" class="form-label">Business License</label>
                                    <input type="file" class="form-control" id="business_license" name="business_license" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload business license document (PDF, JPG, PNG)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_certificate" class="form-label">Tax Certificate</label>
                                    <input type="file" class="form-control" id="tax_certificate" name="tax_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload tax certificate document (PDF, JPG, PNG)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="insurance_certificate" class="form-label">Insurance Certificate</label>
                                    <input type="file" class="form-control" id="insurance_certificate" name="insurance_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload insurance certificate (PDF, JPG, PNG)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quality_certificates" class="form-label">Quality Certificates</label>
                                    <input type="file" class="form-control" id="quality_certificates" name="quality_certificates" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload quality certificates (PDF, JPG, PNG)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" name="action" value="save">
                                        <i class="fas fa-save"></i> Save Supplier
                                    </button>
                                    <button type="submit" class="btn btn-success" name="action" value="save_and_activate">
                                        <i class="fas fa-check"></i> Save & Activate
                                    </button>
                                    <button type="button" class="btn btn-info" id="previewBtn">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supplier Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('supplierForm').submit()">
                    Save Supplier
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('#supplierForm').on('submit', function(e) {
        let isValid = true;
        const requiredFields = ['company_name', 'contact_person', 'email', 'phone', 'address', 'city', 'country', 'category'];

        requiredFields.forEach(function(field) {
            const element = $('#' + field);
            if (!element.val().trim()) {
                element.addClass('is-invalid');
                isValid = false;
            } else {
                element.removeClass('is-invalid');
            }
        });

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if ($('#email').val() && !emailRegex.test($('#email').val())) {
            $('#email').addClass('is-invalid');
            isValid = false;
        }

        // Website validation
        if ($('#website').val()) {
            const websiteRegex = /^https?:\/\/.+/;
            if (!websiteRegex.test($('#website').val())) {
                $('#website').addClass('is-invalid');
                isValid = false;
                alert('Please enter a valid website URL starting with http:// or https://');
            }
        }

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields correctly.');
        }
    });

    // Remove validation classes on input
    $('.form-control, .form-select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    // Preview functionality
    $('#previewBtn').click(function() {
        generatePreview();
    });

    function generatePreview() {
        const formData = {
            company_name: $('#company_name').val(),
            business_registration: $('#business_registration').val(),
            tax_id: $('#tax_id').val(),
            website: $('#website').val(),
            category: $('#category option:selected').text(),
            status: $('#status option:selected').text(),
            contact_person: $('#contact_person').val(),
            designation: $('#designation').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            mobile: $('#mobile').val(),
            fax: $('#fax').val(),
            address: $('#address').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            postal_code: $('#postal_code').val(),
            country: $('#country option:selected').text(),
            payment_terms: $('#payment_terms option:selected').text(),
            credit_limit: $('#credit_limit').val(),
            currency: $('#currency option:selected').text(),
            lead_time: $('#lead_time').val(),
            bank_name: $('#bank_name').val(),
            account_number: $('#account_number').val(),
            routing_number: $('#routing_number').val(),
            swift_code: $('#swift_code').val(),
            products_services: $('#products_services').val(),
            notes: $('#notes').val(),
            certifications: $('#certifications').val(),
            insurance_details: $('#insurance_details').val()
        };

        let previewHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Company Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Company Name:</strong></td><td>${formData.company_name || 'N/A'}</td></tr>
                        <tr><td><strong>Registration:</strong></td><td>${formData.business_registration || 'N/A'}</td></tr>
                        <tr><td><strong>Tax ID:</strong></td><td>${formData.tax_id || 'N/A'}</td></tr>
                        <tr><td><strong>Website:</strong></td><td>${formData.website || 'N/A'}</td></tr>
                        <tr><td><strong>Category:</strong></td><td>${formData.category || 'N/A'}</td></tr>
                        <tr><td><strong>Status:</strong></td><td>${formData.status || 'N/A'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Contact Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Contact Person:</strong></td><td>${formData.contact_person || 'N/A'}</td></tr>
                        <tr><td><strong>Designation:</strong></td><td>${formData.designation || 'N/A'}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${formData.email || 'N/A'}</td></tr>
                        <tr><td><strong>Phone:</strong></td><td>${formData.phone || 'N/A'}</td></tr>
                        <tr><td><strong>Mobile:</strong></td><td>${formData.mobile || 'N/A'}</td></tr>
                        <tr><td><strong>Fax:</strong></td><td>${formData.fax || 'N/A'}</td></tr>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Address</h6>
                    <p>
                        ${formData.address || 'N/A'}<br>
                        ${formData.city || 'N/A'}, ${formData.state || 'N/A'} ${formData.postal_code || ''}<br>
                        ${formData.country || 'N/A'}
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Business Terms</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Payment Terms:</strong></td><td>${formData.payment_terms || 'N/A'}</td></tr>
                        <tr><td><strong>Credit Limit:</strong></td><td>$${formData.credit_limit || '0'}</td></tr>
                        <tr><td><strong>Currency:</strong></td><td>${formData.currency || 'N/A'}</td></tr>
                        <tr><td><strong>Lead Time:</strong></td><td>${formData.lead_time || 'N/A'} days</td></tr>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Bank Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Bank Name:</strong></td><td>${formData.bank_name || 'N/A'}</td></tr>
                        <tr><td><strong>Account Number:</strong></td><td>${formData.account_number || 'N/A'}</td></tr>
                        <tr><td><strong>Routing Number:</strong></td><td>${formData.routing_number || 'N/A'}</td></tr>
                        <tr><td><strong>SWIFT Code:</strong></td><td>${formData.swift_code || 'N/A'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Additional Information</h6>
                    <p><strong>Products/Services:</strong><br>${formData.products_services || 'N/A'}</p>
                    <p><strong>Certifications:</strong><br>${formData.certifications || 'N/A'}</p>
                    <p><strong>Insurance:</strong><br>${formData.insurance_details || 'N/A'}</p>
                    <p><strong>Notes:</strong><br>${formData.notes || 'N/A'}</p>
                </div>
            </div>
        `;

        $('#previewContent').html(previewHtml);
        $('#previewModal').modal('show');
    }

    // Auto-format phone numbers (basic)
    $('#phone, #mobile, #fax').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 10) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            $(this).val(value);
        }
    });

    // Auto-format website URL
    $('#website').on('blur', function() {
        let value = $(this).val().trim();
        if (value && !value.match(/^https?:\/\//)) {
            $(this).val('https://' + value);
        }
    });

    // Currency and credit limit formatting
    $('#credit_limit').on('input', function() {
        let value = $(this).val().replace(/[^\d.]/g, '');
        $(this).val(value);
    });

    // File upload preview
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        if (file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            $(this).next('.form-text').text(`Selected: ${fileName} (${fileSize} MB)`);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
}

.form-control.is-invalid:focus,
.form-select.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.table-sm td {
    padding: 0.25rem 0.5rem;
    border-top: 1px solid #dee2e6;
}

.preview-section {
    margin-bottom: 1.5rem;
}

.preview-section h6 {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}
</style>
@endpush