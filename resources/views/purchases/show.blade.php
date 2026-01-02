@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">{{ $purchase->purchase_order_number }}</h3>
                <small class="text-muted">Purchase Order Details</small>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-{{
                    $purchase->status === 'completed' ? 'success' :
                    ($purchase->status === 'approved' ? 'info' :
                    ($purchase->status === 'pending' ? 'warning' : 'secondary'))
                }} fs-6">
                    {{ ucfirst($purchase->status) }}
                </span>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;"><strong>PO Number:</strong></td>
                            <td>{{ $purchase->purchase_order_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Order Date:</strong></td>
                            <td>{{ date('M d, Y', strtotime($purchase->purchase_date)) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{
                                    $purchase->status === 'completed' ? 'success' :
                                    ($purchase->status === 'approved' ? 'info' :
                                    ($purchase->status === 'pending' ? 'warning' : 'secondary'))
                                }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Created:</strong></td>
                            <td>{{ $purchase->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supplier Information -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Supplier Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="avatar-circle me-3">
                            {{ strtoupper(substr($purchase->supplier->company_name, 0, 2)) }}
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $purchase->supplier->company_name }}</h6>
                            <small class="text-muted">Supplier</small>
                        </div>
                    </div>
                    <div class="supplier-details">
                        @if($purchase->supplier->contact_person)
                        <p class="mb-2">
                            <i class="fas fa-user text-muted me-2"></i>
                            <strong>Contact:</strong> {{ $purchase->supplier->contact_person }}
                        </p>
                        @endif
                        @if($purchase->supplier->email)
                        <p class="mb-2">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            <a href="mailto:{{ $purchase->supplier->email }}">{{ $purchase->supplier->email }}</a>
                        </p>
                        @endif
                        @if($purchase->supplier->phone)
                        <p class="mb-2">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <a href="tel:{{ $purchase->supplier->phone }}">{{ $purchase->supplier->phone }}</a>
                        </p>
                        @endif
                        @if($purchase->supplier->address)
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            {{ $purchase->supplier->address }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Order Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Description</th>
                            <th width="120" class="text-center">Quantity</th>
                            <th width="150" class="text-end">Unit Price</th>
                            <th width="150" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->description ?? $item->material_name }}</strong>
                            </td>
                            <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-end">₹{{ number_format($item->unit_price ?? $item->unit_cost, 2) }}</td>
                            <td class="text-end">₹{{ number_format($item->quantity * ($item->unit_price ?? $item->unit_cost), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-md-6 offset-md-6">
            <div class="card">
                <div class="card-body">
                    <table class="table mb-0">
                        <tr>
                            <td class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end" style="width: 150px;">₹{{ number_format($purchase->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end"><strong>Tax:</strong></td>
                            <td class="text-end">₹{{ number_format($purchase->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="table-active">
                            <td class="text-end"><strong>Total Amount:</strong></td>
                            <td class="text-end"><h5 class="mb-0">₹{{ number_format($purchase->total_amount, 2) }}</h5></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-4 mb-4">
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
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

.supplier-details {
    font-size: 0.95rem;
}

.supplier-details i {
    width: 20px;
    text-align: center;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
}
</style>
@endpush
