<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Get all active categories with product counts
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function ($query) {
                // Only count products that have images (ready for catalog)
                $query->whereNotNull('images')
                      ->where('images', '!=', '[]');
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Get featured/recent products for homepage
        $featuredProducts = Product::whereNotNull('images')
            ->where('images', '!=', '[]')
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('catalog.index', compact('categories', 'featuredProducts'));
    }

    public function category(Request $request, $categorySlug)
    {
        // Find category by slug
        $category = Category::where('is_active', true)
            ->where(function($query) use ($categorySlug) {
                $query->where('slug', $categorySlug)
                      ->orWhere('name', str_replace('-', ' ', $categorySlug));
            })
            ->firstOrFail();

        // Get products for this category
        $productsQuery = Product::where('category_id', $category->id)
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->with('category');

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $productsQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Sort options
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');

        switch ($sortBy) {
            case 'price_low':
                $productsQuery->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('base_price', 'desc');
                break;
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            default:
                $productsQuery->orderBy('name', 'asc');
        }

        $products = $productsQuery->paginate(12);

        // Get all categories for navigation
        $allCategories = Category::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->whereNotNull('images')
                      ->where('images', '!=', '[]');
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('catalog.category', compact('category', 'products', 'allCategories'));
    }

    public function product($categorySlug, $productId)
    {
        $product = Product::with('category')
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->findOrFail($productId);

        // Get related products from same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->limit(4)
            ->get();

        return view('catalog.product', compact('product', 'relatedProducts'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return redirect()->route('catalog.index');
        }

        $products = Product::whereNotNull('images')
            ->where('images', '!=', '[]')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->with('category')
            ->orderBy('name')
            ->paginate(12);

        // Get all categories for navigation
        $allCategories = Category::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->whereNotNull('images')
                      ->where('images', '!=', '[]');
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('catalog.search', compact('products', 'query', 'allCategories'));
    }

    public function downloadCatalog()
    {
        // This could generate a PDF catalog
        // For now, we'll return a simple response
        return response()->json([
            'message' => 'PDF catalog generation feature coming soon',
            'contact' => 'Please contact us for a complete catalog'
        ]);
    }
}