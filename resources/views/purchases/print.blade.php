<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $purchase->purchase_order_number ?? 'PO-' . str_pad($purchase->id ?? 1, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 21cm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .company-details {
            color: #666;
            line-height: 1.6;
        }

        .po-header {
            text-align: right;
            flex: 1;
        }

        .po-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .po-number {
            font-size: 18px;
            color: #666;
            margin-bottom: 15px;
        }

        .po-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        .status-draft { background: #f8f9fa; color: #6c757d; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-ordered { background: #cce5ff; color: #004085; }
        .status-received { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .main-content {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }

        .supplier-info, .order-info {
            flex: 1;
        }

        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border: 1px solid #dee2e6;
        }

        .items-table th {
            background: #2c3e50;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: top;
        }

        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            max-width: 400px;
            margin-left: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .totals-row:last-child {
            border-bottom: none;
            background: #2c3e50;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .totals-label {
            font-weight: bold;
        }

        .notes-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #666;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 12px;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .container {
                margin: 0;
                padding: 15px;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
            }
        }

        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('purchases.show', $purchase->id ?? 1) }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="company-info">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Company Logo" class="company-logo">
                <div class="company-name">{{ config('app.name', 'Cynosure Titli Management') }}</div>
                <div class="company-details">
                    {{ config('company.address', '123 Business Street') }}<br>
                    {{ config('company.city', 'Mumbai') }}, {{ config('company.state', 'Maharashtra') }} {{ config('company.pincode', '400001') }}<br>
                    Phone: {{ config('company.phone', '+91 98765 43210') }}<br>
                    Email: {{ config('company.email', 'info@cynosure.com') }}<br>
                    GST: {{ config('company.gst', '27XXXXX1234X1ZX') }}
                </div>
            </div>

            <div class="po-header">
                <div class="po-title">PURCHASE ORDER</div>
                <div class="po-number">#{{ $purchase->purchase_order_number ?? 'PO-' . str_pad($purchase->id ?? 1, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="po-status status-{{ $purchase->status ?? 'draft' }}">
                    {{ ucfirst($purchase->status ?? 'Draft') }}
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="supplier-info">
                <div class="info-section">
                    <div class="section-title">Supplier Information</div>
                    <div class="info-row">
                        <span class="info-label">Company:</span>
                        <span class="info-value">{{ $purchase->supplier->company_name ?? 'ABC Suppliers Ltd.' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact:</span>
                        <span class="info-value">{{ $purchase->supplier->contact_person ?? 'John Doe' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $purchase->supplier->email ?? 'john@abcsuppliers.com' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $purchase->supplier->phone ?? '+91 98765 43210' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value">
                            {{ $purchase->supplier->address ?? '456 Supplier Street' }}<br>
                            {{ $purchase->supplier->city ?? 'Delhi' }}, {{ $purchase->supplier->state ?? 'Delhi' }} {{ $purchase->supplier->pincode ?? '110001' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">GST:</span>
                        <span class="info-value">{{ $purchase->supplier->gst_number ?? '07XXXXX5678X1ZY' }}</span>
                    </div>
                </div>
            </div>

            <div class="order-info">
                <div class="info-section">
                    <div class="section-title">Order Information</div>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ $purchase->created_at ? $purchase->created_at->format('d M, Y') : date('d M, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Expected:</span>
                        <span class="info-value">{{ $purchase->expected_delivery_date ? \Carbon\Carbon::parse($purchase->expected_delivery_date)->format('d M, Y') : date('d M, Y', strtotime('+7 days')) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Priority:</span>
                        <span class="info-value">{{ ucfirst($purchase->priority ?? 'medium') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment:</span>
                        <span class="info-value">{{ $purchase->payment_terms ?? 'Net 30' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Delivery:</span>
                        <span class="info-value">{{ $purchase->delivery_terms ?? 'Ex-Works' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Currency:</span>
                        <span class="info-value">{{ $purchase->currency ?? 'INR' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="35%">Item Description</th>
                    <th width="10%">Unit</th>
                    <th width="10%" class="text-center">Quantity</th>
                    <th width="15%" class="text-right">Unit Price</th>
                    <th width="10%" class="text-center">Tax%</th>
                    <th width="15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = $purchase->items ?? collect([
                        (object)[
                            'id' => 1,
                            'description' => 'Wooden Handcraft Item - Medium Size',
                            'sku' => 'WHI-MD-001',
                            'unit' => 'PCS',
                            'quantity' => 50,
                            'unit_price' => 250.00,
                            'tax_rate' => 18,
                            'total' => 14750.00
                        ],
                        (object)[
                            'id' => 2,
                            'description' => 'Metal Craft Decorative Piece',
                            'sku' => 'MCD-001',
                            'unit' => 'PCS',
                            'quantity' => 25,
                            'unit_price' => 450.00,
                            'tax_rate' => 18,
                            'total' => 13275.00
                        ],
                        (object)[
                            'id' => 3,
                            'description' => 'Textile Art Wall Hanging',
                            'sku' => 'TAW-001',
                            'unit' => 'PCS',
                            'quantity' => 15,
                            'unit_price' => 350.00,
                            'tax_rate' => 12,
                            'total' => 5880.00
                        ]
                    ]);
                    $subtotal = $items->sum('total') - $items->sum(fn($item) => ($item->total * $item->tax_rate / (100 + $item->tax_rate)));
                    $tax_amount = $items->sum(fn($item) => ($item->total * $item->tax_rate / (100 + $item->tax_rate)));
                    $total_amount = $items->sum('total');
                @endphp

                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->description }}</strong>
                        @if(isset($item->sku))
                            <br><small style="color: #666;">SKU: {{ $item->sku }}</small>
                        @endif
                    </td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-center">{{ number_format($item->quantity) }}</td>
                    <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">{{ $item->tax_rate }}%</td>
                    <td class="text-right">₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-row">
                <span class="totals-label">Subtotal:</span>
                <span>₹{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="totals-row">
                <span class="totals-label">Tax Amount:</span>
                <span>₹{{ number_format($tax_amount, 2) }}</span>
            </div>
            @if(isset($purchase->discount_amount) && $purchase->discount_amount > 0)
            <div class="totals-row">
                <span class="totals-label">Discount:</span>
                <span>-₹{{ number_format($purchase->discount_amount, 2) }}</span>
            </div>
            @endif
            @if(isset($purchase->shipping_cost) && $purchase->shipping_cost > 0)
            <div class="totals-row">
                <span class="totals-label">Shipping:</span>
                <span>₹{{ number_format($purchase->shipping_cost, 2) }}</span>
            </div>
            @endif
            <div class="totals-row">
                <span class="totals-label">Total Amount:</span>
                <span>₹{{ number_format($total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Notes Section -->
        @if(isset($purchase->notes) && $purchase->notes)
        <div class="notes-section">
            <div class="notes-title">Notes & Instructions:</div>
            <div>{{ $purchase->notes }}</div>
        </div>
        @endif

        <!-- Terms and Conditions -->
        <div class="notes-section">
            <div class="notes-title">Terms & Conditions:</div>
            <div>
                1. Please confirm receipt of this purchase order within 24 hours.<br>
                2. All items must be delivered as per the specifications mentioned.<br>
                3. Any deviation from the agreed terms must be communicated in advance.<br>
                4. Payment will be made as per the agreed payment terms.<br>
                5. Goods must be delivered to the address mentioned above.
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Authorized Signatory</div>
                <div style="margin-top: 5px; font-size: 11px;">{{ config('app.name', 'Cynosure Titli Management') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Supplier Acknowledgment</div>
                <div style="margin-top: 5px; font-size: 11px;">Date: _______________</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div style="text-align: center;">
                This is a system generated purchase order and requires no signature.<br>
                For any queries, please contact us at {{ config('company.email', 'info@cynosure.com') }} or {{ config('company.phone', '+91 98765 43210') }}
            </div>
        </div>
    </div>

    <script>
        // Auto print when opened with print parameter
        if (window.location.search.includes('print=true')) {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>