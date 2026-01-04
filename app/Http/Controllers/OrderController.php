<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->getOrdersData($request);
            }

            // Get statistics for dashboard cards
            $stats = $this->getOrderStats();

            return view('orders.index', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error in orders index: ' . $e->getMessage());
            return back()->with('error', 'Error loading orders: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('orders.create');
        } catch (\Exception $e) {
            Log::error('Error in orders create: ' . $e->getMessage());
            return back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            // Remove customer name/phone fields if customer_type is 'existing'
            $data = $request->all();
            if (isset($data['customer_type']) && $data['customer_type'] === 'existing') {
                unset($data['customer_name'], $data['customer_phone']);
            }

            $validator = Validator::make($data, [
                'order_date' => 'required|date',
                'customer_type' => 'required|in:existing,new',
                'customer_id' => 'required_if:customer_type,existing|exists:customers,id',
                'customer_name' => 'required_if:customer_type,new|string|max:255',
                'customer_phone' => 'required_if:customer_type,new|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'customer_address' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.discount' => 'nullable|numeric|min:0',
                'subtotal' => 'nullable|numeric|min:0',
                'total' => 'nullable|numeric|min:0',
                'delivery_charges' => 'nullable|numeric|min:0',
                'expected_delivery' => 'nullable|date|after_or_equal:order_date',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            // Handle customer creation for new customers
            $customerId = null;
            if ($request->customer_type === 'existing' && $request->customer_id) {
                $customerId = $request->customer_id;
            } else {
                // Create new customer record
                $customerData = [
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'email' => $request->customer_email,
                    'customer_type' => 'individual',  // Always individual for new customers
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $customerId = DB::table('customers')->insertGetId($customerData);
            }

            // Generate unique order number
            $orderNumber = Order::generateOrderNumber();

            // Create order - customer_id is already set above
            $orderData = [
                'order_number' => $orderNumber,
                'customer_id' => $customerId,  // This is set whether existing or newly created
                'order_date' => $request->order_date,
                'status' => Order::STATUS_PENDING,
                'priority' => $request->priority ?? 'medium',
                'subtotal' => $request->subtotal ?? 0,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'total_amount' => $request->total ?? $request->subtotal ?? 0,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'expected_delivery' => $request->expected_delivery,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status ?? Order::PAYMENT_STATUS_PENDING,
                'paid_amount' => $request->paid_amount ?? 0,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $orderId = DB::table('orders')->insertGetId($orderData);

            // Create order items
            foreach ($request->items as $item) {
                $itemTotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'] ?? 'Product',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'customization_details' => $item['customization_details'] ?? null,
                    'subtotal' => $itemTotal,
                    'total' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Note: Stock management handled separately in inventory table
                // Update inventory instead of products table
                DB::table('inventories')->where('product_id', $item['product_id'])->decrement('quantity_in_stock', $item['quantity']);
            }

            DB::commit();

            $action = $request->input('action', 'save');
            if ($action === 'save_and_process') {
                // Update status to in_progress
                DB::table('orders')->where('id', $orderId)->update([
                    'status' => Order::STATUS_IN_PROGRESS,
                    'updated_at' => now()
                ]);

                return redirect()->route('orders.show', $orderId)
                    ->with('success', "Order {$orderNumber} created and moved to in-progress!");
            }

            return redirect()->route('orders.show', $orderId)
                ->with('success', "Order {$orderNumber} created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $order = $this->getOrderWithRelations($id);

            if (!$order) {
                return redirect()->route('orders.index')->with('error', 'Order not found.');
            }

            return view('orders.show', compact('order'));
        } catch (\Exception $e) {
            Log::error('Error showing order: ' . $e->getMessage());
            return back()->with('error', 'Error loading order: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $order = $this->getOrderWithRelations($id);

            if (!$order) {
                return redirect()->route('orders.index')->with('error', 'Order not found.');
            }

            return view('orders.edit', compact('order'));
        } catch (\Exception $e) {
            Log::error('Error editing order: ' . $e->getMessage());
            return back()->with('error', 'Error loading order: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_date' => 'required|date',
                'customer_type' => 'required|in:existing',  // Only existing allowed when editing
                'customer_id' => 'required|exists:customers,id',  // Customer ID required when editing
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'status' => 'required|in:' . Order::getStatusesForValidation(),
                'payment_status' => 'required|in:pending,partial,paid,failed'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            // Get current order to restore stock
            $currentOrder = DB::table('orders')->where('id', $id)->first();
            $currentItems = DB::table('order_items')->where('order_id', $id)->get();

            // Restore stock for current items
            foreach ($currentItems as $item) {
                DB::table('inventories')
                    ->where('product_id', $item->product_id)
                    ->increment('quantity_in_stock', $item->quantity);
            }

            // Update order
            $orderData = [
                'customer_id' => $request->customer_id,  // Always link to existing customer when editing
                'order_date' => $request->order_date,
                'expected_delivery' => $request->expected_delivery,
                'priority' => $request->priority ?? 'medium',
                'status' => $request->status,
                'subtotal' => $request->subtotal ?? 0,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'total_amount' => $request->total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'paid_amount' => $request->paid_amount ?? 0,
                'notes' => $request->notes,
                'updated_at' => now()
            ];

            DB::table('orders')->where('id', $id)->update($orderData);

            // Delete existing order items
            DB::table('order_items')->where('order_id', $id)->delete();

            // Create new order items
            foreach ($request->items as $item) {
                $itemTotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);

                DB::table('order_items')->insert([
                    'order_id' => $id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'] ?? 'Product',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'customization_details' => $item['customization_details'] ?? null,
                    'subtotal' => $itemTotal,
                    'total' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update inventory stock
                DB::table('inventories')
                    ->where('product_id', $item['product_id'])
                    ->decrement('quantity_in_stock', $item['quantity']);
            }

            DB::commit();

            $action = $request->input('action', 'save');
            if ($action === 'save_and_complete') {
                DB::table('orders')->where('id', $id)->update([
                    'status' => 'completed',
                    'updated_at' => now()
                ]);
            }

            return redirect()->route('orders.show', $id)
                ->with('success', 'Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating order: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Get order items to restore stock
            $orderItems = DB::table('order_items')->where('order_id', $id)->get();

            foreach ($orderItems as $item) {
                DB::table('inventories')
                    ->where('product_id', $item->product_id)
                    ->increment('quantity_in_stock', $item->quantity);
            }

            // Delete order items first
            DB::table('order_items')->where('order_id', $id)->delete();

            // Delete the order
            DB::table('orders')->where('id', $id)->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting order: ' . $e->getMessage());
            return back()->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:' . Order::getStatusesForValidation(),
                'note' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            DB::table('orders')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

            // Log status change if note provided
            if ($request->note) {
                DB::table('order_logs')->insert([
                    'order_id' => $id,
                    'action' => 'status_changed',
                    'details' => "Status changed to {$request->status}. Note: {$request->note}",
                    'created_at' => now()
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);

        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating status'], 500);
        }
    }

    public function recordPayment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:cash,card,bank_transfer,check',
                'note' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            $order = DB::table('orders')->where('id', $id)->first();
            $newPaidAmount = $order->paid_amount + $request->amount;

            $paymentStatus = 'partial';
            if ($newPaidAmount >= $order->total) {
                $paymentStatus = 'paid';
            }

            DB::table('orders')->where('id', $id)->update([
                'paid_amount' => $newPaidAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $request->payment_method,
                'updated_at' => now()
            ]);

            // Record payment transaction
            DB::table('order_payments')->insert([
                'order_id' => $id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
                'created_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Payment recorded successfully']);

        } catch (\Exception $e) {
            Log::error('Error recording payment: ' . $e->getMessage());
            return response()->json(['error' => 'Error recording payment'], 500);
        }
    }

    public function duplicate($id)
    {
        try {
            $order = $this->getOrderWithRelations($id);

            if (!$order) {
                return redirect()->route('orders.index')->with('error', 'Order not found.');
            }

            // Generate new order number
            $newOrderNumber = 'ORD-' . date('Y') . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);

            return view('orders.create', compact('order', 'newOrderNumber'));

        } catch (\Exception $e) {
            Log::error('Error duplicating order: ' . $e->getMessage());
            return back()->with('error', 'Error duplicating order: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            // Get order items to restore stock
            $orderItems = DB::table('order_items')->where('order_id', $id)->get();

            foreach ($orderItems as $item) {
                DB::table('inventories')
                    ->where('product_id', $item->product_id)
                    ->increment('quantity_in_stock', $item->quantity);
            }

            DB::table('orders')->where('id', $id)->update([
                'status' => 'cancelled',
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Order cancelled successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json(['error' => 'Error cancelling order'], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:delete,update_status,export',
                'order_ids' => 'required|array|min:1',
                'order_ids.*' => 'integer|exists:orders,id',
                'status' => 'required_if:action,update_status|in:' . Order::getStatusesForValidation()
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            $orderIds = $request->order_ids;
            $action = $request->action;

            switch ($action) {
                case 'delete':
                    DB::beginTransaction();

                    // Restore stock for all items
                    $orderItems = DB::table('order_items')->whereIn('order_id', $orderIds)->get();
                    foreach ($orderItems as $item) {
                        DB::table('inventories')
                            ->where('product_id', $item->product_id)
                            ->increment('quantity_in_stock', $item->quantity);
                    }

                    DB::table('order_items')->whereIn('order_id', $orderIds)->delete();
                    DB::table('orders')->whereIn('id', $orderIds)->delete();

                    DB::commit();
                    $message = count($orderIds) . ' orders deleted successfully';
                    break;

                case 'update_status':
                    DB::table('orders')->whereIn('id', $orderIds)->update([
                        'status' => $request->status,
                        'updated_at' => now()
                    ]);
                    $message = count($orderIds) . ' orders status updated successfully';
                    break;

                case 'export':
                    return $this->exportOrders($orderIds);
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

    public function getOrdersData(Request $request)
    {
        try {
            $query = DB::table('orders')
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->select([
                    'orders.id',
                    'orders.order_number',
                    'orders.order_date',
                    'orders.status',
                    'orders.payment_status',
                    'orders.payment_method',
                    'orders.paid_amount',
                    'orders.total_amount',
                    'orders.created_at',
                    'customers.name as customer_name',
                    'customers.phone as customer_phone',
                    'customers.email as customer_email'
                ]);

            // Apply filters
            if ($request->has('status') && $request->status !== '' && $request->status !== null) {
                $query->where('orders.status', $request->status);
            }

            if ($request->has('payment_status') && $request->payment_status !== '' && $request->payment_status !== null) {
                $query->where('orders.payment_status', $request->payment_status);
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->where('orders.order_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->where('orders.order_date', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search && $request->search !== 'null') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('orders.order_number', 'LIKE', "%{$search}%")
                      ->orWhere('orders.id', 'LIKE', "%{$search}%")
                      ->orWhere('customers.name', 'LIKE', "%{$search}%")
                      ->orWhere('customers.phone', 'LIKE', "%{$search}%");
                });
            }

            // Sorting
            $sortField = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');

            $allowedSorts = ['id', 'order_date', 'status', 'total_amount', 'created_at'];
            if (in_array($sortField, $allowedSorts)) {
                if ($sortField === 'total') {
                    $query->orderBy("orders.total_amount", $sortDirection);
                } else {
                    $query->orderBy("orders.{$sortField}", $sortDirection);
                }
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $ordersData = $query->offset($offset)->limit($perPage)->get();

            // Transform data to match frontend expectations
            $orders = $ordersData->map(function ($order) {
                // Get items count for this order
                $itemsCount = DB::table('order_items')->where('order_id', $order->id)->sum('quantity');

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'order_date' => $order->order_date,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'paid_amount' => $order->paid_amount,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'items_count' => $itemsCount,
                    'customer' => $order->customer_name ? [
                        'name' => $order->customer_name,
                        'email' => $order->customer_email,
                        'phone' => $order->customer_phone
                    ] : null,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone,
                    'tax_amount' => 0 // Products don't have tax as per user requirement
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $orders,
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($total / $perPage),
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting orders data: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request parameters: ' . json_encode($request->all()));
            return response()->json(['success' => false, 'message' => 'Error loading orders: ' . $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        return $this->getOrdersData($request);
    }

    public function getStats()
    {
        try {
            $stats = $this->getOrderStats();
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading stats'
            ], 500);
        }
    }

    public function getOrderStats()
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();

            // Calculate total revenue from all completed orders
            $totalRevenue = DB::table('orders')
                ->where('status', Order::STATUS_COMPLETED)
                ->sum('total_amount');

            // Calculate total completed orders for average calculation
            $totalCompletedOrders = DB::table('orders')
                ->where('status', Order::STATUS_COMPLETED)
                ->count();

            // Calculate average order value
            $averageOrder = $totalCompletedOrders > 0 ? $totalRevenue / $totalCompletedOrders : 0;

            return [
                'total_orders' => DB::table('orders')->count(),
                'pending_orders' => DB::table('orders')->where('status', Order::STATUS_PENDING)->count(),
                'todays_orders' => DB::table('orders')->whereDate('created_at', $today)->count(),
                'monthly_revenue' => DB::table('orders')
                    ->where('created_at', '>=', $thisMonth)
                    ->where('status', Order::STATUS_COMPLETED)
                    ->sum('total_amount'),
                'total_revenue' => $totalRevenue,
                'average_order' => round($averageOrder, 2),
                'processing_orders' => DB::table('orders')->where('status', Order::STATUS_IN_PROGRESS)->count(),
                'completed_orders' => $totalCompletedOrders
            ];

        } catch (\Exception $e) {
            Log::error('Error getting order stats: ' . $e->getMessage());
            return [
                'total_orders' => 0,
                'pending_orders' => 0,
                'todays_orders' => 0,
                'monthly_revenue' => 0,
                'total_revenue' => 0,
                'average_order' => 0,
                'processing_orders' => 0,
                'completed_orders' => 0
            ];
        }
    }

    private function getOrderWithRelations($id)
    {
        try {
            $order = DB::table('orders')
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('customer_addresses as delivery_addr', 'orders.delivery_address_id', '=', 'delivery_addr.id')
                ->select([
                    'orders.*',
                    'customers.name as customer_name',
                    'customers.phone as customer_phone',
                    'customers.email as customer_email',
                    'delivery_addr.address_line_1 as delivery_address',
                    'delivery_addr.city as delivery_city',
                    'delivery_addr.state as delivery_state',
                    'delivery_addr.postal_code as delivery_postal_code'
                ])
                ->where('orders.id', $id)
                ->first();

            if ($order) {
                $items = DB::table('order_items')
                    ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                    ->select([
                        'order_items.*',
                        'products.name as product_name',
                        'products.sku as product_sku',
                        'products.description as product_description'
                    ])
                    ->where('order_items.order_id', $id)
                    ->get();

                $order->items = $items;
            }

            return $order;

        } catch (\Exception $e) {
            Log::error('Error getting order with relations: ' . $e->getMessage());
            return null;
        }
    }

    private function exportOrders($orderIds)
    {
        try {
            $orders = DB::table('orders')
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->select([
                    'orders.order_number',
                    'orders.order_date',
                    'orders.status',
                    'orders.priority',
                    'orders.total_amount',
                    'orders.payment_status',
                    'customers.name as customer_name'
                ])
                ->whereIn('orders.id', $orderIds)
                ->get();

            $csvData = "Order Number,Order Date,Customer,Status,Priority,Total,Payment Status\n";

            foreach ($orders as $order) {
                $customerName = $order->customer_name ?: 'Guest Customer';
                $csvData .= '"' . $order->order_number . '","' . $order->order_date . '","' .
                           $customerName . '","' . $order->status . '","' . $order->priority . '","' .
                           number_format($order->total_amount, 2) . '","' . $order->payment_status . '"' . "\n";
            }

            $filename = 'orders_export_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('Error exporting orders: ' . $e->getMessage());
            return response()->json(['error' => 'Error exporting orders'], 500);
        }
    }

    public function getDetails($id)
    {
        try {
            $order = DB::table('orders')
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->select([
                    'orders.*',
                    'customers.name as customer_name',
                    'customers.email as customer_email',
                    'customers.phone as customer_phone',
                    'customers.company_name as customer_company'
                ])
                ->where('orders.id', $id)
                ->first();

            if (!$order) {
                Log::warning("Order not found with ID: {$id}");
                return response()->json(['success' => false, 'error' => 'Order not found'], 404);
            }

            Log::info("Found order: " . json_encode($order));

            // Get order items
            $items = DB::table('order_items')
                ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                ->select([
                    'order_items.*',
                    'products.name as product_name',
                    'products.sku'
                ])
                ->where('order_items.order_id', $id)
                ->get();

            $order->items = $items;

            // Calculate order metrics
            $metrics = [
                'total_items' => $items->count(),
                'total_quantity' => $items->sum('quantity'),
                'average_item_price' => $items->count() > 0 ? $items->avg('price') : 0,
                'days_since_order' => Carbon::parse($order->created_at)->diffInDays(now()),
                'fulfillment_status' => $this->calculateFulfillmentStatus($order),
            ];

            return response()->json([
                'success' => true,
                'data' => $order,
                'metrics' => $metrics
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting order details: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error loading order details'], 500);
        }
    }

    public function generateInvoice($id)
    {
        try {
            $order = DB::table('orders')
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->select([
                    'orders.*',
                    'customers.name as customer_name',
                    'customers.email as customer_email',
                    'customers.phone as customer_phone',
                    'customers.company_name as customer_company',
                    'customers.gst_number',
                ])
                ->where('orders.id', $id)
                ->first();

            if (!$order) {
                return redirect()->route('orders.index')->with('error', 'Order not found.');
            }

            // Check if invoice already exists for this order
            $existingInvoice = DB::table('invoices')->where('order_id', $id)->first();
            if ($existingInvoice) {
                return redirect()->route('invoices.show', $existingInvoice->id)
                    ->with('info', 'Invoice already exists for this order.');
            }

            // Get order items
            $items = DB::table('order_items')
                ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                ->select([
                    'order_items.*',
                    'products.name as product_name',
                    'products.sku'
                ])
                ->where('order_items.order_id', $id)
                ->get();

            DB::beginTransaction();

            // Generate invoice number
            $year = date('Y');
            $lastInvoice = DB::table('invoices')->whereYear('created_at', $year)->orderBy('id', 'desc')->first();
            $nextNumber = $lastInvoice ? (intval(substr($lastInvoice->invoice_number, -5)) + 1) : 1;
            $invoiceNumber = 'INV-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Create invoice
            $invoiceData = [
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now()->toDateString(),
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'customer_address' => $order->customer_address,
                'total_amount' => $order->total_amount,
                'total' => $order->total_amount,
                'subtotal' => $order->subtotal ?: $order->total_amount,
                'discount' => $order->discount ?: 0,
                'tax_amount' => $order->tax ?: 0,
                'status' => 'generated',
                'payment_status' => 'pending',
                'paid_amount' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $invoiceId = DB::table('invoices')->insertGetId($invoiceData);

            // Create invoice items
            foreach ($items as $item) {
                DB::table('invoice_items')->insert([
                    'invoice_id' => $invoiceId,
                    'description' => $item->product_name ?: 'Product',
                    'quantity' => $item->quantity,
                    'rate' => $item->price ?: $item->unit_price,
                    'tax_rate' => 0,
                    'amount' => $item->total ?: ($item->quantity * ($item->price ?: $item->unit_price)),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoiceId)
                ->with('success', 'Invoice generated successfully from order.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating invoice from order: ' . $e->getMessage());
            return back()->with('error', 'Error generating invoice: ' . $e->getMessage());
        }
    }

    private function calculateFulfillmentStatus($order)
    {
        switch ($order->status) {
            case 'pending':
                return 'Awaiting Processing';
            case 'processing':
                return 'In Progress';
            case 'shipped':
                return 'Shipped';
            case 'delivered':
                return 'Delivered';
            case 'cancelled':
                return 'Cancelled';
            default:
                return 'Unknown';
        }
    }
}
