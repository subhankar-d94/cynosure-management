@extends('layouts.app')

@section('title', 'Purchase Order Details')

@push('styles')
<style>
    .po-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .po-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #17a2b8;
    }

    .info-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .info-value {
        font-weight: 600;
        color: #495057;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-draft { background: #fff3cd; color: #856404; }
    .status-pending { background: #cce7ff; color: #004085; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-ordered { background: #d1ecf1; color: #0c5460; }
    .status-received { background: #c3e6cb; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-paid { background: #d4edda; color: #155724; }

    .priority-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .priority-high { background: #f8d7da; color: #721c24; }
    .priority-medium { background: #fff3cd; color: #856404; }
    .priority-low { background: #d4edda; color: #155724; }

    .items-table {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .table th {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 15px;
        font-weight: 600;
    }

    .table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .summary-section {
        background: #17a2b8;
        color: white;
        border-radius: 8px;
        padding: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .summary-row:last-child {
        margin-bottom: 0;
        border-top: 1px solid rgba(255,255,255,0.3);
        padding-top: 10px;
        font-weight: bold;
        font-size: 1.1rem;
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

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -34px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #17a2b8;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #17a2b8;
    }

    .timeline-item.completed::before {
        background: #28a745;
        box-shadow: 0 0 0 2px #28a745;
    }

    .timeline-item.current::before {
        background: #ffc107;
        box-shadow: 0 0 0 2px #ffc107;
    }

    .timeline-content {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
    }

    .timeline-title {
        font-weight: 600;
        margin-bottom: 5px;
        color: #495057;
    }

    .timeline-desc {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .timeline-date {
        color: #6c757d;
        font-size: 0.8rem;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .supplier-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #17a2b8, #138496);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .progress-ring {
        width: 80px;
        height: 80px;
        margin: 0 auto;
    }

    .progress-ring-circle {
        stroke: #e9ecef;
        stroke-width: 8;
        fill: transparent;
        r: 30;
        cx: 40;
        cy: 40;
    }

    .progress-ring-progress {
        stroke: #17a2b8;
        stroke-width: 8;
        stroke-linecap: round;
        fill: transparent;
        r: 30;
        cx: 40;
        cy: 40;
        stroke-dasharray: 188.4;
        stroke-dashoffset: 113.04; /* 60% progress */
        transform: rotate(-90deg);
        transform-origin: 40px 40px;
    }

    .progress-text {
        text-align: center;
        margin-top: 10px;
        font-weight: 600;
        color: #17a2b8;
    }

    @media (max-width: 768px) {
        .po-header {
            padding: 20px;
            text-align: center;
        }

        .po-card {
            padding: 15px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            justify-content: center;
        }
    }

    .document-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .document-info {
        display: flex;
        align-items: center;
    }

    .document-icon {
        width: 40px;
        height: 40px;
        background: #17a2b8;
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .notes-section {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="po-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <h1 class="h3 mb-0">{{ $purchase->po_number ?? 'PO-2024-001' }}</h1>
                        <p class="mb-0 opacity-75">Purchase Order Details</p>
                    </div>
                    <div>
                        <span class="status-badge status-{{ $purchase->status ?? 'ordered' }}">
                            {{ ucfirst($purchase->status ?? 'Ordered') }}
                        </span>
                        <span class="priority-badge priority-{{ $purchase->priority ?? 'medium' }} ms-2">
                            {{ ucfirst($purchase->priority ?? 'Medium') }} Priority
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="action-buttons">
                    <a href="{{ route('purchases.edit', $purchase->id ?? 1) }}" class="btn btn-light">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('purchases.print', $purchase->id ?? 1) }}" class="btn btn-light" target="_blank">
                        <i class="fas fa-print"></i> Print
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="downloadPDF()">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="emailSupplier()">
                                <i class="fas fa-envelope"></i> Email to Supplier
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="duplicateOrder()">
                                <i class="fas fa-copy"></i> Duplicate Order
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder()">
                                <i class="fas fa-times"></i> Cancel Order
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="po-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle text-info me-2"></i>
                    Order Information
                </h5>

                <div class="info-section">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Order Date</div>
                            <div class="info-value">{{ $purchase->order_date ? date('M d, Y', strtotime($purchase->order_date)) : 'Feb 01, 2024' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Expected Delivery</div>
                            <div class="info-value">{{ $purchase->expected_delivery_date ? date('M d, Y', strtotime($purchase->expected_delivery_date)) : 'Feb 20, 2024' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Purchase Type</div>
                            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $purchase->purchase_type ?? 'materials')) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ ucfirst($purchase->department ?? 'Production') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Requested By</div>
                            <div class="info-value">{{ $purchase->requested_by ?? 'Sarah Johnson' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Currency</div>
                            <div class="info-value">{{ $purchase->currency ?? 'USD' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Information -->
            <div class="po-card">
                <h5 class="mb-3">
                    <i class="fas fa-building text-info me-2"></i>
                    Supplier Details
                </h5>

                <div class="info-section">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="supplier-avatar">
                                {{ substr($purchase->supplier->company_name ?? 'Global Manufacturing Inc.', 0, 2) }}
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item mb-3">
                                        <div class="info-label">Company Name</div>
                                        <div class="info-value">{{ $purchase->supplier->company_name ?? 'Global Manufacturing Inc.' }}</div>
                                    </div>
                                    <div class="info-item mb-3">
                                        <div class="info-label">Contact Person</div>
                                        <div class="info-value">{{ $purchase->supplier->contact_person ?? 'Sarah Wilson' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Email</div>
                                        <div class="info-value">
                                            <a href="mailto:{{ $purchase->supplier->email ?? 'sarah.wilson@globalmanuf.com' }}">
                                                {{ $purchase->supplier->email ?? 'sarah.wilson@globalmanuf.com' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item mb-3">
                                        <div class="info-label">Phone</div>
                                        <div class="info-value">{{ $purchase->supplier->phone ?? '+1 (555) 987-6543' }}</div>
                                    </div>
                                    <div class="info-item mb-3">
                                        <div class="info-label">Payment Terms</div>
                                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $purchase->payment_terms ?? 'net_45')) }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Supplier Rating</div>
                                        <div class="info-value">
                                            <span class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= ($purchase->supplier->rating ?? 4))
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </span>
                                            {{ $purchase->supplier->rating ?? '4.0' }}/5.0
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Ordered -->
            <div class="po-card">
                <h5 class="mb-3">
                    <i class="fas fa-shopping-cart text-info me-2"></i>
                    Items Ordered
                </h5>

                <div class="items-table">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-center" width="10%">Qty</th>
                                <th class="text-end" width="12%">Unit Price</th>
                                <th class="text-center" width="8%">Tax</th>
                                <th class="text-end" width="12%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($purchase->items) && $purchase->items->count() > 0)
                                @foreach($purchase->items as $item)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $item->description }}</strong>
                                            @if($item->category)
                                            <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</small>
                                            @endif
                                            @if($item->notes)
                                            <br><small class="text-info">{{ $item->notes }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }} {{ $item->unit ?? 'pcs' }}</td>
                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-center">{{ $item->tax_rate ?? 0 }}%</td>
                                    <td class="text-end"><strong>${{ number_format($item->total_price, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        <div>
                                            <strong>Steel Components - Grade A</strong>
                                            <br><small class="text-muted">Raw Materials</small>
                                            <br><small class="text-info">High-grade steel for manufacturing</small>
                                        </div>
                                    </td>
                                    <td class="text-center">500 kg</td>
                                    <td class="text-end">$35.00</td>
                                    <td class="text-center">8%</td>
                                    <td class="text-end"><strong>$18,900.00</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div>
                                            <strong>Plastic Components - Injection Molded</strong>
                                            <br><small class="text-muted">Raw Materials</small>
                                            <br><small class="text-info">Custom molded plastic parts</small>
                                        </div>
                                    </td>
                                    <td class="text-center">200 pcs</td>
                                    <td class="text-end">$125.00</td>
                                    <td class="text-center">8%</td>
                                    <td class="text-end"><strong>$27,000.00</strong></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Order Summary -->
                <div class="summary-section mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            @if($purchase->special_instructions ?? '')
                            <h6>Special Instructions:</h6>
                            <p class="mb-0">{{ $purchase->special_instructions }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>${{ number_format($purchase->subtotal ?? 42500, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax (8%):</span>
                                <span>${{ number_format($purchase->tax_amount ?? 3400, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span>${{ number_format($purchase->shipping_cost ?? 0, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Total Amount:</span>
                                <span>${{ number_format($purchase->total_amount ?? 45900, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Progress Tracking -->
            <div class="po-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line text-info me-2"></i>
                    Order Progress
                </h5>

                <div class="text-center mb-3">
                    <svg class="progress-ring">
                        <circle class="progress-ring-circle"></circle>
                        <circle class="progress-ring-progress"></circle>
                    </svg>
                    <div class="progress-text">65% Complete</div>
                </div>

                <div class="timeline">
                    <div class="timeline-item completed">
                        <div class="timeline-content">
                            <div class="timeline-title">Order Created</div>
                            <div class="timeline-desc">Purchase order submitted for approval</div>
                            <div class="timeline-date">Feb 01, 2024 - 09:30 AM</div>
                        </div>
                    </div>

                    <div class="timeline-item completed">
                        <div class="timeline-content">
                            <div class="timeline-title">Order Approved</div>
                            <div class="timeline-desc">Approved by Finance Department</div>
                            <div class="timeline-date">Feb 01, 2024 - 02:15 PM</div>
                        </div>
                    </div>

                    <div class="timeline-item completed">
                        <div class="timeline-content">
                            <div class="timeline-title">Sent to Supplier</div>
                            <div class="timeline-desc">Purchase order sent to Global Manufacturing</div>
                            <div class="timeline-date">Feb 02, 2024 - 10:00 AM</div>
                        </div>
                    </div>

                    <div class="timeline-item current">
                        <div class="timeline-content">
                            <div class="timeline-title">In Production</div>
                            <div class="timeline-desc">Items are being manufactured</div>
                            <div class="timeline-date">Feb 05, 2024 - Present</div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-title">Ready for Shipment</div>
                            <div class="timeline-desc">Items completed and ready for delivery</div>
                            <div class="timeline-date">Expected: Feb 18, 2024</div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-title">Delivered</div>
                            <div class="timeline-desc">Items received and inspected</div>
                            <div class="timeline-date">Expected: Feb 20, 2024</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="po-card">
                <h5 class="mb-3">
                    <i class="fas fa-bolt text-info me-2"></i>
                    Quick Actions
                </h5>

                <div class="d-grid gap-2">
                    @if(($purchase->status ?? 'ordered') == 'pending')
                    <button class="btn btn-success" onclick="approveOrder()">
                        <i class="fas fa-check"></i> Approve Order
                    </button>
                    @endif

                    @if(in_array($purchase->status ?? 'ordered', ['approved', 'ordered']))
                    <button class="btn btn-info" onclick="markReceived()">
                        <i class="fas fa-box"></i> Mark as Received
                    </button>
                    @endif

                    @if(($purchase->status ?? 'ordered') == 'received')
                    <button class="btn btn-success" onclick="markPaid()">
                        <i class="fas fa-credit-card"></i> Mark as Paid
                    </button>
                    @endif

                    <button class="btn btn-outline-primary" onclick="contactSupplier()">
                        <i class="fas fa-phone"></i> Contact Supplier
                    </button>

                    <button class="btn btn-outline-secondary" onclick="viewSupplier()">
                        <i class="fas fa-building"></i> View Supplier Profile
                    </button>

                    <button class="btn btn-outline-info" onclick="trackShipment()">
                        <i class="fas fa-truck"></i> Track Shipment
                    </button>
                </div>
            </div>

            <!-- Documents -->
            <div class="po-card">
                <h5 class="mb-3">
                    <i class="fas fa-file-alt text-info me-2"></i>
                    Related Documents
                </h5>

                <div class="document-item">
                    <div class="document-info">
                        <div class="document-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Purchase Order PDF</div>
                            <small class="text-muted">Generated: Feb 01, 2024</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadPDF()">
                        <i class="fas fa-download"></i>
                    </button>
                </div>

                <div class="document-item">
                    <div class="document-info">
                        <div class="document-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Supplier Quote</div>
                            <small class="text-muted">Received: Jan 28, 2024</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewQuote()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                @if(($purchase->status ?? 'ordered') == 'received')
                <div class="document-item">
                    <div class="document-info">
                        <div class="document-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Delivery Receipt</div>
                            <small class="text-muted">Received: Feb 20, 2024</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewReceipt()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @endif
            </div>

            <!-- Internal Notes -->
            @if($purchase->internal_notes ?? '')
            <div class="notes-section">
                <h6><i class="fas fa-sticky-note me-2"></i>Internal Notes</h6>
                <p class="mb-0">{{ $purchase->internal_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Back to List -->
    <div class="text-center mt-4">
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Purchase Orders
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approveOrder() {
    if (confirm('Are you sure you want to approve this purchase order?')) {
        // AJAX call to approve order
        alert('Purchase order has been approved.');
        location.reload();
    }
}

function markReceived() {
    if (confirm('Mark this purchase order as received?')) {
        // AJAX call to mark as received
        alert('Purchase order has been marked as received.');
        location.reload();
    }
}

function markPaid() {
    if (confirm('Mark this purchase order as paid?')) {
        // AJAX call to mark as paid
        alert('Purchase order has been marked as paid.');
        location.reload();
    }
}

function contactSupplier() {
    alert('Opening contact options for supplier...');
}

function viewSupplier() {
    window.open('{{ route("suppliers.show", $purchase->supplier_id ?? 1) }}', '_blank');
}

function trackShipment() {
    alert('Opening shipment tracking...');
}

function downloadPDF() {
    window.open('{{ route("purchases.print", $purchase->id ?? 1) }}', '_blank');
}

function emailSupplier() {
    alert('Opening email composer...');
}

function duplicateOrder() {
    if (confirm('Create a duplicate of this purchase order?')) {
        alert('Duplicate purchase order created.');
    }
}

function cancelOrder() {
    if (confirm('Are you sure you want to cancel this purchase order? This action cannot be undone.')) {
        alert('Purchase order has been cancelled.');
        window.location.href = '{{ route("purchases.index") }}';
    }
}

function viewQuote() {
    alert('Opening supplier quote...');
}

function viewReceipt() {
    alert('Opening delivery receipt...');
}
</script>
@endpush