@extends('layouts.app')

@php
function formatDate($date, $default = 'N/A') {
    if (!$date) return $default;
    if (is_string($date)) {
        try {
            return \Carbon\Carbon::parse($date)->format('M d, Y h:i A');
        } catch (\Exception $e) {
            return $default;
        }
    }
    return $date->format('M d, Y h:i A');
}

function formatDateOnly($date, $default = 'N/A') {
    if (!$date) return $default;
    if (is_string($date)) {
        try {
            return \Carbon\Carbon::parse($date)->format('M d, Y');
        } catch (\Exception $e) {
            return $default;
        }
    }
    return $date->format('M d, Y');
}
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Invoice Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">Invoice #{{ $invoice->invoice_number ?? 'INV-2024-12345' }}</h3>
                        <small class="text-muted">Created on {{ formatDate($invoice->created_at ?? null, 'Jan 15, 2024 10:30 AM') }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('invoices.edit', $invoice->id ?? 1) }}">
                                    <i class="fas fa-edit"></i> Edit Invoice
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('invoices.preview', $invoice->id ?? 1) }}" target="_blank">
                                    <i class="fas fa-eye"></i> Preview
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="sendInvoice()">
                                    <i class="fas fa-paper-plane"></i> Send Invoice
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('invoices.download', $invoice->id ?? 1) }}">
                                    <i class="fas fa-download"></i> Download PDF
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('invoices.print', $invoice->id ?? 1) }}" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                @if(($invoice->status ?? 'draft') !== 'paid')
                                <li><a class="dropdown-item" href="#" onclick="markAsPaid()">
                                    <i class="fas fa-check"></i> Mark as Paid
                                </a></li>
                                @endif
                                @if(($invoice->status ?? 'draft') === 'draft')
                                <li><a class="dropdown-item" href="#" onclick="markAsSent()">
                                    <i class="fas fa-paper-plane"></i> Mark as Sent
                                </a></li>
                                @endif
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteInvoice()">
                                    <i class="fas fa-trash"></i> Delete Invoice
                                </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{
                                    ($invoice->status ?? 'draft') === 'paid' ? 'success' :
                                    (($invoice->status ?? 'draft') === 'sent' ? 'primary' :
                                    (($invoice->status ?? 'draft') === 'overdue' ? 'danger' :
                                    (($invoice->status ?? 'draft') === 'viewed' ? 'info' : 'secondary')))
                                }} fs-6 me-2">
                                    {{ ucfirst($invoice->status ?? 'draft') }}
                                </span>
                                @if(($invoice->due_date ?? '2024-02-15') < date('Y-m-d') && ($invoice->status ?? 'draft') !== 'paid')
                                <span class="badge bg-warning">Overdue</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <strong>Issue Date:</strong><br>
                            <span class="text-muted">{{ formatDateOnly($invoice->issue_date ?? null, 'Jan 15, 2024') }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Due Date:</strong><br>
                            <span class="text-muted {{ ($invoice->due_date ?? '2024-02-15') < date('Y-m-d') && ($invoice->status ?? 'draft') !== 'paid' ? 'text-danger fw-bold' : '' }}">
                                {{ formatDateOnly($invoice->due_date ?? null, 'Feb 15, 2024') }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Amount:</strong><br>
                            <h5 class="text-primary mb-0">₹{{ number_format((float)($invoice->total ?? 0), 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Invoice Details -->
                <div class="col-lg-8">
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Bill To</h5>
                        </div>
                        <div class="card-body">
                            @php
                                // Get customer name from relationship or invoice fields
                                $customerName = $invoice->customer_name ?? ($invoice->custom_name ?? null);
                                $customerEmail = $invoice->customer_email ?? ($invoice->custom_email ?? null);
                                $customerPhone = $invoice->customer_phone ?? null;
                                $customerCompany = $invoice->customer_company ?? null;
                                $customerAddress = $invoice->customer_address ?? ($invoice->billing_address ?? null);

                                // If we have a customer_id, show link to customer profile
                                $hasCustomerProfile = isset($invoice->customer_id) && $invoice->customer_id;
                            @endphp

                            <div class="d-flex align-items-start">
                                @if($customerName)
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($customerName, 0, 2)) }}
                                </div>
                                @endif
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0">{{ $customerName ?? 'Customer Name Not Available' }}</h6>
                                        @if($hasCustomerProfile)
                                        <a href="{{ route('customers.show', $invoice->customer_id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i> View Profile
                                        </a>
                                        @endif
                                    </div>
                                    <p class="text-muted mb-2">
                                        @if($customerCompany)
                                        <strong>{{ $customerCompany }}</strong><br>
                                        @endif
                                        @if($customerEmail)
                                        <i class="fas fa-envelope"></i> {{ $customerEmail }}<br>
                                        @endif
                                        @if($customerPhone)
                                        <i class="fas fa-phone"></i> {{ $customerPhone }}<br>
                                        @endif
                                        @if($customerAddress)
                                        <i class="fas fa-map-marker-alt"></i> {{ $customerAddress }}
                                        @endif
                                        @if(!$customerEmail && !$customerPhone && !$customerAddress)
                                        <em class="text-muted">No contact information available</em>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Invoice Items</h5>
                            <span class="badge bg-primary">{{ (isset($invoice->items) && is_countable($invoice->items)) ? count($invoice->items) : 0 }} Items</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Rate</th>
                                            <th class="text-center">Tax</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($invoice->items) && (is_countable($invoice->items) ? count($invoice->items) : 0) > 0)
                                            @foreach($invoice->items as $item)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $item->description ?? 'Sample Service' }}</strong>
                                                        @if(isset($item->details) && $item->details)
                                                        <br><small class="text-muted">{{ $item->details }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ number_format((float)($item->quantity ?? 0), 3) }}</td>
                                                <td class="text-end">₹{{ number_format((float)($item->rate ?? 0), 2) }}</td>
                                                <td class="text-center">{{ number_format((float)($item->tax_rate ?? 0), 2) }}%</td>
                                                <td class="text-end"><strong>₹{{ number_format((float)($item->amount ?? 0), 2) }}</strong></td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                    No items found for this invoice
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Invoice Summary -->
                            <div class="row justify-content-end">
                                <div class="col-md-4">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Subtotal:</strong></td>
                                            <td class="text-end">₹{{ number_format((float)($invoice->subtotal ?? 0), 2) }}</td>
                                        </tr>
                                        @if(($invoice->discount ?? 0) > 0)
                                        <tr>
                                            <td><strong>Discount:</strong></td>
                                            <td class="text-end text-success">-₹{{ number_format($invoice->discount ?? 0, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Tax:</strong></td>
                                            <td class="text-end">₹{{ number_format((float)($invoice->tax_amount ?? 0), 2) }}</td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td><strong>Total:</strong></td>
                                            <td class="text-end"><strong>₹{{ number_format((float)($invoice->total ?? 0), 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes and Terms -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Notes</h5>
                                </div>
                                <div class="card-body">
                                    @if($invoice->notes ?? 'Thank you for your business!')
                                        <p class="mb-0">{{ $invoice->notes ?? 'Thank you for your business!' }}</p>
                                    @else
                                        <p class="text-muted mb-0">No notes for this invoice.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Terms & Conditions</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $invoice->terms ?? 'Payment is due within 30 days of invoice date. Late payments may incur additional charges.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Payment Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Payment Terms:</strong><br>
                                    <span class="badge bg-light text-dark">
                                        {{ ucwords(str_replace('_', ' ', $invoice->payment_terms ?? 'net_30')) }}
                                    </span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>PO Number:</strong><br>
                                    <span class="text-muted">{{ $invoice->po_number ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Total Amount:</strong><br>
                                    <span class="h5 text-primary">₹{{ number_format((float)($invoice->total ?? 0), 2) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Amount Paid:</strong><br>
                                    <span class="h5 text-success">₹{{ number_format((float)($invoice->paid_amount ?? 0), 2) }}</span>
                                </div>
                            </div>
                            @if((float)($invoice->total ?? 0) > (float)($invoice->paid_amount ?? 0))
                            <div class="mt-3">
                                <div class="alert alert-warning">
                                    <strong>Balance Due:</strong> ₹{{ number_format((float)($invoice->total ?? 0) - (float)($invoice->paid_amount ?? 0), 2) }}
                                </div>
                                @if(($invoice->status ?? 'draft') !== 'paid')
                                <button type="button" class="btn btn-success btn-sm w-100" onclick="markAsPaid()">
                                    <i class="fas fa-check"></i> Mark as Paid
                                </button>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Invoice Timeline -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Invoice Activity</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Invoice Created</h6>
                                        <small class="text-muted">{{ formatDate($invoice->created_at ?? null, 'Jan 15, 2024 10:30 AM') }}</small>
                                    </div>
                                </div>
                                @if(($invoice->status ?? 'draft') !== 'draft')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Invoice Sent</h6>
                                        <small class="text-muted">{{ formatDate($invoice->sent_at ?? null, 'Not available') }}</small>
                                    </div>
                                </div>
                                @endif
                                @if(($invoice->status ?? 'draft') === 'viewed' || ($invoice->status ?? 'draft') === 'paid')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Invoice Viewed</h6>
                                        <small class="text-muted">{{ formatDate($invoice->viewed_at ?? null, 'Not available') }}</small>
                                    </div>
                                </div>
                                @endif
                                @if(($invoice->status ?? 'draft') === 'paid')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Payment Received</h6>
                                        <small class="text-muted">{{ formatDate($invoice->paid_at ?? null, 'Not available') }}</small>
                                    </div>
                                </div>
                                @else
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-light"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-muted">Payment Pending</h6>
                                        <small class="text-muted">Awaiting payment</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="sendInvoice()">
                                    <i class="fas fa-paper-plane"></i> Send Invoice
                                </button>
                                <a href="{{ route('invoices.download', $invoice->id ?? 1) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                                <a href="{{ route('invoices.edit', $invoice->id ?? 1) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Edit Invoice
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="duplicateInvoice()">
                                    <i class="fas fa-copy"></i> Duplicate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Invoice Modal -->
<div class="modal fade" id="sendInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sendInvoiceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipientEmail" class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" id="recipientEmail"
                               value="{{ $invoice->customer_email ?? ($invoice->custom_email ?? '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="emailSubject"
                               value="Invoice {{ $invoice->invoice_number ?? 'Unknown' }} from Your Company">
                    </div>
                    <div class="mb-3">
                        <label for="emailMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="emailMessage" rows="4">Dear {{ $invoice->customer_name ?? ($invoice->custom_name ?? 'Customer') }},

Please find attached your invoice. Payment is due by {{ formatDateOnly($invoice->due_date ?? null, 'the due date specified') }}.

Thank you for your business!

Best regards,
Your Company</textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="attachPDF" checked>
                        <label class="form-check-label" for="attachPDF">
                            Attach PDF copy
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Invoice
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

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 1px #dee2e6;
}

.timeline-content {
    padding-left: 20px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Send Invoice
    window.sendInvoice = function() {
        $('#sendInvoiceModal').modal('show');
    };

    $('#sendInvoiceForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            email: $('#recipientEmail').val(),
            subject: $('#emailSubject').val(),
            message: $('#emailMessage').val(),
            attach_pdf: $('#attachPDF').is(':checked'),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route("invoices.send", $invoice->id ?? 1) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#sendInvoiceModal').modal('hide');
                location.reload();
                alert('Invoice sent successfully!');
            },
            error: function(xhr) {
                alert('Error sending invoice: ' + xhr.responseJSON.message);
            }
        });
    });

    // Mark as Paid
    window.markAsPaid = function() {
        if (confirm('Mark this invoice as paid?')) {
            $.ajax({
                url: '{{ route("invoices.mark-paid", $invoice->id ?? 1) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error marking invoice as paid: ' + xhr.responseJSON.message);
                }
            });
        }
    };

    // Mark as Sent
    window.markAsSent = function() {
        if (confirm('Mark this invoice as sent?')) {
            $.ajax({
                url: '{{ route("invoices.mark-sent", $invoice->id ?? 1) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error marking invoice as sent: ' + xhr.responseJSON.message);
                }
            });
        }
    };

    // Delete Invoice
    window.deleteInvoice = function() {
        if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
            $.ajax({
                url: '{{ route("invoices.destroy", $invoice->id ?? 1) }}',
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    window.location.href = '{{ route("invoices.index") }}';
                },
                error: function(xhr) {
                    alert('Error deleting invoice: ' + xhr.responseJSON.message);
                }
            });
        }
    };

    // Duplicate Invoice
    window.duplicateInvoice = function() {
        if (confirm('Create a duplicate of this invoice?')) {
            window.location.href = '{{ route("invoices.create") }}?duplicate={{ $invoice->id ?? 1 }}';
        }
    };
});
</script>
@endpush
