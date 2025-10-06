<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index()
    {
        return view('customers.index');
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'customer_type' => 'required|in:individual,business',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'customer_code' => 'nullable|string|unique:customers,customer_code',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:15',
            'notes' => 'nullable|string',
            'status' => 'in:active,inactive',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'preferred_contact_method' => 'in:email,phone,sms',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $customerData = $request->only([
                'name', 'customer_type', 'email', 'phone', 'company_name',
                'gst_number', 'notes', 'status', 'credit_limit',
                'payment_terms', 'discount_percentage', 'preferred_contact_method',
                'customer_code'
            ]);

            // Generate customer code if not provided
            if (empty($customerData['customer_code'])) {
                $customerData['customer_code'] = $this->generateCustomerCode();
            }

            // Set defaults for nullable fields
            $customerData['status'] = $customerData['status'] ?? 'active';
            $customerData['credit_limit'] = $customerData['credit_limit'] ?? 0;
            $customerData['payment_terms'] = $customerData['payment_terms'] ?? 30;
            $customerData['discount_percentage'] = $customerData['discount_percentage'] ?? 0;

            // Handle boolean fields
            $customerData['email_notifications'] = $request->boolean('email_notifications');
            $customerData['sms_notifications'] = $request->boolean('sms_notifications');
            $customerData['marketing_emails'] = $request->boolean('marketing_emails');

            $customerData['created_at'] = now();
            $customerData['updated_at'] = now();

            // Insert customer
            $customerId = DB::table('customers')->insertGetId($customerData);

            // Handle addresses
            if ($request->has('addresses')) {
                $this->storeAddresses($customerId, $request->addresses);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => ['id' => $customerId],
                'redirect' => route('customers.show', $customerId)
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error creating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified customer
     */
    public function show($id)
    {
        try {
            $customer = DB::table('customers')->where('id', $id)->first();

            if (!$customer) {
                return redirect()->route('customers.index')->with('error', 'Customer not found');
            }

            // Get customer statistics
            $stats = $this->getCustomerStats($id);
            $customer->stats = $stats;

            return view('customers.show', compact('customer'));

        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', 'Error loading customer');
        }
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit($id)
    {
        try {
            $customer = DB::table('customers')->where('id', $id)->first();

            if (!$customer) {
                return redirect()->route('customers.index')->with('error', 'Customer not found');
            }

            return view('customers.edit', compact('customer'));

        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', 'Error loading customer');
        }
    }

    /**
     * Update the specified customer in storage
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'customer_type' => 'required|in:individual,business',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'customer_code' => 'nullable|string|unique:customers,customer_code,' . $id,
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:15',
            'notes' => 'nullable|string',
            'status' => 'in:active,inactive',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'preferred_contact_method' => 'in:email,phone,sms',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $customerData = $request->only([
                'name', 'customer_type', 'email', 'phone', 'customer_code',
                'company_name', 'gst_number', 'notes', 'status',
                'credit_limit', 'payment_terms', 'discount_percentage',
                'preferred_contact_method'
            ]);

            $customerData['email_notifications'] = $request->boolean('email_notifications');
            $customerData['sms_notifications'] = $request->boolean('sms_notifications');
            $customerData['marketing_emails'] = $request->boolean('marketing_emails');
            $customerData['updated_at'] = now();

            DB::table('customers')->where('id', $id)->update($customerData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'redirect' => route('customers.show', $id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error updating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customer from storage
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Check if customer has orders
            $orderCount = DB::table('orders')->where('customer_id', $id)->count();

            if ($orderCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete customer with existing orders'
                ], 400);
            }

            // Delete addresses first
            DB::table('customer_addresses')->where('customer_id', $id)->delete();

            // Delete customer
            DB::table('customers')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error deleting customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer orders
     */
    public function orders(Request $request, $id)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $status = $request->get('status');
            $dateRange = $request->get('date_range');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $query = DB::table('orders')
                ->where('customer_id', $id)
                ->select([
                    'orders.*',
                    DB::raw('(SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as items_count')
                ]);

            // Apply filters
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($dateRange) {
                $this->applyDateRangeFilter($query, $dateRange, $request);
            }

            $query->orderBy($sortBy, $sortOrder);

            $total = $query->count();
            $orders = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

            // Get statistics if requested
            $stats = null;
            if ($request->get('stats')) {
                $stats = $this->getOrderStats($id);
            }

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'data' => $orders,
                        'total' => $total,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'from' => ($page - 1) * $perPage + 1,
                        'to' => min($page * $perPage, $total)
                    ],
                    'stats' => $stats
                ]);
            }

            // Get customer info for the view
            $customer = DB::table('customers')->where('id', $id)->first();

            return view('customers.orders', compact('customer'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading orders: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('customers.index')->with('error', 'Error loading orders');
        }
    }

    /**
     * Get customer addresses
     */
    public function getAddresses($id)
    {
        try {
            $addresses = DB::table('customer_addresses')
                ->where('customer_id', $id)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $addresses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading addresses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new address for customer
     */
    public function storeAddress(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:billing,shipping,both',
            'label' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $addressData = $request->only([
                'type', 'label', 'street_address', 'city',
                'state', 'postal_code', 'country'
            ]);

            $addressData['customer_id'] = $id;
            $addressData['is_default'] = $request->boolean('is_default');
            $addressData['created_at'] = now();
            $addressData['updated_at'] = now();

            // If this is set as default, unset other defaults
            if ($addressData['is_default']) {
                DB::table('customer_addresses')
                    ->where('customer_id', $id)
                    ->update(['is_default' => false]);
            }

            $addressId = DB::table('customer_addresses')->insertGetId($addressData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Address added successfully',
                'data' => ['id' => $addressId]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error adding address: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update customer address
     */
    public function updateAddress(Request $request, $customerId, $addressId)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:billing,shipping,both',
            'label' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $addressData = $request->only([
                'type', 'label', 'street_address', 'city',
                'state', 'postal_code', 'country'
            ]);

            $addressData['is_default'] = $request->boolean('is_default');
            $addressData['updated_at'] = now();

            // If this is set as default, unset other defaults
            if ($addressData['is_default']) {
                DB::table('customer_addresses')
                    ->where('customer_id', $customerId)
                    ->where('id', '!=', $addressId)
                    ->update(['is_default' => false]);
            }

            DB::table('customer_addresses')
                ->where('id', $addressId)
                ->where('customer_id', $customerId)
                ->update($addressData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error updating address: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete customer address
     */
    public function deleteAddress($customerId, $addressId)
    {
        try {
            DB::table('customer_addresses')
                ->where('id', $addressId)
                ->where('customer_id', $customerId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting address: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import customers from CSV
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:2048',
            'update_existing' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $updateExisting = $request->boolean('update_existing');

            $imported = 0;
            $updated = 0;
            $errors = [];

            // Process CSV file
            if (($handle = fopen($file->path(), 'r')) !== false) {
                $header = fgetcsv($handle);

                while (($data = fgetcsv($handle)) !== false) {
                    try {
                        $customerData = array_combine($header, $data);

                        // Generate customer code if not provided
                        if (empty($customerData['customer_code'])) {
                            $customerData['customer_code'] = $this->generateCustomerCode();
                        }

                        $customerData['created_at'] = now();
                        $customerData['updated_at'] = now();

                        // Check if customer exists
                        $existing = DB::table('customers')
                            ->where('email', $customerData['email'])
                            ->orWhere('customer_code', $customerData['customer_code'])
                            ->first();

                        if ($existing && $updateExisting) {
                            DB::table('customers')
                                ->where('id', $existing->id)
                                ->update($customerData);
                            $updated++;
                        } elseif (!$existing) {
                            DB::table('customers')->insert($customerData);
                            $imported++;
                        }

                    } catch (\Exception $e) {
                        $errors[] = "Row error: " . $e->getMessage();
                    }
                }

                fclose($handle);
            }

            return response()->json([
                'success' => true,
                'message' => "Import completed. {$imported} imported, {$updated} updated.",
                'data' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export customers to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = DB::table('customers');

            // Apply filters
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('customer_code', 'like', "%{$search}%");
                });
            }

            if ($request->get('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->get('customer_type')) {
                $query->where('customer_type', $request->get('customer_type'));
            }

            $customers = $query->orderBy('name')->get();

            $filename = 'customers_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($customers) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'ID', 'Customer Code', 'Name', 'Email', 'Phone',
                    'Customer Type', 'Company Name', 'Status',
                    'Credit Limit', 'Payment Terms', 'Created At'
                ]);

                foreach ($customers as $customer) {
                    fputcsv($file, [
                        $customer->id,
                        $customer->customer_code,
                        $customer->name,
                        $customer->email,
                        $customer->phone,
                        $customer->customer_type,
                        $customer->company_name,
                        $customer->status,
                        $customer->credit_limit,
                        $customer->payment_terms,
                        $customer->created_at
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Get customers data for DataTables
     */
    public function getData(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $status = $request->get('status');
            $customerType = $request->get('customer_type');
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');

            $query = DB::table('customers')
                ->select([
                    'customers.*',
                    DB::raw('(SELECT COUNT(*) FROM orders WHERE orders.customer_id = customers.id) as total_orders'),
                    DB::raw('(SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE orders.customer_id = customers.id) as total_spent'),
                    DB::raw('(SELECT MAX(created_at) FROM orders WHERE orders.customer_id = customers.id) as last_order_date')
                ]);

            // Apply filters
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('customer_code', 'like', "%{$search}%");
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($customerType) {
                $query->where('customer_type', $customerType);
            }

            $query->orderBy($sortBy, $sortOrder);

            // Get stats if requested
            $stats = null;
            if ($request->get('stats')) {
                $stats = [
                    'total_customers' => DB::table('customers')->count(),
                    'new_customers' => DB::table('customers')->where('created_at', '>=', now()->startOfMonth())->count(),
                    'active_orders' => DB::table('orders')->whereIn('status', ['pending', 'confirmed', 'processing'])->count(),
                    'total_revenue' => DB::table('orders')->sum('total_amount') ?? 0
                ];
            }

            $total = $query->count();
            $customers = $query->skip(($page - 1) * $perPage)
                             ->take($perPage)
                             ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $customers,
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total)
                ],
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique customer code
     */
    private function generateCustomerCode()
    {
        do {
            $code = 'CUST' . now()->format('y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (DB::table('customers')->where('customer_code', $code)->exists());

        return $code;
    }

    /**
     * Store customer addresses
     */
    private function storeAddresses($customerId, $addresses)
    {
        foreach ($addresses as $index => $address) {
            if (!empty($address['street_address'])) {
                $addressData = [
                    'customer_id' => $customerId,
                    'type' => $address['type'] ?? 'billing',
                    'label' => $address['label'] ?? null,
                    'address_line_1' => $address['street_address'],
                    'city' => $address['city'] ?? null,
                    'state' => $address['state'] ?? null,
                    'postal_code' => $address['postal_code'] ?? null,
                    'country' => $address['country'] ?? 'India',
                    'is_default' => isset($address['is_default']) ? true : false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                DB::table('customer_addresses')->insert($addressData);
            }
        }
    }

    /**
     * Get customer statistics
     */
    private function getCustomerStats($customerId)
    {
        $stats = DB::table('orders')
            ->where('customer_id', $customerId)
            ->selectRaw('
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as total_spent,
                COALESCE(AVG(total_amount), 0) as average_order,
                MAX(created_at) as last_order_date
            ')
            ->first();

        // Calculate days since last order
        $lastOrderDays = null;
        if ($stats->last_order_date) {
            $lastOrderDays = Carbon::parse($stats->last_order_date)->diffInDays(now());
        }

        return [
            'total_orders' => $stats->total_orders ?? 0,
            'total_spent' => $stats->total_spent ?? 0,
            'average_order' => $stats->average_order ?? 0,
            'last_order_days' => $lastOrderDays
        ];
    }

    /**
     * Get order statistics for customer
     */
    private function getOrderStats($customerId)
    {
        return [
            'total_orders' => DB::table('orders')->where('customer_id', $customerId)->count(),
            'total_value' => DB::table('orders')->where('customer_id', $customerId)->sum('total_amount') ?? 0,
            'pending_orders' => DB::table('orders')->where('customer_id', $customerId)->whereIn('status', ['pending', 'confirmed'])->count(),
            'average_order' => DB::table('orders')->where('customer_id', $customerId)->avg('total_amount') ?? 0
        ];
    }

    /**
     * Apply date range filter to query
     */
    private function applyDateRangeFilter($query, $dateRange, $request)
    {
        switch ($dateRange) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'quarter':
                $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                break;
            case 'year':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
            case 'custom':
                if ($request->get('start_date') && $request->get('end_date')) {
                    $query->whereBetween('created_at', [
                        $request->get('start_date'),
                        $request->get('end_date')
                    ]);
                }
                break;
        }
    }
}