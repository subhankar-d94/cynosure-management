@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Supplier Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">{{ $supplier->company_name }}</h3>
                        <small class="text-muted">Supplier ID: #{{ $supplier->id }} | Added on {{ $supplier->created_at->format('M d, Y') }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('suppliers.edit', $supplier) }}">
                                    <i class="fas fa-edit"></i> Edit Supplier
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                @if($supplier->status === 'active')
                                <li><a class="dropdown-item text-warning" href="#" onclick="changeStatus('inactive')">
                                    <i class="fas fa-pause"></i> Deactivate
                                </a></li>
                                @else
                                <li><a class="dropdown-item text-success" href="#" onclick="changeStatus('active')">
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
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{
                                    $supplier->status === 'active' ? 'success' :
                                    ($supplier->status === 'pending' ? 'warning' : 'secondary')
                                }} fs-6">
                                    {{ ucfirst($supplier->status) }}
                                </span>
                                <span class="badge bg-primary fs-6">
                                    {{ ucwords(str_replace('_', ' ', $supplier->category)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Company Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-building me-2"></i>Company Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;"><strong>Company Name:</strong></td>
                                    <td>{{ $supplier->company_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Category:</strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ ucwords(str_replace('_', ' ', $supplier->category)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{
                                            $supplier->status === 'active' ? 'success' :
                                            ($supplier->status === 'pending' ? 'warning' : 'secondary')
                                        }}">
                                            {{ ucfirst($supplier->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($supplier->website)
                                <tr>
                                    <td class="text-muted"><strong>Website:</strong></td>
                                    <td>
                                        <a href="{{ $supplier->website }}" target="_blank" class="text-decoration-none">
                                            {{ str_replace(['http://', 'https://'], '', $supplier->website) }}
                                            <i class="fas fa-external-link-alt fa-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endif
                                @if($supplier->gst_number)
                                <tr>
                                    <td class="text-muted"><strong>GST Number:</strong></td>
                                    <td>{{ $supplier->gst_number }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($supplier->contact_person, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $supplier->contact_person }}</h6>
                                    <small class="text-muted">Primary Contact</small>
                                </div>
                            </div>
                            <div class="contact-details">
                                @if($supplier->email)
                                <div class="mb-3">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                        {{ $supplier->email }}
                                    </a>
                                </div>
                                @endif
                                @if($supplier->phone)
                                <div class="mb-3">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <a href="tel:{{ $supplier->phone }}" class="text-decoration-none">
                                        {{ $supplier->phone }}
                                    </a>
                                </div>
                                @endif
                                @if($supplier->address)
                                <div class="mb-0">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    <span>{{ $supplier->address }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Purchases -->
            @if($supplier->purchases && $supplier->purchases->count() > 0)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-shopping-cart me-2"></i>Recent Purchases</h5>
                    @if($supplier->purchases->count() > 5)
                    <span class="text-muted">Showing 5 of {{ $supplier->purchases->count() }} purchases</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>PO Number</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplier->purchases->take(5) as $purchase)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-decoration-none">
                                            {{ $purchase->reference_number }}
                                        </a>
                                    </td>
                                    <td>{{ $purchase->created_at->format('M d, Y') }}</td>
                                    <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{
                                            $purchase->status === 'completed' ? 'success' :
                                            ($purchase->status === 'pending' ? 'warning' :
                                            ($purchase->status === 'approved' ? 'info' : 'secondary'))
                                        }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-shopping-cart me-2"></i>Recent Purchases</h5>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No purchases found for this supplier</p>
                    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Purchase Order
                    </a>
                </div>
            </div>
            @endif

            <!-- Additional Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Created:</strong> {{ $supplier->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Last Updated:</strong> {{ $supplier->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation -->
<form id="deleteForm" action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
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
    width: 20px;
    text-align: center;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-weight: 500;
}

.table td {
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    window.changeStatus = function(newStatus) {
        const action = newStatus === 'active' ? 'activate' : 'deactivate';
        if (confirm(`Are you sure you want to ${action} this supplier?`)) {
            $.ajax({
                url: '{{ route("suppliers.update", $supplier) }}',
                method: 'PUT',
                data: {
                    company_name: '{{ $supplier->company_name }}',
                    contact_person: '{{ $supplier->contact_person }}',
                    category: '{{ $supplier->category }}',
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error updating supplier status. Please try again.');
                }
            });
        }
    };

    window.deleteSupplier = function() {
        if (confirm('Are you sure you want to delete this supplier? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    };
});
</script>
@endpush
