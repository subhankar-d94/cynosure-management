<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('analytics.index');
    }

    public function dashboard()
    {
        return view('analytics.dashboard');
    }

    public function trends(Request $request)
    {
        $period = $request->get('period', '30');
        $type = $request->get('type', 'sales');

        return view('analytics.trends', compact('period', 'type'));
    }

    public function forecasting()
    {
        return view('analytics.forecasting');
    }

    public function cohortAnalysis()
    {
        return view('analytics.cohort');
    }

    public function getData($type, Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        switch ($type) {
            case 'sales':
                return $this->getSalesData($period);
            case 'orders':
                return $this->getOrderData($period);
            case 'customers':
                return $this->getCustomerData($period);
            case 'inventory':
                return $this->getInventoryData($period);
            case 'revenue':
                return $this->getRevenueData($period);
            default:
                return response()->json(['error' => 'Invalid data type'], 400);
        }
    }

    public function getSalesData($period = '30')
    {
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        $salesData = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_sales, COUNT(*) as order_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $sales = [];
        $orders = [];

        foreach ($salesData as $data) {
            $labels[] = Carbon::parse($data->date)->format('M d');
            $sales[] = (float) $data->total_sales;
            $orders[] = (int) $data->order_count;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Sales (₹)',
                        'data' => $sales,
                        'borderColor' => '#0d6efd',
                        'backgroundColor' => 'rgba(13, 110, 253, 0.1)',
                    ],
                    [
                        'label' => 'Orders',
                        'data' => $orders,
                        'borderColor' => '#198754',
                        'backgroundColor' => 'rgba(25, 135, 84, 0.1)',
                        'yAxisID' => 'y1'
                    ]
                ]
            ]
        ]);
    }

    public function getOrderData($period = '30')
    {
        $startDate = Carbon::now()->subDays($period);

        $orderStats = Order::whereBetween('created_at', [$startDate, Carbon::now()])
            ->selectRaw('
                status,
                COUNT(*) as count,
                SUM(total_amount) as total_amount,
                AVG(total_amount) as avg_amount
            ')
            ->groupBy('status')
            ->get();

        $statusCounts = [];
        $statusRevenue = [];
        $labels = [];

        foreach ($orderStats as $stat) {
            $labels[] = ucfirst(str_replace('_', ' ', $stat->status));
            $statusCounts[] = $stat->count;
            $statusRevenue[] = (float) $stat->total_amount;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'counts' => $statusCounts,
                'revenue' => $statusRevenue,
                'stats' => $orderStats
            ]
        ]);
    }

    public function getCustomerData($period = '30')
    {
        $startDate = Carbon::now()->subDays($period);

        // New customers
        $newCustomers = Customer::whereBetween('created_at', [$startDate, Carbon::now()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Customer segments by order count
        $customerSegments = Customer::withCount('orders')
            ->get()
            ->groupBy(function ($customer) {
                if ($customer->orders_count == 0) return 'No Orders';
                if ($customer->orders_count == 1) return 'One-time';
                if ($customer->orders_count <= 5) return 'Regular';
                return 'Loyal';
            });

        $segmentData = [];
        foreach ($customerSegments as $segment => $customers) {
            $segmentData[] = [
                'label' => $segment,
                'count' => $customers->count(),
                'percentage' => round(($customers->count() / Customer::count()) * 100, 1)
            ];
        }

        // Top customers by revenue
        $topCustomers = Customer::withSum('orders', 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'new_customers' => $newCustomers,
                'segments' => $segmentData,
                'top_customers' => $topCustomers
            ]
        ]);
    }

    public function getInventoryData($period = '30')
    {
        // Stock levels by category
        $stockByCategory = Product::with('category', 'inventory')
            ->whereHas('inventory')
            ->get()
            ->groupBy('category.name')
            ->map(function ($products) {
                return [
                    'total_products' => $products->count(),
                    'total_stock' => $products->sum('inventory.quantity_in_stock'),
                    'total_value' => $products->sum(function ($product) {
                        return $product->price * $product->inventory->quantity_in_stock;
                    })
                ];
            });

        // Low stock alerts
        $lowStockProducts = Inventory::with('product')
            ->whereRaw('quantity_in_stock <= reorder_level')
            ->get();

        // Inventory movement over time (check if table exists)
        $inventoryMovements = collect();
        try {
            $startDate = Carbon::now()->subDays($period);
            if (Schema::hasTable('inventory_movements')) {
                $inventoryMovements = DB::table('inventory_movements')
                    ->whereBetween('created_at', [$startDate, Carbon::now()])
                    ->selectRaw('DATE(created_at) as date, movement_type, SUM(ABS(quantity)) as total_quantity')
                    ->groupBy('date', 'movement_type')
                    ->orderBy('date')
                    ->get();
            }
        } catch (\Exception $e) {
            Log::info('Inventory movements table not available: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'stock_by_category' => $stockByCategory,
                'low_stock_count' => $lowStockProducts->count(),
                'low_stock_products' => $lowStockProducts,
                'movements' => $inventoryMovements
            ]
        ]);
    }

    public function getRevenueData($period = '30')
    {
        $startDate = Carbon::now()->subDays($period);

        // Revenue by day
        $dailyRevenue = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, Carbon::now()])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by product category
        $categoryRevenue = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$startDate, Carbon::now()])
            ->selectRaw('categories.name as category, SUM(order_items.quantity * order_items.unit_price) as revenue')
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->get();

        // Monthly comparison
        $thisMonth = Order::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $lastMonth = Order::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total_amount');

        $growthRate = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'daily_revenue' => $dailyRevenue,
                'category_revenue' => $categoryRevenue,
                'this_month' => $thisMonth,
                'last_month' => $lastMonth,
                'growth_rate' => round($growthRate, 2)
            ]
        ]);
    }

    public function getTrendData($type, Request $request)
    {
        $period = $request->get('period', '90');
        $startDate = Carbon::now()->subDays($period);

        switch ($type) {
            case 'sales_trend':
                return $this->getSalesTrend($startDate);
            case 'customer_acquisition':
                return $this->getCustomerAcquisitionTrend($startDate);
            case 'product_performance':
                return $this->getProductPerformanceTrend($startDate);
            default:
                return response()->json(['error' => 'Invalid trend type'], 400);
        }
    }

    private function getSalesTrend($startDate)
    {
        $trends = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, Carbon::now()])
            ->selectRaw('
                WEEK(created_at) as week,
                YEAR(created_at) as year,
                SUM(total_amount) as revenue,
                COUNT(*) as orders,
                AVG(total_amount) as avg_order_value
            ')
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $trends
        ]);
    }

    private function getCustomerAcquisitionTrend($startDate)
    {
        $acquisition = Customer::whereBetween('created_at', [$startDate, Carbon::now()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as new_customers')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $acquisition
        ]);
    }

    private function getProductPerformanceTrend($startDate)
    {
        $performance = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$startDate, Carbon::now()])
            ->selectRaw('
                products.name as product_name,
                products.sku as product_sku,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.quantity * order_items.unit_price) as total_revenue
            ')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }

    // Additional methods for dashboard widgets
    public function getDashboardData()
    {
        try {
            $today = Carbon::now()->startOfDay();
            $weekAgo = Carbon::now()->subWeek();
            $monthAgo = Carbon::now()->subMonth();

            // Get order stats with proper error handling
            $todaySales = 0;
            $weekSales = 0;
            $monthSales = 0;
            $totalOrders = 0;

            try {
                $todaySales = Order::where('status', '!=', 'cancelled')
                    ->whereDate('created_at', $today)
                    ->sum('total_amount') ?? 0;

                $weekSales = Order::where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [$weekAgo, Carbon::now()])
                    ->sum('total_amount') ?? 0;

                $monthSales = Order::where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [$monthAgo, Carbon::now()])
                    ->sum('total_amount') ?? 0;

                $totalOrders = Order::where('status', '!=', 'cancelled')->count();
            } catch (\Exception $e) {
                Log::warning('Error fetching order data for analytics: ' . $e->getMessage());
            }

            // Get inventory stats with error handling
            $lowStockCount = 0;
            try {
                $lowStockCount = Inventory::whereRaw('quantity_in_stock <= reorder_level')->count();
            } catch (\Exception $e) {
                Log::warning('Error fetching inventory data for analytics: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'today_sales' => $todaySales,
                    'week_sales' => $weekSales,
                    'month_sales' => $monthSales,
                    'total_orders' => $totalOrders,
                    'total_customers' => Customer::count(),
                    'total_products' => Product::count(),
                    'low_stock_count' => $lowStockCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDashboardData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error loading dashboard data',
                'data' => [
                    'today_sales' => 0,
                    'week_sales' => 0,
                    'month_sales' => 0,
                    'total_orders' => 0,
                    'total_customers' => 0,
                    'total_products' => 0,
                    'low_stock_count' => 0
                ]
            ]);
        }
    }

    public function getSalesChart($period = '30')
    {
        return $this->getSalesData($period);
    }
}