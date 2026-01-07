<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // Sales Reports
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $salesData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->with('customer', 'items.product')
            ->get();

        return view('reports.sales', compact('salesData', 'startDate', 'endDate'));
    }

    public function salesByCategory(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $categoryData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('categories.name as category_name, SUM(order_items.quantity * order_items.unit_price) as total_revenue, SUM(order_items.quantity) as total_quantity')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        return view('reports.sales-category', compact('categoryData', 'startDate', 'endDate'));
    }

    public function salesByCustomer(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $customerData = Customer::withSum(['orders' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled');
        }], 'total_amount')
            ->withCount(['orders' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', '!=', 'cancelled');
            }])
            ->having('orders_sum_total_amount', '>', 0)
            ->orderByDesc('orders_sum_total_amount')
            ->get();

        return view('reports.sales-customer', compact('customerData', 'startDate', 'endDate'));
    }

    public function salesByProduct(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $productData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('products.name as product_name, products.sku, SUM(order_items.quantity * order_items.unit_price) as total_revenue, SUM(order_items.quantity) as total_quantity')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->get();

        return view('reports.sales-product', compact('productData', 'startDate', 'endDate'));
    }

    public function exportSales(Request $request)
    {
        // TODO: Implement sales export
        return response()->json(['success' => true, 'message' => 'Export functionality coming soon']);
    }

    // Inventory Reports
    public function inventory()
    {
        $inventoryData = Product::with('category', 'supplier')->get();
        return view('reports.inventory', compact('inventoryData'));
    }

    public function inventoryValuation()
    {
        $valuation = Product::with('category')
            ->get()
            ->map(function ($product) {
                return [
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'N/A',
                    'stock_quantity' => $product->stock_quantity,
                    'cost_per_unit' => $product->cost_per_unit ?? 0,
                    'total_value' => $product->stock_value
                ];
            });

        $totalValue = $valuation->sum('total_value');

        return view('reports.inventory-valuation', compact('valuation', 'totalValue'));
    }

    public function inventoryMovement(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // TODO: Implement inventory movement report
        return view('reports.inventory-movement', compact('startDate', 'endDate'));
    }

    public function exportInventory()
    {
        // TODO: Implement inventory export
        return response()->json(['success' => true, 'message' => 'Export functionality coming soon']);
    }

    // Financial Reports
    public function financial(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $revenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // TODO: Calculate expenses, profit, etc.

        return view('reports.financial', compact('revenue', 'startDate', 'endDate'));
    }

    public function profitLoss(Request $request)
    {
        // TODO: Implement profit & loss report
        return view('reports.profit-loss');
    }

    public function cashFlow(Request $request)
    {
        // TODO: Implement cash flow report
        return view('reports.cash-flow');
    }

    // Delivery Reports
    public function delivery(Request $request)
    {
        // TODO: Implement delivery reports
        return view('reports.delivery');
    }

    public function deliveryPerformance()
    {
        // TODO: Implement delivery performance report
        return view('reports.delivery-performance');
    }

    public function deliveryZones()
    {
        // TODO: Implement delivery zones report
        return view('reports.delivery-zones');
    }

    // Customer Reports
    public function customers()
    {
        $customerStats = Customer::selectRaw('
            COUNT(*) as total_customers,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_customers_this_month,
            AVG(CASE WHEN orders_count > 0 THEN orders_count ELSE NULL END) as avg_orders_per_customer
        ', [Carbon::now()->subMonth()])
            ->first();

        return view('reports.customers', compact('customerStats'));
    }

    public function customerAnalytics()
    {
        // TODO: Implement customer analytics
        return view('reports.customer-analytics');
    }

    // Custom Reports
    public function custom()
    {
        return view('reports.custom');
    }

    public function generateCustom(Request $request)
    {
        // TODO: Implement custom report generation
        return response()->json(['success' => true, 'message' => 'Custom report functionality coming soon']);
    }

    // API Methods for reports
    public function generateReport(Request $request)
    {
        // TODO: Implement API report generation
        return response()->json(['success' => true, 'message' => 'Report generation API coming soon']);
    }

    public function getSalesSummary(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);

        $summary = [
            'total_sales' => Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->sum('total_amount'),
            'total_orders' => Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count(),
            'average_order_value' => Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->avg('total_amount')
        ];

        return response()->json(['success' => true, 'data' => $summary]);
    }

    public function getInventorySummary()
    {
        $summary = [
            'total_products' => Product::count(),
            'total_stock_value' => Product::all()->sum('stock_value'),
            'low_stock_items' => Product::lowStock()->count()
        ];

        return response()->json(['success' => true, 'data' => $summary]);
    }

    public function getCustomerSummary()
    {
        $summary = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::whereHas('orders', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths(3));
            })->count(),
            'new_customers_this_month' => Customer::whereMonth('created_at', Carbon::now()->month)->count()
        ];

        return response()->json(['success' => true, 'data' => $summary]);
    }

    public function getFinancialSummary(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);

        $summary = [
            'total_revenue' => Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->sum('total_amount'),
            'pending_payments' => Order::where('payment_status', 'pending')
                ->sum('total_amount'),
            'refunded_amount' => Order::where('status', 'refunded')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->sum('total_amount')
        ];

        return response()->json(['success' => true, 'data' => $summary]);
    }

    public function export(Request $request)
    {
        // TODO: Implement general export functionality
        return response()->json(['success' => true, 'message' => 'Export functionality coming soon']);
    }
}