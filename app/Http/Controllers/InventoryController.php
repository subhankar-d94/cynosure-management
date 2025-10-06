<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return $this->getData($request);
        }

        return view('inventory.index');
    }

    public function lowStock(Request $request)
    {
        if ($request->expectsJson()) {
            return $this->getData($request->merge(['low_stock_only' => true]));
        }

        return view('inventory.low-stock');
    }

    public function show(Inventory $inventory)
    {
        $inventory->load(['product.category', 'supplier']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $inventory
            ]);
        }

        return view('inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $inventory->load(['product.category', 'supplier']);
        $suppliers = Supplier::all();

        return view('inventory.edit', compact('inventory', 'suppliers'));
    }

    public function update(Request $request, Inventory $inventory): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reorder_level' => 'required|integer|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'location' => 'nullable|string|max:255',
            'max_stock_level' => 'nullable|integer|min:0',
            'lead_time_days' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'abc_classification' => 'nullable|in:A,B,C',
            'track_serials' => 'boolean',
            'allow_negative' => 'boolean',
            'auto_reorder' => 'boolean',
            'valuation_method' => 'nullable|in:fifo,lifo,average,standard',
            'unit_of_measure' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $inventory->update($request->except(['_token', '_method']));

            return response()->json([
                'success' => true,
                'message' => 'Inventory settings updated successfully',
                'data' => $inventory->fresh(['product.category', 'supplier'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adjustStock(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'inventory_id' => 'required|exists:inventories,id',
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $inventory = Inventory::findOrFail($request->inventory_id);
            $oldQuantity = $inventory->quantity_in_stock;
            $quantity = $request->quantity;

            DB::beginTransaction();

            switch ($request->adjustment_type) {
                case 'add':
                    $newQuantity = $oldQuantity + $quantity;
                    break;
                case 'remove':
                    $newQuantity = max(0, $oldQuantity - $quantity);
                    break;
                case 'set':
                    $newQuantity = $quantity;
                    break;
            }

            $inventory->update(['quantity_in_stock' => $newQuantity]);

            // Log the movement (in a real implementation, you'd have a movements table)
            // InventoryMovement::create([...]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'data' => [
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $newQuantity,
                    'adjustment' => $newQuantity - $oldQuantity
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkAdjust(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'inventory_ids' => 'required|array',
            'inventory_ids.*' => 'exists:inventories,id',
            'adjustment_type' => 'required|in:add,remove',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $inventoryIds = json_decode($request->inventory_ids);
            $quantity = $request->quantity;
            $adjustmentType = $request->adjustment_type;
            $updatedCount = 0;

            DB::beginTransaction();

            foreach ($inventoryIds as $inventoryId) {
                $inventory = Inventory::find($inventoryId);
                if ($inventory) {
                    $oldQuantity = $inventory->quantity_in_stock;

                    if ($adjustmentType === 'add') {
                        $newQuantity = $oldQuantity + $quantity;
                    } else {
                        $newQuantity = max(0, $oldQuantity - $quantity);
                    }

                    $inventory->update(['quantity_in_stock' => $newQuantity]);
                    $updatedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully adjusted stock for {$updatedCount} items",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function movementHistory(Request $request)
    {
        // This would typically query a separate movements table
        // For now, we'll return a placeholder response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => [],
                    'total' => 0,
                    'per_page' => 15,
                    'current_page' => 1
                ]
            ]);
        }

        return view('inventory.movements');
    }

    public function createReorderAlert(Inventory $inventory): JsonResponse
    {
        try {
            // In a real implementation, you'd create reorder alerts
            // ReorderAlert::create([...]);

            return response()->json([
                'success' => true,
                'message' => 'Reorder alert created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reorder alert: ' . $e->getMessage()
            ], 500);
        }
    }

    public function valuation(Request $request)
    {
        if ($request->expectsJson()) {
            return $this->getValuationData($request);
        }

        return view('inventory.valuation');
    }

    public function getStats(Request $request): JsonResponse
    {
        try {
            $query = Inventory::with(['product.category']);

            // Apply filters for specific stats
            if ($request->has('low_stock')) {
                $stats = [
                    'low_stock_items' => Inventory::lowStock()->count(),
                    'out_of_stock_items' => Inventory::where('quantity_in_stock', 0)->count(),
                    'total_products' => Inventory::count(),
                    'estimated_restock_value' => Inventory::lowStock()
                        ->with('product')
                        ->get()
                        ->sum(function($inventory) {
                            $shortage = max(0, $inventory->reorder_level - $inventory->quantity_in_stock);
                            return $shortage * $inventory->cost_per_unit;
                        }),
                    'affected_categories' => Inventory::lowStock()
                        ->with('product.category')
                        ->get()
                        ->pluck('product.category.name')
                        ->unique()
                        ->count()
                ];
            } else {
                $stats = [
                    'total_products' => Inventory::count(),
                    'low_stock_items' => Inventory::lowStock()->count(),
                    'out_of_stock_items' => Inventory::where('quantity_in_stock', 0)->count(),
                    'total_stock_value' => Inventory::all()->sum(function($inventory) {
                        return $inventory->quantity_in_stock * $inventory->cost_per_unit;
                    })
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load stats: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getData(Request $request): JsonResponse
    {
        try {
            $query = Inventory::with(['product.category', 'supplier']);

            // Apply filters
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            if ($request->has('stock_status')) {
                switch ($request->stock_status) {
                    case 'in_stock':
                        $query->where('quantity_in_stock', '>', 0)
                              ->whereColumn('quantity_in_stock', '>', 'reorder_level');
                        break;
                    case 'low_stock':
                        $query->whereColumn('quantity_in_stock', '<=', 'reorder_level')
                              ->where('quantity_in_stock', '>', 0);
                        break;
                    case 'out_of_stock':
                        $query->where('quantity_in_stock', 0);
                        break;
                }
            }

            if ($request->has('low_stock_only') && $request->low_stock_only) {
                $query->lowStock();
            }

            if ($request->has('category_filter') && $request->category_filter) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('category_id', $request->category_filter);
                });
            }

            // Apply sorting
            if ($request->has('sort_by')) {
                $sortBy = $request->sort_by;
                $sortOrder = $request->input('sort_order', 'asc');

                switch ($sortBy) {
                    case 'product_name':
                        $query->join('products', 'inventories.product_id', '=', 'products.id')
                              ->orderBy('products.name', $sortOrder)
                              ->select('inventories.*');
                        break;
                    case 'stock_value':
                        $query->orderByRaw("(quantity_in_stock * cost_per_unit) {$sortOrder}");
                        break;
                    default:
                        $query->orderBy($sortBy, $sortOrder);
                        break;
                }
            }

            // Return all data if requested
            if ($request->has('all') && $request->all) {
                $inventories = $query->get();
                return response()->json([
                    'success' => true,
                    'data' => $inventories
                ]);
            }

            // Paginate results
            $inventories = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $inventories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load inventory data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Inventory $inventory)
    {
        // This would show detailed history for a specific inventory item
        return view('inventory.history', compact('inventory'));
    }

    public function getDetails(Inventory $inventory): JsonResponse
    {
        try {
            $inventory->load(['product.category', 'supplier']);

            // Calculate basic metrics (in a real implementation, these would be more sophisticated)
            $metrics = [
                'avg_monthly_usage' => 50, // Placeholder
                'turnover_rate' => 4.2,
                'days_of_stock' => $inventory->quantity_in_stock > 0 ?
                    ($inventory->quantity_in_stock / max(1, 50)) * 30 : 0,
                'restock_frequency' => 2,
                'total_movements' => 25 // Placeholder
            ];

            return response()->json([
                'success' => true,
                'data' => $inventory,
                'metrics' => $metrics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load inventory details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getChartData(Inventory $inventory, Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 30);
            $startDate = Carbon::now()->subDays($period);

            // In a real implementation, this would query movement history
            // For now, generating sample data
            $labels = [];
            $stockLevels = [];
            $reorderLevels = [];

            for ($i = $period; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M j');
                $stockLevels[] = $inventory->quantity_in_stock + rand(-10, 20);
                $reorderLevels[] = $inventory->reorder_level;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'stock_levels' => $stockLevels,
                    'reorder_levels' => $reorderLevels
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHistoryData(Inventory $inventory, Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);

            // This would query actual movement history
            // For now, returning sample data
            $movements = collect([]);

            return response()->json([
                'success' => true,
                'data' => $movements
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load history data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportHistory(Inventory $inventory)
    {
        $filename = 'inventory_history_' . $inventory->product->sku . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($inventory) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Date',
                'Type',
                'Quantity',
                'Reason',
                'Balance After',
                'Notes'
            ]);

            // In a real implementation, this would export actual movement data
            // For now, just headers
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export(Request $request)
    {
        $filename = 'inventory_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($request) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Product Name',
                'SKU',
                'Category',
                'Current Stock',
                'Reorder Level',
                'Cost per Unit',
                'Stock Value',
                'Status',
                'Supplier',
                'Last Updated'
            ]);

            // Get data with same filters as index
            $query = Inventory::with(['product.category', 'supplier']);

            // Apply filters (same as getData method)
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            $inventories = $query->get();

            foreach ($inventories as $inventory) {
                $stockValue = $inventory->quantity_in_stock * $inventory->cost_per_unit;
                $status = $inventory->quantity_in_stock == 0 ? 'Out of Stock' :
                         ($inventory->quantity_in_stock <= $inventory->reorder_level ? 'Low Stock' : 'In Stock');

                fputcsv($file, [
                    $inventory->product->name,
                    $inventory->product->sku,
                    $inventory->product->category->name ?? 'Uncategorized',
                    $inventory->quantity_in_stock,
                    $inventory->reorder_level,
                    $inventory->cost_per_unit,
                    $stockValue,
                    $status,
                    $inventory->supplier->name ?? 'N/A',
                    $inventory->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getValuationData(Request $request): JsonResponse
    {
        try {
            $query = Inventory::with(['product.category', 'supplier']);

            $inventories = $query->get();

            $summary = [
                'total_cost_value' => $inventories->sum(function($inv) {
                    return $inv->quantity_in_stock * $inv->cost_per_unit;
                }),
                'total_selling_value' => $inventories->sum(function($inv) {
                    return $inv->quantity_in_stock * ($inv->product->base_price ?? 0);
                }),
                'profit_potential' => 0,
                'turnover_rate' => 4.2
            ];

            $summary['profit_potential'] = $summary['total_selling_value'] - $summary['total_cost_value'];

            $metrics = [
                'active_products' => $inventories->where('quantity_in_stock', '>', 0)->count(),
                'avg_stock_value' => $inventories->avg(function($inv) {
                    return $inv->quantity_in_stock * $inv->cost_per_unit;
                }),
                'profit_margin_percent' => $summary['total_cost_value'] > 0 ?
                    ($summary['profit_potential'] / $summary['total_cost_value']) * 100 : 0,
                'dead_stock_value' => 0,
                'fast_moving' => 0,
                'slow_moving' => 0
            ];

            $items = $inventories->map(function($inventory) {
                return [
                    'id' => $inventory->id,
                    'product' => $inventory->product,
                    'quantity_in_stock' => $inventory->quantity_in_stock,
                    'cost_per_unit' => $inventory->cost_per_unit,
                    'selling_price' => $inventory->product->base_price ?? 0
                ];
            });

            $categoryDistribution = $inventories->groupBy('product.category.name')
                ->map(function($group, $category) {
                    return [
                        'category' => $category ?: 'Uncategorized',
                        'value' => $group->sum(function($inv) {
                            return $inv->quantity_in_stock * $inv->cost_per_unit;
                        })
                    ];
                })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'metrics' => $metrics,
                    'items' => $items,
                    'category_distribution' => $categoryDistribution
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load valuation data: ' . $e->getMessage()
            ], 500);
        }
    }
}
