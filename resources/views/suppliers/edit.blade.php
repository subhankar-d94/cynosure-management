@extends('layouts.app')

@section('title', 'Edit Supplier')

@push('styles')
<style>
    .supplier-form {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .form-section {
        padding: 25px;
        border-bottom: 1px solid #e9ecef;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 10px;
        color: #007bff;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }

    .required {
        color: #dc3545;
    }

    .btn-action {
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .file-upload-area:hover {
        border-color: #007bff;
        background: #e7f3ff;
    }

    .uploaded-files {
        margin-top: 10px;
    }

    .file-item {
        display: inline-block;
        margin: 5px;
        padding: 8px 12px;
        background: #e9ecef;
        border-radius: 15px;
        font-size: 0.85rem;
    }

    .remove-file {
        color: #dc3545;
        margin-left: 8px;
        cursor: pointer;
    }

    .address-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    @media (max-width: 768px) {
        .address-group {
            grid-template-columns: 1fr;
        }

        .supplier-form {
            margin: 10px;
        }

        .form-section {
            padding: 15px;
        }
    }

    .current-logo {
        max-width: 120px;
        max-height: 80px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .rating-display {
        color: #ffc107;
    }

    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .status-active { background-color: #28a745; }
    .status-inactive { background-color: #dc3545; }
    .status-pending { background-color: #ffc107; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Supplier</h1>
            <p class="text-muted">Update supplier information and business details</p>
        </div>
        <div>
            <a href="{{ route('suppliers.show', $supplier->id ?? 1) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-eye"></i> View Supplier
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Supplier Edit Form -->
    <form action="{{ route('suppliers.update', $supplier->id ?? 1) }}" method="POST" enctype="multipart/form-data" id="supplierForm">
        @csrf
        @method('PUT')

        <div class="supplier-form">
            <!-- Company Information -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-building"></i>
                    Company Information
                </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Company Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="company_name"
                                   value="{{ old('company_name', $supplier->company_name ?? 'TechCorp Solutions Ltd.') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Registration Number</label>
                            <input type="text" class="form-control" name="registration_number"
                                   value="{{ old('registration_number', $supplier->registration_number ?? 'REG123456789') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tax ID / GST Number</label>
                            <input type="text" class="form-control" name="tax_id"
                                   value="{{ old('tax_id', $supplier->tax_id ?? 'GST987654321') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" name="website"
                                   value="{{ old('website', $supplier->website ?? 'https://techcorp.com') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Category <span class="required">*</span></label>
                            <select class="form-select" name="category" required>
                                <option value="">Select Category</option>
                                <option value="raw_materials" {{ (old('category', $supplier->category ?? 'technology') == 'raw_materials') ? 'selected' : '' }}>Raw Materials</option>
                                <option value="manufacturing" {{ (old('category', $supplier->category ?? 'technology') == 'manufacturing') ? 'selected' : '' }}>Manufacturing</option>
                                <option value="technology" {{ (old('category', $supplier->category ?? 'technology') == 'technology') ? 'selected' : '' }}>Technology</option>
                                <option value="services" {{ (old('category', $supplier->category ?? 'technology') == 'services') ? 'selected' : '' }}>Services</option>
                                <option value="logistics" {{ (old('category', $supplier->category ?? 'technology') == 'logistics') ? 'selected' : '' }}>Logistics</option>
                                <option value="packaging" {{ (old('category', $supplier->category ?? 'technology') == 'packaging') ? 'selected' : '' }}>Packaging</option>
                                <option value="maintenance" {{ (old('category', $supplier->category ?? 'technology') == 'maintenance') ? 'selected' : '' }}>Maintenance</option>
                                <option value="consulting" {{ (old('category', $supplier->category ?? 'technology') == 'consulting') ? 'selected' : '' }}>Consulting</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" {{ (old('status', $supplier->status ?? 'active') == 'active') ? 'selected' : '' }}>
                                    <span class="status-indicator status-active"></span> Active
                                </option>
                                <option value="inactive" {{ (old('status', $supplier->status ?? 'active') == 'inactive') ? 'selected' : '' }}>
                                    <span class="status-indicator status-inactive"></span> Inactive
                                </option>
                                <option value="pending" {{ (old('status', $supplier->status ?? 'active') == 'pending') ? 'selected' : '' }}>
                                    <span class="status-indicator status-pending"></span> Pending Approval
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Company Description</label>
                            <textarea class="form-control" name="description" rows="3"
                                      placeholder="Brief description of the company and its services">{{ old('description', $supplier->description ?? 'Leading technology solutions provider specializing in enterprise software development and IT consulting services.') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Current Logo</label>
                            @if(isset($supplier->logo) && $supplier->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $supplier->logo) }}" alt="Current Logo" class="current-logo">
                            </div>
                            @endif
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <small class="text-muted">Upload new logo (optional). Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Current Rating</label>
                            <div class="rating-display">
                                @php
                                    $rating = $supplier->rating ?? 4.2;
                                    $fullStars = floor($rating);
                                    $halfStar = $rating - $fullStars >= 0.5;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fas fa-star"></i>
                                    @elseif($i == $fullStars + 1 && $halfStar)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                                <span class="ms-2">{{ $rating }}/5.0</span>
                            </div>
                            <small class="text-muted">Based on {{ $supplier->total_reviews ?? 24 }} reviews</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Primary Contact Information -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-user"></i>
                    Primary Contact Information
                </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Contact Person <span class="required">*</span></label>
                            <input type="text" class="form-control" name="contact_person"
                                   value="{{ old('contact_person', $supplier->contact_person ?? 'John Anderson') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" name="designation"
                                   value="{{ old('designation', $supplier->designation ?? 'Sales Director') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" class="form-control" name="email"
                                   value="{{ old('email', $supplier->email ?? 'john.anderson@techcorp.com') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Phone <span class="required">*</span></label>
                            <input type="tel" class="form-control" name="phone"
                                   value="{{ old('phone', $supplier->phone ?? '+1 (555) 123-4567') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Mobile</label>
                            <input type="tel" class="form-control" name="mobile"
                                   value="{{ old('mobile', $supplier->mobile ?? '+1 (555) 987-6543') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Fax</label>
                            <input type="tel" class="form-control" name="fax"
                                   value="{{ old('fax', $supplier->fax ?? '+1 (555) 123-4568') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Address Information
                </h4>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Street Address <span class="required">*</span></label>
                            <input type="text" class="form-control" name="address"
                                   value="{{ old('address', $supplier->address ?? '123 Business Park Drive, Suite 500') }}" required>
                        </div>
                    </div>
                </div>

                <div class="address-group">
                    <div class="form-group">
                        <label class="form-label">City <span class="required">*</span></label>
                        <input type="text" class="form-control" name="city"
                               value="{{ old('city', $supplier->city ?? 'New York') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">State/Province <span class="required">*</span></label>
                        <input type="text" class="form-control" name="state"
                               value="{{ old('state', $supplier->state ?? 'NY') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Postal Code <span class="required">*</span></label>
                        <input type="text" class="form-control" name="postal_code"
                               value="{{ old('postal_code', $supplier->postal_code ?? '10001') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country <span class="required">*</span></label>
                        <select class="form-select" name="country" required>
                            <option value="">Select Country</option>
                            <option value="US" {{ (old('country', $supplier->country ?? 'US') == 'US') ? 'selected' : '' }}>United States</option>
                            <option value="CA" {{ (old('country', $supplier->country ?? 'US') == 'CA') ? 'selected' : '' }}>Canada</option>
                            <option value="GB" {{ (old('country', $supplier->country ?? 'US') == 'GB') ? 'selected' : '' }}>United Kingdom</option>
                            <option value="DE" {{ (old('country', $supplier->country ?? 'US') == 'DE') ? 'selected' : '' }}>Germany</option>
                            <option value="FR" {{ (old('country', $supplier->country ?? 'US') == 'FR') ? 'selected' : '' }}>France</option>
                            <option value="IN" {{ (old('country', $supplier->country ?? 'US') == 'IN') ? 'selected' : '' }}>India</option>
                            <option value="CN" {{ (old('country', $supplier->country ?? 'US') == 'CN') ? 'selected' : '' }}>China</option>
                            <option value="JP" {{ (old('country', $supplier->country ?? 'US') == 'JP') ? 'selected' : '' }}>Japan</option>
                            <option value="AU" {{ (old('country', $supplier->country ?? 'US') == 'AU') ? 'selected' : '' }}>Australia</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Business Terms -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-handshake"></i>
                    Business Terms & Conditions
                </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Payment Terms</label>
                            <select class="form-select" name="payment_terms">
                                <option value="net_15" {{ (old('payment_terms', $supplier->payment_terms ?? 'net_30') == 'net_15') ? 'selected' : '' }}>Net 15 Days</option>
                                <option value="net_30" {{ (old('payment_terms', $supplier->payment_terms ?? 'net_30') == 'net_30') ? 'selected' : '' }}>Net 30 Days</option>
                                <option value="net_45" {{ (old('payment_terms', $supplier->payment_terms ?? 'net_30') == 'net_45') ? 'selected' : '' }}>Net 45 Days</option>
                                <option value="net_60" {{ (old('payment_terms', $supplier->payment_terms ?? 'net_30') == 'net_60') ? 'selected' : '' }}>Net 60 Days</option>
                                <option value="due_on_receipt" {{ (old('payment_terms', $supplier->payment_terms ?? 'net_30') == 'due_on_receipt') ? 'selected' : '' }}>Due on Receipt</option>
                                <option value="prepaid" {{ (old('payment_terms', $supplier->payment_terms ?? 'net_30') == 'prepaid') ? 'selected' : '' }}>Prepaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Credit Limit</label>
                            <input type="number" class="form-control" name="credit_limit" step="0.01"
                                   value="{{ old('credit_limit', $supplier->credit_limit ?? '50000.00') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Currency</label>
                            <select class="form-select" name="currency">
                                <option value="USD" {{ (old('currency', $supplier->currency ?? 'USD') == 'USD') ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ (old('currency', $supplier->currency ?? 'USD') == 'EUR') ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ (old('currency', $supplier->currency ?? 'USD') == 'GBP') ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="CAD" {{ (old('currency', $supplier->currency ?? 'USD') == 'CAD') ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                <option value="INR" {{ (old('currency', $supplier->currency ?? 'USD') == 'INR') ? 'selected' : '' }}>INR - Indian Rupee</option>
                                <option value="JPY" {{ (old('currency', $supplier->currency ?? 'USD') == 'JPY') ? 'selected' : '' }}>JPY - Japanese Yen</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Lead Time (Days)</label>
                            <input type="number" class="form-control" name="lead_time"
                                   value="{{ old('lead_time', $supplier->lead_time ?? '14') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Minimum Order Value</label>
                            <input type="number" class="form-control" name="min_order_value" step="0.01"
                                   value="{{ old('min_order_value', $supplier->min_order_value ?? '1000.00') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Discount Terms</label>
                            <input type="text" class="form-control" name="discount_terms"
                                   value="{{ old('discount_terms', $supplier->discount_terms ?? '2% 10 Net 30') }}"
                                   placeholder="e.g., 2% 10 Net 30">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Information -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-university"></i>
                    Bank Information
                </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name"
                                   value="{{ old('bank_name', $supplier->bank_name ?? 'Chase Bank') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Account Number</label>
                            <input type="text" class="form-control" name="account_number"
                                   value="{{ old('account_number', $supplier->account_number ?? '****5678') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Routing Number</label>
                            <input type="text" class="form-control" name="routing_number"
                                   value="{{ old('routing_number', $supplier->routing_number ?? '021000021') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">SWIFT Code</label>
                            <input type="text" class="form-control" name="swift_code"
                                   value="{{ old('swift_code', $supplier->swift_code ?? 'CHASUS33') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Bank Address</label>
                            <textarea class="form-control" name="bank_address" rows="2"
                                      placeholder="Complete bank address">{{ old('bank_address', $supplier->bank_address ?? '270 Park Avenue, New York, NY 10017, USA') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Additional Information
                </h4>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Products/Services Offered</label>
                            <textarea class="form-control" name="products_services" rows="3"
                                      placeholder="Describe the main products or services offered by this supplier">{{ old('products_services', $supplier->products_services ?? 'Enterprise software solutions, cloud computing services, IT consulting, system integration, and technical support services.') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Internal Notes</label>
                            <textarea class="form-control" name="notes" rows="3"
                                      placeholder="Internal notes about this supplier (not visible to supplier)">{{ old('notes', $supplier->notes ?? 'Reliable partner with excellent track record. Preferred vendor for technology solutions.') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Certifications</label>
                            <input type="text" class="form-control" name="certifications"
                                   value="{{ old('certifications', $supplier->certifications ?? 'ISO 9001, ISO 27001, SOC 2') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Insurance Information</label>
                            <input type="text" class="form-control" name="insurance"
                                   value="{{ old('insurance', $supplier->insurance ?? 'General Liability: $2M, Professional: $1M') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-file-alt"></i>
                    Documents
                </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Business License</label>
                            @if(isset($supplier->business_license) && $supplier->business_license)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $supplier->business_license) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            </div>
                            @endif
                            <input type="file" class="form-control" name="business_license" accept=".pdf,.doc,.docx,.jpg,.png">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tax Certificate</label>
                            @if(isset($supplier->tax_certificate) && $supplier->tax_certificate)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $supplier->tax_certificate) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            </div>
                            @endif
                            <input type="file" class="form-control" name="tax_certificate" accept=".pdf,.doc,.docx,.jpg,.png">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Insurance Certificate</label>
                            @if(isset($supplier->insurance_certificate) && $supplier->insurance_certificate)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $supplier->insurance_certificate) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            </div>
                            @endif
                            <input type="file" class="form-control" name="insurance_certificate" accept=".pdf,.doc,.docx,.jpg,.png">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Quality Certificates</label>
                            @if(isset($supplier->quality_certificates) && $supplier->quality_certificates)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $supplier->quality_certificates) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            </div>
                            @endif
                            <input type="file" class="form-control" name="quality_certificates" accept=".pdf,.doc,.docx,.jpg,.png">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset Changes
                        </button>
                        <a href="{{ route('suppliers.show', $supplier->id ?? 1) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-action">
                            <i class="fas fa-save"></i> Update Supplier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to update this supplier's information?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    This will modify the supplier record and may affect existing purchase orders and relationships.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Confirm Update</button>
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
        e.preventDefault();

        // Show confirmation modal
        $('#confirmModal').modal('show');
    });

    // Phone number formatting
    $('input[name="phone"], input[name="mobile"], input[name="fax"]').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 10) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        }
        $(this).val(value);
    });

    // Website URL formatting
    $('input[name="website"]').on('blur', function() {
        let value = $(this).val();
        if (value && !value.startsWith('http://') && !value.startsWith('https://')) {
            $(this).val('https://' + value);
        }
    });

    // File upload preview
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        if (file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);

            // Show file info
            const fileInfo = $('<div class="file-info mt-1 text-muted">').html(
                `<i class="fas fa-file"></i> ${fileName} (${fileSize} MB)`
            );

            $(this).siblings('.file-info').remove();
            $(this).after(fileInfo);
        }
    });

    // Auto-save functionality (every 30 seconds)
    let autoSaveTimer = setInterval(function() {
        saveFormData();
    }, 30000);

    // Real-time form changes detection
    let hasChanges = false;
    $('#supplierForm input, #supplierForm select, #supplierForm textarea').on('change input', function() {
        hasChanges = true;

        // Show unsaved changes indicator
        if (!$('.unsaved-indicator').length) {
            $('.section-title').first().append('<span class="unsaved-indicator badge bg-warning ms-2">Unsaved Changes</span>');
        }
    });

    // Warn before leaving page with unsaved changes
    $(window).on('beforeunload', function(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});

function resetForm() {
    if (confirm('Are you sure you want to reset all changes? This will restore the original values.')) {
        document.getElementById('supplierForm').reset();
        $('.file-info').remove();
        $('.unsaved-indicator').remove();
        hasChanges = false;
    }
}

function submitForm() {
    $('#confirmModal').modal('hide');

    // Show loading state
    const submitBtn = $('.btn-action');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);

    // Submit the form
    setTimeout(function() {
        document.getElementById('supplierForm').submit();
    }, 500);
}

function saveFormData() {
    // Auto-save form data to localStorage
    const formData = {};
    $('#supplierForm input, #supplierForm select, #supplierForm textarea').each(function() {
        if (this.type !== 'file') {
            formData[this.name] = $(this).val();
        }
    });

    localStorage.setItem('supplierEditData', JSON.stringify(formData));
}

function loadFormData() {
    // Load saved form data from localStorage
    const savedData = localStorage.getItem('supplierEditData');
    if (savedData) {
        const formData = JSON.parse(savedData);

        Object.keys(formData).forEach(function(key) {
            const field = $(`[name="${key}"]`);
            if (field.length && formData[key]) {
                field.val(formData[key]);
            }
        });
    }
}

// Currency formatting for monetary fields
$('input[name="credit_limit"], input[name="min_order_value"]').on('blur', function() {
    const value = parseFloat($(this).val());
    if (!isNaN(value)) {
        $(this).val(value.toFixed(2));
    }
});

// Dynamic validation feedback
$('#supplierForm input[required], #supplierForm select[required]').on('blur', function() {
    const field = $(this);
    const value = field.val();

    // Remove existing feedback
    field.removeClass('is-valid is-invalid');
    field.siblings('.invalid-feedback, .valid-feedback').remove();

    if (!value) {
        field.addClass('is-invalid');
        field.after('<div class="invalid-feedback">This field is required.</div>');
    } else {
        field.addClass('is-valid');
        field.after('<div class="valid-feedback">Looks good!</div>');
    }
});

// Email validation
$('input[name="email"]').on('blur', function() {
    const email = $(this).val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    $(this).removeClass('is-valid is-invalid');
    $(this).siblings('.invalid-feedback, .valid-feedback').remove();

    if (email && !emailRegex.test(email)) {
        $(this).addClass('is-invalid');
        $(this).after('<div class="invalid-feedback">Please enter a valid email address.</div>');
    } else if (email) {
        $(this).addClass('is-valid');
        $(this).after('<div class="valid-feedback">Valid email address.</div>');
    }
});
</script>
@endpush