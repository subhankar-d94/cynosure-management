<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children', 'products']);

        if ($request->has('parent_only')) {
            $query->parent();
        }

        if ($request->has('active_only')) {
            $query->active();
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $request->has('paginate')
            ? $query->paginate($request->input('per_page', 15))
            : $query->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        }

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::parent()->active()->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category->load(['parent', 'children'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Category $category)
    {
        $category->load(['parent', 'children.products', 'products']);

        $stats = [
            'total_products' => $category->products()->count(),
            'children_count' => $category->children()->count(),
            'active_products' => $category->products()->where('stock_quantity', '>', 0)->count()
        ];

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'stats' => $stats
                ]
            ]);
        }

        return view('categories.show', compact('category', 'stats'));
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::parent()
            ->active()
            ->where('id', '!=', $category->id)
            ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id|different:id',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->parent_id && $this->wouldCreateCircularReference($category, $request->parent_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot set parent category as it would create a circular reference'
            ], 422);
        }

        try {
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category->load(['parent', 'children'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            if ($category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with associated products'
                ], 422);
            }

            if ($category->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with subcategories'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHierarchy(): JsonResponse
    {
        $categories = Category::with(['children' => function($query) {
            $query->active()->orderBy('name');
        }])
        ->parent()
        ->active()
        ->orderBy('name')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function toggleStatus(Category $category): JsonResponse
    {
        try {
            $category->update([
                'is_active' => !$category->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully',
                'data' => [
                    'is_active' => $category->is_active
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function wouldCreateCircularReference(Category $category, $parentId): bool
    {
        $parent = Category::find($parentId);

        while ($parent) {
            if ($parent->id === $category->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }
}