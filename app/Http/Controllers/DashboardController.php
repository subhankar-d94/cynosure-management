<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->getStatistics();
        $charts = $this->getChartData();
        $lowStockAlerts = $this->getLowStockAlerts();
        $recentOrders = $this->getRecentOrders();

        return view('dashboard.index', compact('stats', 'charts', 'lowStockAlerts', 'recentOrders'));
    }

    public function getStatistics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month revenue
        $monthlyRevenue = Order::where('created_at', '>=', $thisMonth)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // Last month revenue for comparison
        $lastMonthRevenue = Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // Calculate revenue growth
        $revenueGrowth = $lastMonthRevenue > 0
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Calculate monthly COGS (Cost of Goods Sold)
        $monthlyCogs = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.created_at', '>=', $thisMonth)
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw('SUM(order_items.quantity * COALESCE(products.cost_per_unit, 0)) as total_cogs')
            ->value('total_cogs') ?? 0;

        // Calculate gross profit
        $monthlyProfit = $monthlyRevenue - $monthlyCogs;
        $profitMargin = $monthlyRevenue > 0 ? ($monthlyProfit / $monthlyRevenue) * 100 : 0;

        // Today's stats
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $todayOrders = Order::whereDate('created_at', $today)->count();

        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_customers' => Customer::count(),
            'total_products' => Product::count(),
            'low_stock_items' => Product::lowStock()->count(),
            'monthly_revenue' => $monthlyRevenue,
            'monthly_cogs' => $monthlyCogs,
            'monthly_profit' => $monthlyProfit,
            'profit_margin' => $profitMargin,
            'revenue_growth' => $revenueGrowth,
            'monthly_orders' => Order::where('created_at', '>=', $thisMonth)->count(),
            'today_revenue' => $todayRevenue,
            'today_orders' => $todayOrders,
            'pending_invoices' => Invoice::where('status', '!=', 'paid')->count(),
            'pending_shipments' => Shipment::where('status', 'pending')->count(),
            'total_suppliers' => \App\Models\Supplier::count(),
        ];
    }

    public function getChartData(): array
    {
        return [
            'monthly_sales' => $this->getMonthlySalesData(),
            'order_status_distribution' => $this->getOrderStatusData(),
            'customer_type_distribution' => $this->getCustomerTypeData(),
            'category_sales' => $this->getCategorySalesData(),
            'inventory_alerts' => $this->getInventoryAlertsData(),
        ];
    }

    public function getLowStockAlerts()
    {
        return Product::with(['category'])
            ->lowStock()
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $product->stock_quantity,
                    'reorder_level' => $product->reorder_level,
                    'shortage' => $product->reorder_level - $product->stock_quantity,
                    'category' => $product->category->name ?? 'N/A',
                ];
            });
    }

    public function getRecentOrders()
    {
        return Order::with(['customer', 'items.product'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer->name,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'order_date' => $order->order_date->format('Y-m-d'),
                    'items_count' => $order->items->count(),
                ];
            });
    }

    private function getMonthlySalesData(): array
    {
        $data = Order::select(
                DB::raw('DATE_FORMAT(order_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('status', '!=', 'cancelled')
            ->where('order_date', '>=', Carbon::now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $data->pluck('total_sales', 'month')->toArray();
    }

    private function getOrderStatusData(): array
    {
        return Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getCustomerTypeData(): array
    {
        return Customer::select('customer_type', DB::raw('COUNT(*) as count'))
            ->groupBy('customer_type')
            ->pluck('count', 'customer_type')
            ->toArray();
    }

    private function getCategorySalesData(): array
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('categories.name', DB::raw('SUM(order_items.subtotal) as total_sales'))
            ->groupBy('categories.name')
            ->pluck('total_sales', 'categories.name')
            ->toArray();
    }

    private function getInventoryAlertsData(): array
    {
        $lowStock = Product::lowStock()->count();
        $normalStock = Product::whereColumn('stock_quantity', '>', 'reorder_level')->count();

        return [
            'low_stock' => $lowStock,
            'normal_stock' => $normalStock,
        ];
    }

    public function refreshStats(): JsonResponse
    {
        $stats = $this->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function chartData(Request $request): JsonResponse
    {
        $type = $request->query('type', 'monthly_sales');

        $chartData = match($type) {
            'monthly_sales' => $this->getMonthlySalesData(),
            'order_status' => $this->getOrderStatusData(),
            'customer_type' => $this->getCustomerTypeData(),
            'category_sales' => $this->getCategorySalesData(),
            'inventory_alerts' => $this->getInventoryAlertsData(),
            default => []
        };

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }
}
