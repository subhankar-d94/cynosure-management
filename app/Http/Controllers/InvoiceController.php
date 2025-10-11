<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->getInvoicesData($request);
            }

            if ($request->has('stats')) {
                return response()->json(['stats' => $this->getInvoiceStats()]);
            }

            if ($request->has('export')) {
                return $this->exportInvoices($request);
            }

            // If request has pagination/filter parameters, return JSON data
            if ($request->has(['page', 'status', 'date_from', 'date_to', 'search', 'sort', 'direction'])) {
                return $this->getInvoicesData($request);
            }

            // Get statistics for dashboard cards
            $stats = $this->getInvoiceStats();

            return view('invoices.index', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error in invoices index: ' . $e->getMessage());
            return back()->with('error', 'Error loading invoices: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $duplicateInvoice = null;

            if ($request->has('duplicate')) {
                $duplicateInvoice = $this->getInvoiceWithRelations($request->duplicate);
            }

            return view('invoices.create', compact('duplicateInvoice'));
        } catch (\Exception $e) {
            Log::error('Error in invoices create: ' . $e->getMessage());
            return back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|string|unique:invoices',
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
                'customer_type' => 'required|in:existing,new',
                'customer_id' => 'nullable|exists:customers,id',
                'customer_name' => 'required_if:customer_type,new|string|max:255',
                'customer_email' => 'required_if:customer_type,new|email|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_company' => 'nullable|string|max:255',
                'billing_address' => 'nullable|string',
                'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt,custom',
                'po_number' => 'nullable|string|max:100',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string|max:500',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.rate' => 'required|numeric|min:0',
                'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
                'subtotal' => 'required|numeric|min:0',
                'discount_type' => 'nullable|in:fixed,percentage',
                'discount_value' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'status' => 'nullable|in:draft,sent',
                'notes' => 'nullable|string',
                'terms' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            // Create invoice
            $invoiceData = [
                'invoice_number' => $request->invoice_number,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'payment_terms' => $request->payment_terms,
                'po_number' => $request->po_number,
                'status' => $request->status ?? 'draft',
                'subtotal' => $request->subtotal,
                'discount_type' => $request->discount_type ?? 'fixed',
                'discount_value' => $request->discount_value ?? 0,
                'discount' => $request->discount ?? 0,
                'tax_amount' => $request->tax,
                'total' => $request->total,
                'paid_amount' => 0,
                'notes' => $request->notes,
                'terms' => $request->terms,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Handle customer data
            if ($request->customer_type === 'existing' && $request->customer_id) {
                // Get customer details and populate invoice fields
                $customer = DB::table('customers')->where('id', $request->customer_id)->first();
                if ($customer) {
                    $invoiceData['customer_id'] = $request->customer_id;
                    $invoiceData['customer_name'] = $customer->name;
                    $invoiceData['customer_email'] = $customer->email;
                    $invoiceData['customer_phone'] = $customer->phone;
                    $invoiceData['customer_company'] = $customer->company;
                    $invoiceData['customer_address'] = $customer->address;
                }
            } else {
                // For new customers, store customer info in invoice
                $invoiceData['customer_name'] = $request->customer_name;
                $invoiceData['customer_email'] = $request->customer_email;
                $invoiceData['customer_phone'] = $request->customer_phone;
                $invoiceData['customer_company'] = $request->customer_company;
                $invoiceData['customer_address'] = $request->billing_address;
            }

            $invoiceId = DB::table('invoices')->insertGetId($invoiceData);

            // Create invoice items
            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['rate'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $itemAmount = $itemSubtotal + $itemTax;

                DB::table('invoice_items')->insert([
                    'invoice_id' => $invoiceId,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'amount' => $itemAmount,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // If status is sent, update status
            if ($request->status === 'sent') {
                DB::table('invoices')->where('id', $invoiceId)->update([
                    'status' => 'sent',
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            $action = $request->input('action', 'save');
            if ($action === 'save_and_send') {
                // Mark as sent and potentially send email
                DB::table('invoices')->where('id', $invoiceId)->update([
                    'status' => 'sent',
                    'updated_at' => now()
                ]);

                return redirect()->route('invoices.show', $invoiceId)
                    ->with('success', 'Invoice created and marked as sent!');
            }

            return redirect()->route('invoices.show', $invoiceId)
                ->with('success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating invoice: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function show($id, Request $request)
    {
        try {
            $invoice = $this->getInvoiceWithRelations($id);

            if (!$invoice) {
                return redirect()->route('invoices.index')->with('error', 'Invoice not found.');
            }

            if ($request->get('format') === 'json') {
                return response()->json($invoice);
            }

            // Update view status if invoice was sent but not viewed
            if ($invoice->status === 'sent') {
                DB::table('invoices')->where('id', $id)->update([
                    'status' => 'viewed',
                    'updated_at' => now()
                ]);
                $invoice->status = 'viewed';
            }

            return view('invoices.show', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error showing invoice: ' . $e->getMessage());
            return back()->with('error', 'Error loading invoice: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $invoice = $this->getInvoiceWithRelations($id);

            if (!$invoice) {
                return redirect()->route('invoices.index')->with('error', 'Invoice not found.');
            }

            return view('invoices.edit', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error editing invoice: ' . $e->getMessage());
            return back()->with('error', 'Error loading invoice: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $id,
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
                'customer_type' => 'required|in:existing,custom',
                'customer_id' => 'nullable|exists:customers,id',
                'customer_name' => 'required_if:customer_type,custom|string|max:255',
                'customer_email' => 'required_if:customer_type,custom|email|max:255',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string|max:500',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.rate' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'status' => 'required|in:draft,sent,viewed,paid,overdue,cancelled'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            // Update invoice
            $invoiceData = [
                'invoice_number' => $request->invoice_number,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'payment_terms' => $request->payment_terms,
                'po_number' => $request->po_number,
                'status' => $request->status,
                'subtotal' => $request->subtotal,
                'discount_type' => $request->discount_type ?? 'fixed',
                'discount_value' => $request->discount_value ?? 0,
                'discount' => $request->discount ?? 0,
                'tax_amount' => $request->tax,
                'total' => $request->total,
                'notes' => $request->notes,
                'terms' => $request->terms,
                'updated_at' => now()
            ];

            // Handle customer data
            if ($request->customer_type === 'existing' && $request->customer_id) {
                $invoiceData['customer_id'] = $request->customer_id;
                $invoiceData['customer_name'] = null;
                $invoiceData['customer_email'] = null;
                $invoiceData['customer_phone'] = null;
                $invoiceData['customer_company'] = null;
                $invoiceData['billing_address'] = null;
            } else {
                $invoiceData['customer_id'] = null;
                $invoiceData['customer_name'] = $request->customer_name;
                $invoiceData['customer_email'] = $request->customer_email;
                $invoiceData['customer_phone'] = $request->customer_phone;
                $invoiceData['customer_company'] = $request->customer_company;
                $invoiceData['billing_address'] = $request->billing_address;
            }

            DB::table('invoices')->where('id', $id)->update($invoiceData);

            // Delete existing invoice items
            DB::table('invoice_items')->where('invoice_id', $id)->delete();

            // Create new invoice items
            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['rate'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $itemAmount = $itemSubtotal + $itemTax;

                DB::table('invoice_items')->insert([
                    'invoice_id' => $id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'amount' => $itemAmount,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            $action = $request->input('action', 'save');
            if ($action === 'save_and_send') {
                DB::table('invoices')->where('id', $id)->update([
                    'status' => 'sent',
                        'updated_at' => now()
                ]);
            }

            return redirect()->route('invoices.show', $id)
                ->with('success', 'Invoice updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating invoice: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Delete invoice items first
            DB::table('invoice_items')->where('invoice_id', $id)->delete();

            // Delete the invoice
            DB::table('invoices')->where('id', $id)->delete();

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting invoice: ' . $e->getMessage());
            return back()->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }

    public function preview($id)
    {
        try {
            $invoice = $this->getInvoiceWithRelations($id);

            if (!$invoice) {
                return redirect()->route('invoices.index')->with('error', 'Invoice not found.');
            }

            return view('invoices.preview', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error previewing invoice: ' . $e->getMessage());
            return back()->with('error', 'Error loading invoice preview: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $invoice = $this->getInvoiceWithRelations($id);

            if (!$invoice) {
                return redirect()->route('invoices.index')->with('error', 'Invoice not found.');
            }

            // Here you would generate PDF using a library like DomPDF or wkhtmltopdf
            // For now, we'll return the preview view for demonstration
            return view('invoices.preview', compact('invoice'));

        } catch (\Exception $e) {
            Log::error('Error downloading invoice: ' . $e->getMessage());
            return back()->with('error', 'Error downloading invoice: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        try {
            $invoice = $this->getInvoiceWithRelations($id);

            if (!$invoice) {
                return redirect()->route('invoices.index')->with('error', 'Invoice not found.');
            }

            return view('invoices.preview', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error printing invoice: ' . $e->getMessage());
            return back()->with('error', 'Error loading invoice for printing: ' . $e->getMessage());
        }
    }

    public function send(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'attach_pdf' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            $invoice = $this->getInvoiceWithRelations($id);

            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            // Here you would send the actual email
            // For demonstration, we'll just update the status

            DB::table('invoices')->where('id', $id)->update([
                'status' => 'sent',
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Invoice sent successfully']);

        } catch (\Exception $e) {
            Log::error('Error sending invoice: ' . $e->getMessage());
            return response()->json(['error' => 'Error sending invoice'], 500);
        }
    }

    public function markPaid($id)
    {
        try {
            $invoice = DB::table('invoices')->where('id', $id)->first();

            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            DB::table('invoices')->where('id', $id)->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_amount' => $invoice->total,
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Invoice marked as paid']);

        } catch (\Exception $e) {
            Log::error('Error marking invoice as paid: ' . $e->getMessage());
            return response()->json(['error' => 'Error marking invoice as paid'], 500);
        }
    }

    public function markSent($id)
    {
        try {
            DB::table('invoices')->where('id', $id)->update([
                'status' => 'sent',
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Invoice marked as sent']);

        } catch (\Exception $e) {
            Log::error('Error marking invoice as sent: ' . $e->getMessage());
            return response()->json(['error' => 'Error marking invoice as sent'], 500);
        }
    }

    public function bulkSend(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:send,mark_paid,download,delete',
                'invoice_ids' => 'required|array|min:1',
                'invoice_ids.*' => 'integer|exists:invoices,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            $invoiceIds = $request->invoice_ids;
            $action = $request->action;

            switch ($action) {
                case 'send':
                    DB::table('invoices')->whereIn('id', $invoiceIds)->update([
                        'status' => 'sent',
                                'updated_at' => now()
                    ]);
                    $message = count($invoiceIds) . ' invoices sent successfully';
                    break;

                case 'mark_paid':
                    $invoices = DB::table('invoices')->whereIn('id', $invoiceIds)->get();
                    foreach ($invoices as $invoice) {
                        DB::table('invoices')->where('id', $invoice->id)->update([
                            'status' => 'paid',
                            'payment_status' => 'paid',
                            'paid_amount' => $invoice->total,
                            'updated_at' => now()
                        ]);
                    }
                    $message = count($invoiceIds) . ' invoices marked as paid successfully';
                    break;

                case 'delete':
                    DB::beginTransaction();
                    DB::table('invoice_items')->whereIn('invoice_id', $invoiceIds)->delete();
                    DB::table('invoices')->whereIn('id', $invoiceIds)->delete();
                    DB::commit();
                    $message = count($invoiceIds) . ' invoices deleted successfully';
                    break;

                case 'download':
                    // Return download URLs or initiate download process
                    $message = 'Download initiated for ' . count($invoiceIds) . ' invoices';
                    break;
            }

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            if (isset($action) && $action === 'delete') {
                DB::rollBack();
            }
            Log::error('Error in bulk action: ' . $e->getMessage());
            return response()->json(['error' => 'Error performing bulk action'], 500);
        }
    }

    public function getInvoicesData(Request $request)
    {
        try {
            $query = DB::table('invoices')
                ->leftJoin('customers', 'invoices.customer_id', '=', 'customers.id')
                ->select([
                    'invoices.id',
                    'invoices.invoice_number',
                    'invoices.issue_date',
                    'invoices.due_date',
                    'invoices.status',
                    'invoices.total',
                    'invoices.paid_amount',
                    'invoices.created_at',
                    'customers.name as customer_name',
                    'customers.email as customer_email',
                    'invoices.customer_name as custom_name',
                    'invoices.customer_email as custom_email'
                ]);

            // Apply filters
            if ($request->has('status') &&  !empty($request->status)) {
                $query->where('invoices.status', $request->status);
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->where('invoices.issue_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->where('invoices.issue_date', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('invoices.invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('customers.name', 'LIKE', "%{$search}%")
                      ->orWhere('invoices.customer_name', 'LIKE', "%{$search}%")
                      ->orWhere('customers.email', 'LIKE', "%{$search}%")
                      ->orWhere('invoices.customer_email', 'LIKE', "%{$search}%");
                });
            }

            // Sorting
            $sortField = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');

            $allowedSorts = ['invoice_number', 'issue_date', 'due_date', 'status', 'total', 'created_at'];
            if (in_array($sortField, $allowedSorts)) {
                $query->orderBy("invoices.{$sortField}", $sortDirection);
            }

            // Get total count before applying pagination
            $total = $query->count();

            // Pagination
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;

            $invoices = $query->offset($offset)->limit($perPage)->get();

            // Add computed fields and handle overdue status
            $today = date('Y-m-d');

            $invoices = $invoices->map(function($invoice) use ($today) {
                // Use customer name from join or custom name
                $invoice->customer_name = $invoice->customer_name ?: $invoice->custom_name;
                $invoice->customer_email = $invoice->customer_email ?: $invoice->custom_email;

                // Check if overdue and update status
                if ($invoice->due_date && $invoice->due_date < $today && !in_array($invoice->status, ['paid', 'cancelled'])) {
                    $invoice->status = 'overdue';
                }

                // Ensure numeric values are properly formatted
                $invoice->total = (float) ($invoice->total ?? 0);
                $invoice->paid_amount = (float) ($invoice->paid_amount ?? 0);

                return $invoice;
            });

            return response()->json([
                'invoices' => $invoices,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting invoices data: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading invoices'], 500);
        }
    }

    public function getInvoiceStats()
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();

            return [
                'total_invoices' => DB::table('invoices')->count(),
                'pending_invoices' => DB::table('invoices')->whereIn('status', ['draft', 'sent', 'viewed'])->count(),
                'paid_invoices' => DB::table('invoices')->where('status', 'paid')->count(),
                'monthly_revenue' => DB::table('invoices')
                    ->where('created_at', '>=', $thisMonth)
                    ->where('status', 'paid')
                    ->sum('total'),
                'overdue_invoices' => DB::table('invoices')
                    ->where('due_date', '<', $today)
                    ->whereNotIn('status', ['paid', 'cancelled'])
                    ->count(),
                'draft_invoices' => DB::table('invoices')->where('status', 'draft')->count(),
                'outstanding_amount' => DB::table('invoices')
                    ->whereNotIn('status', ['paid', 'cancelled'])
                    ->sum(DB::raw('total - paid_amount')),
                'monthly_invoices' => DB::table('invoices')
                    ->where('created_at', '>=', $thisMonth)
                    ->count()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting invoice stats: ' . $e->getMessage());
            return [
                'total_invoices' => 0,
                'pending_invoices' => 0,
                'paid_invoices' => 0,
                'monthly_revenue' => 0,
                'overdue_invoices' => 0,
                'draft_invoices' => 0,
                'outstanding_amount' => 0,
                'monthly_invoices' => 0
            ];
        }
    }

    private function getInvoiceWithRelations($id)
    {
        try {
            $invoice = DB::table('invoices')
                ->leftJoin('customers', 'invoices.customer_id', '=', 'customers.id')
                ->select([
                    'invoices.*',
                    'customers.name as customer_name',
                    'customers.email as customer_email',
                    'customers.phone as customer_phone',
                    'customers.company_name as customer_company'
                ])
                ->where('invoices.id', $id)
                ->first();

            if ($invoice) {
                $items = DB::table('invoice_items')
                    ->where('invoice_id', $id)
                    ->get();

                $invoice->items = $items;

                // Check if overdue
                if ($invoice->due_date < date('Y-m-d') && !in_array($invoice->status, ['paid', 'cancelled'])) {
                    $invoice->status = 'overdue';
                }
            }

            return $invoice;

        } catch (\Exception $e) {
            Log::error('Error getting invoice with relations: ' . $e->getMessage());
            return null;
        }
    }

    private function exportInvoices(Request $request)
    {
        try {
            $query = DB::table('invoices')
                ->leftJoin('customers', 'invoices.customer_id', '=', 'customers.id')
                ->select([
                    'invoices.invoice_number',
                    'invoices.issue_date',
                    'invoices.due_date',
                    'invoices.status',
                    'invoices.total',
                    'invoices.paid_amount',
                    'customers.name as customer_name',
                    'invoices.customer_name as custom_name'
                ]);

            // Apply same filters as index
            if ($request->has('status') && $request->status !== '') {
                $query->where('invoices.status', $request->status);
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->where('invoices.issue_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->where('invoices.issue_date', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('invoices.invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('customers.name', 'LIKE', "%{$search}%")
                      ->orWhere('invoices.customer_name', 'LIKE', "%{$search}%");
                });
            }

            $invoices = $query->get();

            $csvData = "Invoice Number,Issue Date,Due Date,Customer,Status,Total,Paid Amount,Balance Due\n";

            foreach ($invoices as $invoice) {
                $customerName = $invoice->customer_name ?: $invoice->custom_name ?: 'N/A';
                $balanceDue = $invoice->total - $invoice->paid_amount;

                $csvData .= '"' . $invoice->invoice_number . '","' . $invoice->issue_date . '","' .
                           $invoice->due_date . '","' . $customerName . '","' . $invoice->status . '","' .
                           number_format($invoice->total, 2) . '","' . number_format($invoice->paid_amount, 2) . '","' .
                           number_format($balanceDue, 2) . '"' . "\n";
            }

            $filename = 'invoices_export_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('Error exporting invoices: ' . $e->getMessage());
            return response()->json(['error' => 'Error exporting invoices'], 500);
        }
    }
}
