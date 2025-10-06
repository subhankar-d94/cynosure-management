<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
            $validator = Validator::make($request->all(), [
                'order_date' => 'required|date',
                'customer_type' => 'required|in:existing,walk-in,new',
                'customer_id' => 'nullable|exists:customers,id',
                'customer_name' => 'required_if:customer_type,walk-in,new|string|max:255',
                'customer_phone' => 'required_if:customer_type,walk-in,new|string|max:20',
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

            // Create order
            $orderData = [
                'order_date' => $request->order_date,
                'status' => 'pending',
                'total_amount' => $request->total ?? $request->subtotal ?? 0,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'delivery_date' => $request->expected_delivery,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Handle customer data
            if ($request->customer_type === 'existing' && $request->customer_id) {
                $orderData['customer_id'] = $request->customer_id;
            }
            // Note: For walk-in customers, we'll need to create a customer record first
            // since the orders table only has customer_id foreign key

            $orderId = DB::table('orders')->insertGetId($orderData);

            // Create order items
            foreach ($request->items as $item) {
                $itemTotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'total' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update product stock if exists
                DB::table('products')
                    ->where('id', $item['product_id'])
                    ->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            $action = $request->input('action', 'save');
            if ($action === 'save_and_process') {
                // Update status to processing
                DB::table('orders')->where('id', $orderId)->update([
                    'status' => 'processing',
                    'updated_at' => now()
                ]);

                return redirect()->route('orders.show', $orderId)
                    ->with('success', 'Order created and moved to processing!');
            }

            return redirect()->route('orders.show', $orderId)
                ->with('success', 'Order created successfully!');

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
                'customer_type' => 'required|in:existing,walk-in',
                'customer_id' => 'nullable|exists:customers,id',
                'customer_name' => 'required_if:customer_type,walk-in|string|max:255',
                'customer_phone' => 'required_if:customer_type,walk-in|string|max:20',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled',
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
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->increment('stock_quantity', $item->quantity);
            }

            // Update order
            $orderData = [
                'order_date' => $request->order_date,
                'expected_delivery' => $request->expected_delivery,
                'priority' => $request->priority ?? 'medium',
                'status' => $request->status,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax,
                'total' => $request->total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'paid_amount' => $request->paid_amount ?? 0,
                'notes' => $request->notes,
                'updated_at' => now()
            ];

            // Handle customer data
            if ($request->customer_type === 'existing' && $request->customer_id) {
                $orderData['customer_id'] = $request->customer_id;
                $orderData['customer_name'] = null;
                $orderData['customer_phone'] = null;
                $orderData['customer_email'] = null;
                $orderData['customer_address'] = null;
            } else {
                $orderData['customer_id'] = null;
                $orderData['customer_name'] = $request->customer_name;
                $orderData['customer_phone'] = $request->customer_phone;
                $orderData['customer_email'] = $request->customer_email;
                $orderData['customer_address'] = $request->customer_address;
            }

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
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'total' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update product stock
                DB::table('products')
                    ->where('id', $item['product_id'])
                    ->decrement('stock_quantity', $item['quantity']);
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
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->increment('stock_quantity', $item->quantity);
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
                'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled',
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
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->increment('stock_quantity', $item->quantity);
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
                'status' => 'required_if:action,update_status|in:pending,processing,shipped,delivered,completed,cancelled'
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
                        DB::table('products')
                            ->where('id', $item->product_id)
                            ->increment('stock_quantity', $item->quantity);
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
                    'orders.id as order_number',
                    'orders.order_date',
                    'orders.status',
                    'orders.status as priority',
                    'orders.total_amount as total',
                    'orders.status as payment_status',
                    'orders.created_at',
                    'customers.name as customer_name',
                    'customers.phone as customer_phone',
                    DB::raw('NULL as walk_in_name'),
                    DB::raw('NULL as walk_in_phone')
                ]);

            // Apply filters
            if ($request->has('status') && $request->status !== '') {
                $query->where('orders.status', $request->status);
            }

            // Skip priority filter as column doesn't exist
            // if ($request->has('priority') && $request->priority !== '') {
            //     $query->where('orders.priority', $request->priority);
            // }

            // Skip payment_status filter as column doesn't exist
            // if ($request->has('payment_status') && $request->payment_status !== '') {
            //     $query->where('orders.payment_status', $request->payment_status);
            // }

            if ($request->has('date_from') && $request->date_from) {
                $query->where('orders.order_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->where('orders.order_date', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('orders.id', 'LIKE', "%{$search}%")
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
            $orders = $query->offset($offset)->limit($perPage)->get();

            return response()->json([
                'orders' => $orders,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting orders data: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading orders'], 500);
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
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading stats'], 500);
        }
    }

    public function getOrderStats()
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();

            return [
                'total_orders' => DB::table('orders')->count(),
                'pending_orders' => DB::table('orders')->where('status', 'pending')->count(),
                'todays_orders' => DB::table('orders')->whereDate('created_at', $today)->count(),
                'monthly_revenue' => DB::table('orders')
                    ->where('created_at', '>=', $thisMonth)
                    ->where('status', 'completed')
                    ->sum('total_amount'),
                'processing_orders' => DB::table('orders')->where('status', 'processing')->count(),
                'completed_orders' => DB::table('orders')->where('status', 'completed')->count()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting order stats: ' . $e->getMessage());
            return [
                'total_orders' => 0,
                'pending_orders' => 0,
                'todays_orders' => 0,
                'monthly_revenue' => 0,
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
                ->select([
                    'orders.*',
                    'customers.name as customer_name',
                    'customers.phone as customer_phone',
                    'customers.email as customer_email',
                    'customers.address as customer_address'
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
                    'orders.total',
                    'orders.payment_status',
                    'customers.name as customer_name',
                    'orders.customer_name as walk_in_name'
                ])
                ->whereIn('orders.id', $orderIds)
                ->get();

            $csvData = "Order Number,Order Date,Customer,Status,Priority,Total,Payment Status\n";

            foreach ($orders as $order) {
                $customerName = $order->customer_name ?: $order->walk_in_name ?: 'Walk-in';
                $csvData .= '"' . $order->order_number . '","' . $order->order_date . '","' .
                           $customerName . '","' . $order->status . '","' . $order->priority . '","' .
                           number_format($order->total, 2) . '","' . $order->payment_status . '"' . "\n";
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
}