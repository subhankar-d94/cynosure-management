<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// User API route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// All API routes require authentication
Route::middleware(['auth:sanctum'])->group(function () {

    // Product API
    Route::prefix('products')->name('api.products.')->group(function () {
        Route::get('/', [ProductController::class, 'apiIndex'])->name('index');
        Route::get('/{product}', [ProductController::class, 'apiShow'])->name('show');
        Route::get('/search/{query}', [ProductController::class, 'apiSearch'])->name('search');
        Route::get('/category/{category}', [ProductController::class, 'apiByCategory'])->name('by-category');
    });

    // Customer API
    Route::prefix('customers')->name('api.customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'apiIndex'])->name('index');
        Route::get('/{customer}', [CustomerController::class, 'apiShow'])->name('show');
        Route::get('/search/{query}', [CustomerController::class, 'apiSearch'])->name('search');
        Route::get('/{customer}/orders', [CustomerController::class, 'apiOrders'])->name('orders');
        Route::get('/{customer}/addresses', [CustomerController::class, 'apiAddresses'])->name('addresses');
    });

    // Inventory API
    Route::prefix('inventory')->name('api.inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'apiIndex'])->name('index');
        Route::get('/low-stock', [InventoryController::class, 'apiLowStock'])->name('low-stock');
        Route::get('/{inventory}', [InventoryController::class, 'apiShow'])->name('show');
        Route::post('/{inventory}/quick-adjust', [InventoryController::class, 'quickAdjust'])->name('quick-adjust');
        Route::get('/stats/summary', [InventoryController::class, 'apiStats'])->name('stats');
    });

    // Order API
    Route::prefix('orders')->name('api.orders.')->group(function () {
        Route::get('/', [OrderController::class, 'apiIndex'])->name('index');
        Route::get('/{order}', [OrderController::class, 'apiShow'])->name('show');
        Route::get('/{order}/items', [OrderController::class, 'getOrderItems'])->name('items');
        Route::post('/{order}/add-item', [OrderController::class, 'addItem'])->name('add-item');
        Route::put('/{order}/update-item', [OrderController::class, 'updateItem'])->name('update-item');
        Route::delete('/{order}/remove-item/{item}', [OrderController::class, 'removeItem'])->name('remove-item');
        Route::post('/{order}/recalculate', [OrderController::class, 'recalculate'])->name('recalculate');
        Route::get('/stats/summary', [OrderController::class, 'apiStats'])->name('stats');
    });

    // Shipment API
    Route::prefix('shipments')->name('api.shipments.')->group(function () {
        Route::get('/', [ShipmentController::class, 'apiIndex'])->name('index');
        Route::get('/{shipment}', [ShipmentController::class, 'apiShow'])->name('show');
        Route::get('/{shipment}/status', [ShipmentController::class, 'getStatus'])->name('status');
        Route::post('/{shipment}/track-update', [ShipmentController::class, 'updateTracking'])->name('track-update');
        Route::get('/track/{waybill}', [ShipmentController::class, 'trackByWaybill'])->name('track-waybill');
    });

    // Analytics API
    Route::prefix('analytics')->name('api.analytics.')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'getDashboardData'])->name('dashboard');
        Route::get('/sales/{period}', [AnalyticsController::class, 'getSalesData'])->name('sales');
        Route::get('/inventory/{period}', [AnalyticsController::class, 'getInventoryData'])->name('inventory');
        Route::get('/customers/{period}', [AnalyticsController::class, 'getCustomerData'])->name('customers');
        Route::get('/orders/{period}', [AnalyticsController::class, 'getOrderData'])->name('orders');
        Route::get('/revenue/{period}', [AnalyticsController::class, 'getRevenueData'])->name('revenue');
        Route::get('/trends/{type}', [AnalyticsController::class, 'getTrendData'])->name('trends');
    });

    // Reports API
    Route::prefix('reports')->name('api.reports.')->group(function () {
        Route::post('/generate', [ReportController::class, 'generateReport'])->name('generate');
        Route::get('/sales-summary', [ReportController::class, 'getSalesSummary'])->name('sales-summary');
        Route::get('/inventory-summary', [ReportController::class, 'getInventorySummary'])->name('inventory-summary');
        Route::get('/customer-summary', [ReportController::class, 'getCustomerSummary'])->name('customer-summary');
        Route::get('/financial-summary', [ReportController::class, 'getFinancialSummary'])->name('financial-summary');
    });

    // Search API
    Route::prefix('search')->name('api.search.')->group(function () {
        Route::get('/global/{query}', function ($query) {
            // Global search across all entities
            $results = [];

            // Search products
            $products = \App\Models\Product::where('name', 'like', "%{$query}%")
                ->orWhere('sku', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'name', 'sku', 'price']);

            // Search customers
            $customers = \App\Models\Customer::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'name', 'email', 'phone']);

            // Search orders
            $orders = \App\Models\Order::where('id', 'like', "%{$query}%")
                ->orWhereHas('customer', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->with('customer:id,name')
                ->limit(5)
                ->get(['id', 'customer_id', 'total_amount', 'status', 'created_at']);

            return response()->json([
                'success' => true,
                'results' => [
                    'products' => $products,
                    'customers' => $customers,
                    'orders' => $orders
                ]
            ]);
        })->name('global');
    });

    // Quick Actions API
    Route::prefix('quick-actions')->name('api.quick.')->group(function () {
        Route::post('/stock-alert/{product}', [InventoryController::class, 'createStockAlert'])->name('stock-alert');
        Route::post('/reorder/{product}', [InventoryController::class, 'createReorder'])->name('reorder');
        Route::post('/order-note/{order}', [OrderController::class, 'addNote'])->name('order-note');
        Route::post('/customer-note/{customer}', [CustomerController::class, 'addNote'])->name('customer-note');
    });

    // Dashboard Widgets API
    Route::prefix('widgets')->name('api.widgets.')->group(function () {
        Route::get('/sales-chart/{period?}', [AnalyticsController::class, 'getSalesChart'])->name('sales-chart');
        Route::get('/order-stats', [OrderController::class, 'getOrderStats'])->name('order-stats');
        Route::get('/inventory-alerts', [InventoryController::class, 'getInventoryAlerts'])->name('inventory-alerts');
        Route::get('/recent-orders/{limit?}', [OrderController::class, 'getRecentOrders'])->name('recent-orders');
        Route::get('/top-products/{limit?}', [ProductController::class, 'getTopProducts'])->name('top-products');
        Route::get('/top-customers/{limit?}', [CustomerController::class, 'getTopCustomers'])->name('top-customers');
    });

    // Bulk Operations API
    Route::prefix('bulk')->name('api.bulk.')->group(function () {
        Route::post('/products/update', [ProductController::class, 'bulkUpdate'])->name('products.update');
        Route::post('/orders/status', [OrderController::class, 'bulkUpdateStatus'])->name('orders.status');
        Route::post('/inventory/adjust', [InventoryController::class, 'bulkAdjust'])->name('inventory.adjust');
        Route::delete('/products/delete', [ProductController::class, 'bulkDelete'])->name('products.delete');
        Route::delete('/customers/delete', [CustomerController::class, 'bulkDelete'])->name('customers.delete');
    });

    // Export API
    Route::prefix('export')->name('api.export.')->group(function () {
        Route::post('/products', [ProductController::class, 'export'])->name('products');
        Route::post('/customers', [CustomerController::class, 'export'])->name('customers');
        Route::post('/orders', [OrderController::class, 'export'])->name('orders');
        Route::post('/inventory', [InventoryController::class, 'export'])->name('inventory');
        Route::post('/reports', [ReportController::class, 'export'])->name('reports');
    });

    // Import API
    Route::prefix('import')->name('api.import.')->group(function () {
        Route::post('/products', [ProductController::class, 'import'])->name('products');
        Route::post('/customers', [CustomerController::class, 'import'])->name('customers');
        Route::post('/inventory', [InventoryController::class, 'import'])->name('inventory');
        Route::get('/template/{type}', function ($type) {
            // Return CSV templates for import
            $templates = [
                'products' => [
                    'name', 'sku', 'description', 'price', 'category', 'quantity_in_stock'
                ],
                'customers' => [
                    'name', 'email', 'phone', 'address', 'city', 'state', 'pincode'
                ],
                'inventory' => [
                    'product_sku', 'quantity_in_stock', 'reorder_level', 'supplier'
                ]
            ];

            if (!isset($templates[$type])) {
                return response()->json(['error' => 'Invalid template type'], 400);
            }

            return response()->json([
                'success' => true,
                'headers' => $templates[$type]
            ]);
        })->name('template');
    });
});

// Public API routes (no authentication)
Route::prefix('public')->name('api.public.')->group(function () {
    Route::get('/track/{waybill}', [ShipmentController::class, 'publicTrackingApi'])->name('track');
    Route::get('/product/{sku}', [ProductController::class, 'publicProductInfo'])->name('product');
    Route::post('/contact', function (Request $request) {
        // Handle contact form submissions
        return response()->json(['success' => true, 'message' => 'Message received']);
    })->name('contact');
});

// Webhook API routes (no authentication, but should have IP restrictions in production)
Route::prefix('webhooks')->name('api.webhooks.')->group(function () {
    Route::post('/delhivery', [ShipmentController::class, 'delhiveryWebhook'])->name('delhivery');
    Route::post('/payment/{gateway}', function ($gateway, Request $request) {
        // Handle payment webhooks
        return response()->json(['success' => true]);
    })->name('payment');
});