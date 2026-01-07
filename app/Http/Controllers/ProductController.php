<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Stock status filter
        if ($request->filled('stock_filter') && $request->stock_filter != '') {
            switch ($request->stock_filter) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0)
                          ->whereColumn('stock_quantity', '>', 'reorder_level');
                    break;
                case 'low_stock':
                    $query->whereColumn('stock_quantity', '<=', 'reorder_level')
                          ->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
            }
        }

        // Customizable filter
        if ($request->filled('customizable_filter') && $request->customizable_filter != '') {
            $query->where('is_customizable', $request->customizable_filter);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'stock') {
            $query->orderBy('stock_quantity', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage)->appends($request->except('page'));

        $categories = Category::active()->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();
        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'base_price' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric|min:0',
            'dimensions.width' => 'nullable|numeric|min:0',
            'dimensions.height' => 'nullable|numeric|min:0',
            'is_customizable' => 'boolean',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'initial_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'product_images' => 'nullable|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'weight' => $request->weight,
                'dimensions' => $request->dimensions,
                'is_customizable' => $request->boolean('is_customizable'),
                'sku' => $request->sku ?: null, // Let model auto-generate if empty
                'stock_quantity' => $request->input('initial_stock', 0),
                'reorder_level' => $request->input('reorder_level', 10),
                'cost_per_unit' => $request->input('cost_per_unit'),
                'supplier_id' => $request->supplier_id
            ]);

            // Handle image uploads
            if ($request->hasFile('product_images')) {
                $imageUploadService = new ImageUploadService();
                $uploadedImages = $imageUploadService->uploadProductImages($request->file('product_images'), $product->id);
                if (!empty($uploadedImages)) {
                    $product->update(['images' => $uploadedImages]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product->load(['category', 'supplier'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'orderItems.order']);

        $stats = [
            'total_orders' => $product->orderItems()->count(),
            'total_quantity_sold' => $product->orderItems()->sum('quantity'),
            'total_revenue' => $product->orderItems()->sum('subtotal'),
            'average_order_value' => $product->orderItems()->avg('unit_price'),
            'current_stock' => $product->stock_quantity,
            'stock_value' => $product->stock_value
        ];

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                    'stats' => $stats
                ]
            ]);
        }

        return view('products.show', compact('product', 'stats'));
    }

    public function edit(Product $product)
    {
        $product->load(['supplier']);
        $categories = Category::active()->get();
        $suppliers = \App\Models\Supplier::all();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'base_price' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric|min:0',
            'dimensions.width' => 'nullable|numeric|min:0',
            'dimensions.height' => 'nullable|numeric|min:0',
            'is_customizable' => 'boolean',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'product_images' => 'nullable|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'images_to_remove' => 'nullable|array',
            'images_to_remove.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // If category changed and SKU is empty or auto-generated, regenerate SKU
            $updateData = [
                'name' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'weight' => $request->weight,
                'dimensions' => $request->dimensions,
                'is_customizable' => $request->boolean('is_customizable'),
            ];

            // Handle SKU update
            if ($request->filled('sku')) {
                $updateData['sku'] = $request->sku;
            } elseif ($product->category_id != $request->category_id) {
                // Category changed and no SKU provided, regenerate
                $updateData['sku'] = Product::generateSku($request->category_id);
            }

            $product->update($updateData);

            // Handle image operations
            $imageUploadService = new ImageUploadService();
            $currentImages = $product->images ?? [];

            // Remove specified images
            if ($request->has('images_to_remove')) {
                $imagesToRemove = $request->input('images_to_remove');
                $imageUploadService->deleteImages($imagesToRemove);
                $currentImages = array_diff($currentImages, $imagesToRemove);
            }

            // Add new images
            if ($request->hasFile('product_images')) {
                $uploadedImages = $imageUploadService->uploadProductImages($request->file('product_images'), $product->id);
                $currentImages = array_merge($currentImages, $uploadedImages);
            }

            // Update product with current images
            $product->update(['images' => array_values($currentImages)]);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product->load(['category', 'inventory'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            if ($product->orderItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete product with existing orders'
                ], 422);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicate(Product $product): JsonResponse
    {
        try {
            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Copy)';
            $newProduct->sku = $this->generateUniqueSku($product->sku);
            $newProduct->stock_quantity = 0; // Reset stock for duplicate
            $newProduct->save();

            return response()->json([
                'success' => true,
                'message' => 'Product duplicated successfully',
                'data' => $newProduct->load(['category', 'supplier'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'updates' => 'required|array',
            'updates.category_id' => 'nullable|exists:categories,id',
            'updates.base_price' => 'nullable|numeric|min:0',
            'updates.is_customizable' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = array_filter($request->input('updates'));
            $updatedCount = Product::whereIn('id', $request->input('product_ids'))
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} products",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update products: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVariants(Product $product): JsonResponse
    {
        if (!$product->is_customizable) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not customizable'
            ], 422);
        }

        $variants = $product->orderItems()
            ->whereNotNull('customization_details')
            ->select('customization_details')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return $item->customization_details;
            });

        return response()->json([
            'success' => true,
            'data' => $variants
        ]);
    }

    public function getData(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'inventory']);

        // Apply filters
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('customizable_only') && $request->customizable_only) {
            $query->customizable();
        }

        if ($request->has('stock_filter')) {
            switch ($request->stock_filter) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('stock_quantity', '<=', 'reorder_level');
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->input('sort_order', 'asc');

            if ($sortBy === 'stock') {
                $query->orderBy('stock_quantity', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Paginate results
        $products = $request->has('paginate') && $request->paginate
            ? $query->paginate($request->input('per_page', 15))
            : $query->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function getForOrder(Request $request): JsonResponse
    {
        $query = Product::with(['category'])
            ->where('stock_quantity', '>', 0);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->limit(50)->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'base_price' => $product->base_price,
                'available_stock' => $product->stock_quantity,
                'category' => $product->category->name ?? 'Uncategorized',
                'is_customizable' => $product->is_customizable
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $csvData = array_map('str_getcsv', file($path));
            $header = array_shift($csvData);

            $imported = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($csvData as $rowIndex => $row) {
                try {
                    if (count($row) !== count($header)) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Invalid column count";
                        continue;
                    }

                    $data = array_combine($header, $row);

                    // Basic validation
                    if (empty($data['sku']) || empty($data['name'])) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": SKU and Name are required";
                        continue;
                    }

                    // Check if SKU already exists
                    if (Product::where('sku', $data['sku'])->exists()) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": SKU '{$data['sku']}' already exists";
                        continue;
                    }

                    $product = Product::create([
                        'sku' => $data['sku'],
                        'name' => $data['name'],
                        'category_id' => $data['category_id'] ?? null,
                        'description' => $data['description'] ?? null,
                        'base_price' => $data['base_price'] ?? 0,
                        'weight' => $data['weight'] ?? null,
                        'is_customizable' => filter_var($data['is_customizable'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    ]);

                    // Create inventory if stock data provided
                    if (isset($data['initial_stock']) && $data['initial_stock'] > 0) {
                        Inventory::create([
                            'product_id' => $product->id,
                            'quantity_in_stock' => $data['initial_stock'],
                            'reorder_level' => $data['reorder_level'] ?? 10,
                            'cost_per_unit' => $data['cost_per_unit'] ?? $product->base_price
                        ]);
                    }

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Import completed. {$imported} products imported successfully.",
                'imported_count' => $imported,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $query = Product::with(['category', 'inventory']);

        // Apply same filters as index
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'SKU',
                'Name',
                'Category',
                'Description',
                'Base Price',
                'Weight',
                'Customizable',
                'Current Stock',
                'Reorder Level',
                'Cost per Unit',
                'Stock Value',
                'Created At'
            ]);

            // CSV data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->sku,
                    $product->name,
                    $product->category->name ?? 'Uncategorized',
                    $product->description,
                    $product->base_price,
                    $product->weight,
                    $product->is_customizable ? 'Yes' : 'No',
                    $product->inventory->quantity_in_stock ?? 0,
                    $product->inventory->reorder_level ?? 'N/A',
                    $product->inventory->cost_per_unit ?? 'N/A',
                    $product->inventory ?
                        ($product->inventory->quantity_in_stock * $product->inventory->cost_per_unit) : 0,
                    $product->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function previewSku(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id'
        ]);

        try {
            $sku = Product::generateSku($request->category_id);
            return response()->json([
                'success' => true,
                'sku' => $sku
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate SKU: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateUniqueSku(string $baseSku): string
    {
        $counter = 1;
        $newSku = $baseSku . '-COPY';

        while (Product::where('sku', $newSku)->exists()) {
            $newSku = $baseSku . '-COPY-' . $counter;
            $counter++;
        }

        return $newSku;
    }
}