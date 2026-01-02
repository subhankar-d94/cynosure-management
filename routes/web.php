<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    // Redirect to catalog for public users, dashboard for authenticated users
    return auth()->check() ? redirect()->route('dashboard.index') : redirect()->route('catalog.index');
});

// Public Product Catalog Routes
Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('index');
    Route::get('/search', [CatalogController::class, 'search'])->name('search');
    Route::get('/download', [CatalogController::class, 'downloadCatalog'])->name('download');
    Route::get('/{categorySlug}', [CatalogController::class, 'category'])->name('category');
    Route::get('/{categorySlug}/{productId}', [CatalogController::class, 'product'])->name('product');
});

// Public shipment tracking
Route::get('/track/{waybill}', [ShipmentController::class, 'publicTracking'])->name('public-tracking');

// Authentication Routes
require __DIR__.'/auth.php';

// All authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/refresh-stats', [DashboardController::class, 'refreshStats'])->name('refresh-stats');
        Route::get('/chart-data', [DashboardController::class, 'chartData'])->name('chart-data');
        Route::get('/recent-orders', [DashboardController::class, 'getRecentOrders'])->name('recent-orders');
        Route::get('/low-stock-alerts', [DashboardController::class, 'getLowStockAlerts'])->name('low-stock-alerts');
    });

    // Category Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');

        // Additional category routes (must be before {category} routes)
        Route::get('/hierarchy', [CategoryController::class, 'getHierarchy'])->name('hierarchy');
        Route::get('/data/list', [CategoryController::class, 'getData'])->name('data');

        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/bulk-activate', [CategoryController::class, 'bulkActivate'])->name('bulk-activate');
        Route::post('/bulk-deactivate', [CategoryController::class, 'bulkDeactivate'])->name('bulk-deactivate');
        Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Product Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

        // Additional product routes
        Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
        Route::post('/bulk-update', [ProductController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/{product}/variants', [ProductController::class, 'getVariants'])->name('variants');
        Route::post('/import', [ProductController::class, 'import'])->name('import');
        Route::get('/export', [ProductController::class, 'export'])->name('export');
        Route::get('/data/list', [ProductController::class, 'getData'])->name('data');
        Route::get('/data/for-order', [ProductController::class, 'getForOrder'])->name('for-order');
        Route::post('/preview-sku', [ProductController::class, 'previewSku'])->name('preview-sku');
    });

    // Inventory Routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');

        // Inventory management routes
        Route::post('/adjust', [InventoryController::class, 'adjustStock'])->name('adjust');
        Route::post('/bulk-adjust', [InventoryController::class, 'bulkAdjust'])->name('bulk-adjust');
        Route::get('/movements/history', [InventoryController::class, 'movementHistory'])->name('movements');
        Route::post('/reorder/{inventory}', [InventoryController::class, 'createReorderAlert'])->name('reorder');
        Route::get('/valuation', [InventoryController::class, 'valuation'])->name('valuation');
        Route::get('/data/stats', [InventoryController::class, 'getStats'])->name('stats');
        Route::get('/data/list', [InventoryController::class, 'getData'])->name('data');
        Route::get('/{inventory}/history', [InventoryController::class, 'history'])->name('history');
        Route::get('/{inventory}/details', [InventoryController::class, 'getDetails'])->name('details');
        Route::get('/{inventory}/chart-data', [InventoryController::class, 'getChartData'])->name('chart-data');
        Route::get('/{inventory}/history-data', [InventoryController::class, 'getHistoryData'])->name('history-data');
        Route::get('/{inventory}/export-history', [InventoryController::class, 'exportHistory'])->name('export-history');
        Route::get('/export', [InventoryController::class, 'export'])->name('export');
    });

    // Customer Routes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');

        // Customer management routes
        Route::get('/{customer}/orders', [CustomerController::class, 'orders'])->name('orders');
        Route::get('/{customer}/addresses', [CustomerController::class, 'getAddresses'])->name('addresses');
        Route::post('/{customer}/addresses', [CustomerController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/{customer}/addresses/{address}', [CustomerController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/{customer}/addresses/{address}', [CustomerController::class, 'deleteAddress'])->name('addresses.delete');
        Route::post('/import', [CustomerController::class, 'import'])->name('import');
        Route::get('/export', [CustomerController::class, 'export'])->name('export');
        Route::get('/data/list', [CustomerController::class, 'getData'])->name('data');
    });

    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');

        // Order management routes
        Route::post('/{order}/confirm', [OrderController::class, 'confirm'])->name('confirm');
        Route::post('/{order}/process', [OrderController::class, 'process'])->name('process');
        Route::post('/{order}/complete', [OrderController::class, 'complete'])->name('complete');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/record-payment', [OrderController::class, 'recordPayment'])->name('record-payment');
        Route::post('/{order}/email', [OrderController::class, 'email'])->name('email');
        Route::post('/{order}/add-tracking', [OrderController::class, 'addTracking'])->name('add-tracking');
        Route::post('/{order}/notify', [OrderController::class, 'notify'])->name('notify');
        Route::get('/{order}/invoice', [OrderController::class, 'generateInvoice'])->name('invoice');
        Route::get('/{order}/print', [OrderController::class, 'print'])->name('print');
        Route::post('/{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');
        Route::get('/data/stats', [OrderController::class, 'getStats'])->name('stats');
        Route::get('/data/list', [OrderController::class, 'getData'])->name('data');
        Route::get('/{order}/details', [OrderController::class, 'getDetails'])->name('details');
        Route::post('/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/bulk-action', [OrderController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/export', [OrderController::class, 'export'])->name('export');
        Route::post('/calculate-shipping', [OrderController::class, 'calculateShipping'])->name('calculate-shipping');
        Route::get('/{order}/invoice/print', [OrderController::class, 'printInvoice'])->name('invoice.print');
        Route::get('/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('invoice.download');
    });

    // Invoice Routes
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');

        // Invoice management routes
        Route::get('/{invoice}/preview', [InvoiceController::class, 'preview'])->name('preview');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
        Route::post('/{invoice}/send', [InvoiceController::class, 'send'])->name('send');
        Route::post('/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('mark-paid');
        Route::post('/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])->name('mark-sent');
        Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
        Route::post('/bulk-send', [InvoiceController::class, 'bulkSend'])->name('bulk-send');
    });

    // Shipment Routes
    Route::prefix('shipments')->name('shipments.')->group(function () {
        Route::get('/', [ShipmentController::class, 'index'])->name('index');
        Route::get('/create', [ShipmentController::class, 'create'])->name('create');
        Route::post('/', [ShipmentController::class, 'store'])->name('store');
        Route::get('/{shipment}', [ShipmentController::class, 'show'])->name('show');
        Route::get('/{shipment}/edit', [ShipmentController::class, 'edit'])->name('edit');
        Route::put('/{shipment}', [ShipmentController::class, 'update'])->name('update');
        Route::delete('/{shipment}', [ShipmentController::class, 'destroy'])->name('destroy');

        // Shipment tracking routes
        Route::get('/{shipment}/track', [ShipmentController::class, 'track'])->name('track');
        Route::post('/{shipment}/update-status', [ShipmentController::class, 'updateStatus'])->name('update-status');
        Route::post('/{shipment}/create-waybill', [ShipmentController::class, 'createWaybill'])->name('create-waybill');
        Route::post('/{shipment}/cancel', [ShipmentController::class, 'cancel'])->name('cancel');
        Route::get('/{shipment}/label', [ShipmentController::class, 'printLabel'])->name('label');
        Route::post('/bulk-track', [ShipmentController::class, 'bulkTrack'])->name('bulk-track');
    });

    // Supplier Routes
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');

        // Supplier management routes
        Route::get('/{supplier}/purchases', [SupplierController::class, 'purchases'])->name('purchases');
        Route::get('/{supplier}/materials', [SupplierController::class, 'materials'])->name('materials');
        Route::post('/{supplier}/rate', [SupplierController::class, 'rate'])->name('rate');
        Route::get('/performance', [SupplierController::class, 'performance'])->name('performance');
    });

    // Purchase Routes
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseController::class, 'create'])->name('create');
        Route::post('/', [PurchaseController::class, 'store'])->name('store');
        Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('show');
        Route::get('/{purchase}/edit', [PurchaseController::class, 'edit'])->name('edit');
        Route::put('/{purchase}', [PurchaseController::class, 'update'])->name('update');
        Route::delete('/{purchase}', [PurchaseController::class, 'destroy'])->name('destroy');

        // Purchase management routes
        Route::post('/{purchase}/approve', [PurchaseController::class, 'approve'])->name('approve');
        Route::post('/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('cancel');
        Route::post('/{purchase}/receive', [PurchaseController::class, 'receive'])->name('receive');
        Route::get('/{purchase}/print', [PurchaseController::class, 'print'])->name('print');
        Route::post('/{purchase}/duplicate', [PurchaseController::class, 'duplicate'])->name('duplicate');

        // Bulk operations
        Route::post('/bulk-approve', [PurchaseController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-receive', [PurchaseController::class, 'bulkReceive'])->name('bulk-receive');
        Route::post('/bulk-cancel', [PurchaseController::class, 'bulkCancel'])->name('bulk-cancel');
        Route::post('/bulk-export', [PurchaseController::class, 'bulkExport'])->name('bulk-export');
        Route::get('/export', [PurchaseController::class, 'export'])->name('export');
    });

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');

        // Sales Reports
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/sales/category', [ReportController::class, 'salesByCategory'])->name('sales.category');
        Route::get('/sales/customer', [ReportController::class, 'salesByCustomer'])->name('sales.customer');
        Route::get('/sales/product', [ReportController::class, 'salesByProduct'])->name('sales.product');
        Route::get('/sales/export', [ReportController::class, 'exportSales'])->name('sales.export');
        Route::get('/sales-summary', [ReportController::class, 'getSalesSummary'])->name('sales-summary');

        // Inventory Reports
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/inventory/valuation', [ReportController::class, 'inventoryValuation'])->name('inventory.valuation');
        Route::get('/inventory/movement', [ReportController::class, 'inventoryMovement'])->name('inventory.movement');
        Route::get('/inventory/export', [ReportController::class, 'exportInventory'])->name('inventory.export');

        // Financial Reports
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('cash-flow');

        // Delivery Reports
        Route::get('/delivery', [ReportController::class, 'delivery'])->name('delivery');
        Route::get('/delivery/performance', [ReportController::class, 'deliveryPerformance'])->name('delivery.performance');
        Route::get('/delivery/zones', [ReportController::class, 'deliveryZones'])->name('delivery.zones');

        // Customer Reports
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/customer-analytics', [ReportController::class, 'customerAnalytics'])->name('customer-analytics');

        // Custom Reports
        Route::get('/custom', [ReportController::class, 'custom'])->name('custom');
        Route::post('/custom/generate', [ReportController::class, 'generateCustom'])->name('custom.generate');
    });

    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard-data', [AnalyticsController::class, 'getDashboardData'])->name('dashboard-data');
        Route::get('/trends', [AnalyticsController::class, 'trends'])->name('trends');
        Route::get('/forecasting', [AnalyticsController::class, 'forecasting'])->name('forecasting');
        Route::get('/cohort', [AnalyticsController::class, 'cohortAnalysis'])->name('cohort');
        Route::get('/data/{type}', [AnalyticsController::class, 'getData'])->name('data');
        Route::get('/trends/{type}', [AnalyticsController::class, 'getTrendData'])->name('trends.data');
    });

    // Address Routes (Google Places Integration)
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::post('/validate', [AddressController::class, 'validate'])->name('validate');
        Route::get('/autocomplete', [AddressController::class, 'autocomplete'])->name('autocomplete');
        Route::post('/geocode', [AddressController::class, 'geocode'])->name('geocode');
        Route::get('/serviceability/{pincode}', [AddressController::class, 'checkServiceability'])->name('serviceability');
        Route::get('/shipping-rate', [AddressController::class, 'getShippingRate'])->name('shipping-rate');
    });

    // File Upload Routes
    Route::prefix('uploads')->name('uploads.')->group(function () {
        Route::post('/product-images', [ProductController::class, 'uploadImages'])->name('product-images');
        Route::post('/invoices/logo', [InvoiceController::class, 'uploadLogo'])->name('invoice-logo');
        Route::post('/customers/import-csv', [CustomerController::class, 'importCsv'])->name('customer-csv');
        Route::post('/products/import-csv', [ProductController::class, 'importCsv'])->name('product-csv');
    });

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

// Webhook Routes (public, no authentication)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/delhivery', [ShipmentController::class, 'delhiveryWebhook'])->name('delhivery');
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});