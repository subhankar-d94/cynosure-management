<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number ?? 'INV-2024-12345' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .invoice-header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-logo {
            width: 150px;
            height: 60px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            border-radius: 8px;
        }

        .invoice-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }

        .invoice-meta {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .bill-to-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .items-table {
            margin-bottom: 30px;
        }

        .items-table th {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px;
            font-weight: 600;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .items-table tr:hover {
            background: #f8f9fa;
        }

        .summary-table {
            max-width: 400px;
            margin-left: auto;
        }

        .summary-table th, .summary-table td {
            border: none;
            padding: 8px 15px;
        }

        .total-row {
            background: #007bff;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-sent {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-draft {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-overdue {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .footer-section {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .payment-terms {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 5rem;
            color: rgba(220, 53, 69, 0.1);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 20px;
            }

            .container {
                max-width: none;
                margin: 0;
                padding: 0;
            }
        }

        @page {
            margin: 0.5in;
        }
    </style>
</head>
<body>
    <!-- Watermark for overdue invoices -->
    @if(($invoice->status ?? 'draft') === 'overdue' || (($invoice->due_date ?? '2024-02-15') < date('Y-m-d') && ($invoice->status ?? 'draft') !== 'paid'))
    <div class="watermark">OVERDUE</div>
    @endif

    <div class="container mt-4">
        <!-- Print Button -->
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Invoice
            </button>
            <button onclick="window.close()" class="btn btn-secondary ms-2">
                <i class="fas fa-times"></i> Close
            </button>
        </div>

        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="company-logo mb-3">
                        Your Company
                    </div>
                    <div class="company-details">
                        <strong>Cynosure Titli</strong><br>
                        123 Business Street<br>
                        City, State 12345<br>
                        Phone: (555) 123-4567<br>
                        Email: info@yourcompany.com<br>
                        Website: www.yourcompany.com
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <h1 class="invoice-title">INVOICE</h1>
                    <div class="invoice-meta mt-3">
                        <div class="row">
                            <div class="col-6">
                                <strong>Invoice #:</strong><br>
                                <span class="h5">{{ $invoice->invoice_number ?? 'INV-2024-12345' }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Status:</strong><br>
                                <span class="status-badge status-{{ $invoice->status ?? 'draft' }}">
                                    {{ ucfirst($invoice->status ?? 'draft') }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <strong>Issue Date:</strong><br>
                                {{ date('M d, Y', strtotime($invoice->issue_date ?? '2024-01-15')) }}
                            </div>
                            <div class="col-6">
                                <strong>Due Date:</strong><br>
                                <span class="{{ ($invoice->due_date ?? '2024-02-15') < date('Y-m-d') && ($invoice->status ?? 'draft') !== 'paid' ? 'text-danger fw-bold' : '' }}">
                                    {{ date('M d, Y', strtotime($invoice->due_date ?? '2024-02-15')) }}
                                </span>
                            </div>
                        </div>
                        @if($invoice->po_number ?? '')
                        <div class="row mt-2">
                            <div class="col-12">
                                <strong>PO Number:</strong> {{ $invoice->po_number }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bill To Section -->
        <div class="bill-to-section">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Bill To:</h5>
                    @if(isset($invoice->customer))
                    <div>
                        <strong>{{ $invoice->customer->name }}</strong><br>
                        @if($invoice->customer->company)
                        {{ $invoice->customer->company }}<br>
                        @endif
                        {{ $invoice->customer->email }}<br>
                        @if($invoice->customer->phone)
                        {{ $invoice->customer->phone }}<br>
                        @endif
                        @if($invoice->billing_address)
                        {{ $invoice->billing_address }}
                        @endif
                    </div>
                    @else
                    <div>
                        <strong>{{ $invoice->customer_name ?? 'Sample Customer' }}</strong><br>
                        @if($invoice->customer_company ?? '')
                        {{ $invoice->customer_company }}<br>
                        @endif
                        {{ $invoice->customer_email ?? 'customer@example.com' }}<br>
                        @if($invoice->customer_phone ?? '')
                        {{ $invoice->customer_phone }}<br>
                        @endif
                        @if($invoice->billing_address ?? '')
                        {{ $invoice->billing_address }}
                        @endif
                    </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Payment Terms:</h5>
                    <div>
                        <strong>Terms:</strong> {{ ucwords(str_replace('_', ' ', $invoice->payment_terms ?? 'net_30')) }}<br>
                        <strong>Due Date:</strong> {{ date('M d, Y', strtotime($invoice->due_date ?? '2024-02-15')) }}<br>
                        @if(($invoice->total ?? 1958.00) > ($invoice->paid_amount ?? 0))
                        <strong class="text-danger">Amount Due:</strong> ${{ number_format(($invoice->total ?? 1958.00) - ($invoice->paid_amount ?? 0), 2) }}
                        @else
                        <strong class="text-success">Paid in Full</strong>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="items-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center" width="10%">Qty</th>
                        <th class="text-end" width="15%">Rate</th>
                        <th class="text-center" width="10%">Tax</th>
                        <th class="text-end" width="15%">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($invoice->items) && $invoice->items->count() > 0)
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->description ?? 'Sample Service' }}</strong>
                                @if(isset($item->details) && $item->details)
                                <br><small class="text-muted">{{ $item->details }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                            <td class="text-end">${{ number_format($item->rate ?? 125.00, 2) }}</td>
                            <td class="text-center">{{ $item->tax_rate ?? 8 }}%</td>
                            <td class="text-end"><strong>${{ number_format($item->amount ?? 135.00, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <strong>Professional Services</strong>
                                <br><small class="text-muted">Consulting and development work</small>
                            </td>
                            <td class="text-center">10</td>
                            <td class="text-end">$125.00</td>
                            <td class="text-center">8%</td>
                            <td class="text-end"><strong>$1,350.00</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Software License</strong>
                                <br><small class="text-muted">Annual subscription</small>
                            </td>
                            <td class="text-center">1</td>
                            <td class="text-end">$500.00</td>
                            <td class="text-center">0%</td>
                            <td class="text-end"><strong>$500.00</strong></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Invoice Summary -->
        <div class="row">
            <div class="col-md-6">
                @if($invoice->notes ?? '')
                <div class="mb-4">
                    <h6 class="text-primary">Notes:</h6>
                    <p>{{ $invoice->notes }}</p>
                </div>
                @endif
            </div>
            <div class="col-md-6">
                <table class="summary-table table">
                    <tr>
                        <th class="text-end">Subtotal:</th>
                        <td class="text-end">${{ number_format($invoice->subtotal ?? 1850.00, 2) }}</td>
                    </tr>
                    @if(($invoice->discount ?? 0) > 0)
                    <tr>
                        <th class="text-end">Discount:</th>
                        <td class="text-end text-success">-${{ number_format($invoice->discount ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="text-end">Tax:</th>
                        <td class="text-end">${{ number_format($invoice->tax ?? 108.00, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <th class="text-end">TOTAL:</th>
                        <td class="text-end">${{ number_format($invoice->total ?? 1958.00, 2) }}</td>
                    </tr>
                    @if(($invoice->paid_amount ?? 0) > 0)
                    <tr>
                        <th class="text-end">Amount Paid:</th>
                        <td class="text-end text-success">${{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr class="table-warning">
                        <th class="text-end">Balance Due:</th>
                        <td class="text-end"><strong>${{ number_format(($invoice->total ?? 1958.00) - ($invoice->paid_amount ?? 0), 2) }}</strong></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Terms & Conditions:</h6>
                    <p class="small">
                        {{ $invoice->terms ?? 'Payment is due within 30 days of invoice date. Late payments may incur additional charges.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Payment Information:</h6>
                    <p class="small">
                        <strong>Bank:</strong> Your Bank Name<br>
                        <strong>Account:</strong> 1234567890<br>
                        <strong>Routing:</strong> 987654321<br>
                        <strong>Reference:</strong> {{ $invoice->invoice_number ?? 'INV-2024-12345' }}
                    </p>
                </div>
            </div>

            @if(($invoice->status ?? 'draft') !== 'paid' && ($invoice->due_date ?? '2024-02-15') >= date('Y-m-d'))
            <div class="payment-terms">
                <h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Payment Reminder</h6>
                <p class="mb-0">
                    This invoice is due on <strong>{{ date('M d, Y', strtotime($invoice->due_date ?? '2024-02-15')) }}</strong>.
                    Please ensure payment is made by the due date to avoid any late fees.
                </p>
            </div>
            @endif

            <div class="text-center mt-4">
                <hr>
                <p class="text-muted small">
                    Thank you for your business! If you have any questions about this invoice,
                    please contact us at (555) 123-4567 or info@yourcompany.com
                </p>
                <p class="text-muted small">
                    Invoice generated on {{ date('M d, Y \a\t g:i A') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS for potential interactive elements -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>
