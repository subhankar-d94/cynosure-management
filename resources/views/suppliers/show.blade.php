@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Supplier Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">{{ $supplier->company_name ?? 'Sample Supplier Corp' }}</h3>
                        <small class="text-muted">Supplier ID: {{ $supplier->id ?? 'SUP-001' }} | Added on {{ $supplier->created_at->format('M d, Y') ?? 'Jan 15, 2024' }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('suppliers.edit', $supplier->id ?? 1) }}">
                                    <i class="fas fa-edit"></i> Edit Supplier
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="rateSupplier()">
                                    <i class="fas fa-star"></i> Rate Supplier
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('suppliers.purchases', $supplier->id ?? 1) }}">
                                    <i class="fas fa-shopping-cart"></i> View Purchases
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('suppliers.materials', $supplier->id ?? 1) }}">
                                    <i class="fas fa-boxes"></i> Materials Catalog
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="exportSupplierData()">
                                    <i class="fas fa-download"></i> Export Data
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="generateReport()">
                                    <i class="fas fa-file-pdf"></i> Generate Report
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                @if(($supplier->status ?? 'active') === 'active')
                                <li><a class="dropdown-item text-warning" href="#" onclick="deactivateSupplier()">
                                    <i class="fas fa-pause"></i> Deactivate
                                </a></li>
                                @else
                                <li><a class="dropdown-item text-success" href="#" onclick="activateSupplier()">
                                    <i class="fas fa-play"></i> Activate
                                </a></li>
                                @endif
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteSupplier()">
                                    <i class="fas fa-trash"></i> Delete Supplier
                                </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Suppliers
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{
                                    ($supplier->status ?? 'active') === 'active' ? 'success' :
                                    (($supplier->status ?? 'active') === 'pending' ? 'warning' :
                                    (($supplier->status ?? 'active') === 'blocked' ? 'danger' : 'secondary'))
                                }} fs-6 me-2">
                                    {{ ucfirst($supplier->status ?? 'active') }}
                                </span>
                                <span class="badge bg-primary">
                                    {{ ucwords(str_replace('_', ' ', $supplier->category ?? 'raw_materials')) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Purchases:</strong><br>
                            <h5 class="text-success mb-0">${{ number_format($supplier->total_purchases ?? 125750.50, 2) }}</h5>
                        </div>
                        <div class="col-md-3">
                            <strong>Rating:</strong><br>
                            <div class="d-flex align-items-center">
                                @php $rating = $supplier->rating ?? 4.2; @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-muted"></i>
                                    @endif
                                @endfor
                                <span class="ms-2">{{ number_format($rating, 1) }}/5</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <strong>Lead Time:</strong><br>
                            <span class="text-muted">{{ $supplier->lead_time ?? 14 }} days</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Company Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Company Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Company Name:</strong><br>
                                    <span>{{ $supplier->company_name ?? 'Sample Supplier Corp' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Registration:</strong><br>
                                    <span class="text-muted">{{ $supplier->business_registration ?? 'REG-123456789' }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Tax ID:</strong><br>
                                    <span class="text-muted">{{ $supplier->tax_id ?? 'TAX-987654321' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Website:</strong><br>
                                    @if($supplier->website ?? 'https://supplier-corp.com')
                                    <a href="{{ $supplier->website ?? 'https://supplier-corp.com' }}" target="_blank" class="text-decoration-none">
                                        {{ str_replace(['http://', 'https://'], '', $supplier->website ?? 'supplier-corp.com') }}
                                        <i class="fas fa-external-link-alt fa-sm"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Products/Services:</strong><br>
                                    <p class="text-muted mb-0">{{ $supplier->products_services ?? 'High-quality raw materials and components for manufacturing. Specializing in steel, aluminum, and composite materials with international quality standards.' }}</p>
                                </div>
                            </div>
                            @if($supplier->certifications ?? 'ISO 9001, FDA Approved, CE Certified')
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Certifications:</strong><br>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach(explode(',', $supplier->certifications ?? 'ISO 9001, FDA Approved, CE Certified') as $cert)
                                        <span class="badge bg-light text-dark">{{ trim($cert) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($supplier->contact_person ?? 'John Smith', 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $supplier->contact_person ?? 'John Smith' }}</h6>
                                    <small class="text-muted">{{ $supplier->designation ?? 'Sales Manager' }}</small>
                                </div>
                            </div>
                            <div class="contact-details">
                                <div class="mb-2">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    <a href="mailto:{{ $supplier->email ?? 'john.smith@supplier-corp.com' }}" class="text-decoration-none">
                                        {{ $supplier->email ?? 'john.smith@supplier-corp.com' }}
                                    </a>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <a href="tel:{{ $supplier->phone ?? '+1-555-123-4567' }}" class="text-decoration-none">
                                        {{ $supplier->phone ?? '+1-555-123-4567' }}
                                    </a>
                                </div>
                                @if($supplier->mobile ?? '+1-555-987-6543')
                                <div class="mb-2">
                                    <i class="fas fa-mobile-alt text-muted me-2"></i>
                                    <a href="tel:{{ $supplier->mobile ?? '+1-555-987-6543' }}" class="text-decoration-none">
                                        {{ $supplier->mobile ?? '+1-555-987-6543' }}
                                    </a>
                                </div>
                                @endif
                                @if($supplier->fax ?? '')
                                <div class="mb-2">
                                    <i class="fas fa-fax text-muted me-2"></i>
                                    <span>{{ $supplier->fax }}</span>
                                </div>
                                @endif
                                <div class="mb-0">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    <span>
                                        {{ $supplier->address ?? '123 Industrial Blvd' }}<br>
                                        {{ $supplier->city ?? 'Manufacturing City' }}, {{ $supplier->state ?? 'State' }} {{ $supplier->postal_code ?? '12345' }}<br>
                                        {{ $supplier->country ?? 'United States' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Business Terms -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Business Terms</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Payment Terms:</strong><br>
                                    <span class="badge bg-light text-dark">
                                        {{ ucwords(str_replace('_', ' ', $supplier->payment_terms ?? 'net_30')) }}
                                    </span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Credit Limit:</strong><br>
                                    <span class="text-success fw-bold">${{ number_format($supplier->credit_limit ?? 50000, 2) }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Currency:</strong><br>
                                    <span>{{ $supplier->currency ?? 'USD' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Lead Time:</strong><br>
                                    <span>{{ $supplier->lead_time ?? 14 }} days</span>
                                </div>
                            </div>
                            @if($supplier->insurance_details ?? 'Comprehensive commercial insurance with $5M coverage')
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Insurance:</strong><br>
                                    <small class="text-muted">{{ $supplier->insurance_details ?? 'Comprehensive commercial insurance with $5M coverage' }}</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Purchase Statistics -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Purchase Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-1">{{ $supplier->total_orders ?? 87 }}</h4>
                                        <small class="text-muted">Total Orders</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success mb-1">${{ number_format($supplier->total_purchases ?? 125750.50, 0) }}</h4>
                                    <small class="text-muted">Total Value</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-info mb-1">{{ $supplier->monthly_orders ?? 12 }}</h4>
                                        <small class="text-muted">This Month</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-warning mb-1">${{ number_format($supplier->avg_order_value ?? 1446.00, 0) }}</h4>
                                    <small class="text-muted">Avg Order</small>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Performance Score:</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ ($supplier->performance_score ?? 85) }}%"></div>
                                    </div>
                                    <span class="fw-bold">{{ $supplier->performance_score ?? 85 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Purchases -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Purchases</h5>
                    <a href="{{ route('suppliers.purchases', $supplier->id ?? 1) }}" class="btn btn-sm btn-outline-primary">
                        View All Purchases
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>PO Number</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($supplier->recent_purchases) && $supplier->recent_purchases->count() > 0)
                                    @foreach($supplier->recent_purchases as $purchase)
                                    <tr>
                                        <td>
                                            <a href="#" class="text-decoration-none">
                                                {{ $purchase->po_number }}
                                            </a>
                                        </td>
                                        <td>{{ $purchase->created_at->format('M d, Y') }}</td>
                                        <td>{{ $purchase->items_count }} items</td>
                                        <td>${{ number_format($purchase->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $purchase->status === 'completed' ? 'success' : ($purchase->status === 'pending' ? 'warning' : 'info') }}">
                                                {{ ucfirst($purchase->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-secondary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <a href="#" class="text-decoration-none">PO-2024-001</a>
                                        </td>
                                        <td>Jan 15, 2024</td>
                                        <td>15 items</td>
                                        <td>$12,450.00</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="#" class="text-decoration-none">PO-2024-002</a>
                                        </td>
                                        <td>Jan 12, 2024</td>
                                        <td>8 items</td>
                                        <td>$8,750.00</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                        <td><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="#" class="text-decoration-none">PO-2024-003</a>
                                        </td>
                                        <td>Jan 10, 2024</td>
                                        <td>22 items</td>
                                        <td>$18,920.00</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Bank Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Bank Information</h5>
                        </div>
                        <div class="card-body">
                            @if($supplier->bank_name ?? 'First National Bank')
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Bank Name:</strong><br>
                                    <span>{{ $supplier->bank_name ?? 'First National Bank' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Account Number:</strong><br>
                                    <span class="text-muted">****{{ substr($supplier->account_number ?? '1234567890', -4) }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Routing Number:</strong><br>
                                    <span class="text-muted">{{ $supplier->routing_number ?? '123456789' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>SWIFT Code:</strong><br>
                                    <span class="text-muted">{{ $supplier->swift_code ?? 'FNBKUS44' }}</span>
                                </div>
                            </div>
                            @else
                            <div class="text-center text-muted">
                                <i class="fas fa-university fa-3x mb-2"></i>
                                <p>No bank information provided</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Documents & Notes -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Documents & Notes</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Documents:</strong>
                                <div class="list-group list-group-flush">
                                    @if($supplier->business_license ?? true)
                                    <div class="list-group-item px-0">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        Business License
                                        <a href="#" class="float-end text-decoration-none">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                    @endif
                                    @if($supplier->tax_certificate ?? true)
                                    <div class="list-group-item px-0">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        Tax Certificate
                                        <a href="#" class="float-end text-decoration-none">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                    @endif
                                    @if($supplier->insurance_certificate ?? true)
                                    <div class="list-group-item px-0">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        Insurance Certificate
                                        <a href="#" class="float-end text-decoration-none">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @if($supplier->notes ?? 'Reliable supplier with excellent quality standards. Preferred vendor for critical components.')
                            <div>
                                <strong>Internal Notes:</strong><br>
                                <p class="text-muted mb-0">{{ $supplier->notes ?? 'Reliable supplier with excellent quality standards. Preferred vendor for critical components.' }}</p>
                            </div>
                            @endif
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
                            <button type="button" class="btn btn-outline-primary w-100" onclick="createPurchaseOrder()">
                                <i class="fas fa-plus"></i> New Purchase Order
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('suppliers.materials', $supplier->id ?? 1) }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-boxes"></i> View Materials
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-success w-100" onclick="contactSupplier()">
                                <i class="fas fa-envelope"></i> Send Message
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-warning w-100" onclick="requestQuote()">
                                <i class="fas fa-calculator"></i> Request Quote
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rate Supplier Modal -->
<div class="modal fade" id="rateSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rate Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rateSupplierForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1-5 stars)</label>
                        <div class="rating-input">
                            <input type="radio" id="star5" name="rating" value="5">
                            <label for="star5" class="star"></label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" class="star"></label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" class="star"></label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" class="star"></label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" class="star"></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ratingComment" class="form-label">Comment (optional)</label>
                        <textarea class="form-control" id="ratingComment" name="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-star"></i> Submit Rating
                    </button>
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

.contact-details {
    font-size: 0.95rem;
}

.contact-details i {
    width: 16px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}

.progress {
    background-color: #e9ecef;
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input .star {
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: block;
    color: #ddd;
    font-size: 24px;
    line-height: 30px;
    text-align: center;
    transition: color 0.2s;
}

.rating-input .star:hover,
.rating-input .star:hover ~ .star,
.rating-input input[type="radio"]:checked ~ .star {
    color: #ffc107;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Rate supplier
    window.rateSupplier = function() {
        $('#rateSupplierModal').modal('show');
    };

    $('#rateSupplierForm').on('submit', function(e) {
        e.preventDefault();

        $.post('{{ route("suppliers.rate", $supplier->id ?? 1) }}', {
            rating: $('input[name="rating"]:checked').val(),
            comment: $('#ratingComment').val(),
            _token: '{{ csrf_token() }}'
        }, function(response) {
            $('#rateSupplierModal').modal('hide');
            location.reload();
            alert('Rating submitted successfully!');
        });
    });

    // Other actions
    window.activateSupplier = function() {
        if (confirm('Activate this supplier?')) {
            $.ajax({
                url: '{{ route("suppliers.update", $supplier->id ?? 1) }}',
                method: 'PUT',
                data: {
                    status: 'active',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    };

    window.deactivateSupplier = function() {
        if (confirm('Deactivate this supplier? This will prevent new orders but preserve existing data.')) {
            $.ajax({
                url: '{{ route("suppliers.update", $supplier->id ?? 1) }}',
                method: 'PUT',
                data: {
                    status: 'inactive',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    };

    window.deleteSupplier = function() {
        if (confirm('Are you sure you want to delete this supplier? This action cannot be undone.')) {
            $.ajax({
                url: '{{ route("suppliers.destroy", $supplier->id ?? 1) }}',
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    window.location.href = '{{ route("suppliers.index") }}';
                }
            });
        }
    };

    window.exportSupplierData = function() {
        window.open('{{ route("suppliers.index") }}?export=1&supplier_ids[]={{ $supplier->id ?? 1 }}', '_blank');
    };

    window.generateReport = function() {
        alert('Generating comprehensive supplier report...');
        // Implementation for generating detailed supplier report
    };

    window.createPurchaseOrder = function() {
        alert('Redirecting to create new purchase order for this supplier...');
        // Implementation for creating purchase order
    };

    window.contactSupplier = function() {
        alert('Opening email composer to contact supplier...');
        // Implementation for contacting supplier
    };

    window.requestQuote = function() {
        alert('Opening quote request form...');
        // Implementation for requesting quote
    };
});
</script>
@endpush