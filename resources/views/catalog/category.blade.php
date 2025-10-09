@extends('layouts.catalog')

@section('title', $category->name . ' - Product Catalog')

@section('content')
<div class="container">
    <!-- Category Section -->
    <div class="category-section">
        <!-- Category Header -->
        <div class="category-header">
            <h2 class="category-title">{{ $category->name }}</h2>
            @if($category->description)
            <p class="category-description">{{ $category->description }}</p>
            @endif
            <small class="text-muted">{{ $products->total() }} item{{ $products->total() !== 1 ? 's' : '' }}</small>
        </div>

        <!-- Search and Sort Options -->
        @if($products->total() > 0)
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" class="form-control" name="search"
                           placeholder="Search in {{ $category->name }}..."
                           value="{{ request('search') }}">
                    <button class="btn btn-catalog" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                    <a href="{{ route('catalog.category', $category->slug) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end mt-2 mt-md-0">
                <form method="GET" class="d-inline">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="sort" class="form-select w-auto d-inline" onchange="this.form.submit()">
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                    </select>
                </form>
            </div>
        </div>
        @endif

        <!-- Products -->
        @if($products->count() > 0)
            @foreach($products as $product)
            <div class="product-item">
                <div class="product-image">
                    @if($product->hasImages())
                        <img src="{{ Storage::url($product->first_image) }}" alt="{{ $product->name }}">
                    @else
                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                    @endif
                </div>

                <div class="product-content">
                    <h3 class="product-name">{{ $product->name }}</h3>

                    @if($product->description)
                    <p class="product-description">{{ $product->description }}</p>
                    @endif

                    <div class="product-details">
                        <span class="product-price">₹{{ number_format($product->base_price, 2) }}</span>
                        <span class="product-sku">SKU: {{ $product->sku }}</span>
                        @if($product->is_customizable)
                        <span class="product-badge">Customizable</span>
                        @endif
                    </div>

                    <!-- Additional Product Info -->
                    <div class="product-meta">
                        @if($product->weight)
                        <small class="text-muted me-3">
                            <i class="bi bi-box me-1"></i>Weight: {{ $product->weight }} kg
                        </small>
                        @endif

                        @if($product->dimensions && isset($product->dimensions['length']))
                        <small class="text-muted me-3">
                            <i class="bi bi-rulers me-1"></i>
                            {{ $product->dimensions['length'] }} × {{ $product->dimensions['width'] ?? 'N/A' }} × {{ $product->dimensions['height'] ?? 'N/A' }} cm
                        </small>
                        @endif

                        <small class="text-muted">
                            <a href="{{ route('catalog.product', [$category->slug, $product->id]) }}" class="text-decoration-none">
                                <i class="bi bi-eye me-1"></i>View Details & Images
                            </a>
                        </small>

                        <small class="text-muted">
                            <a href="https://wa.me/918250346616?text=Hi, I'm interested in {{ urlencode($product->name) }} (SKU: {{ $product->sku }}) from your catalog."
                               class="text-decoration-none" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i>Inquire
                            </a>
                        </small>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="text-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif

        @else
            <div class="text-center py-4">
                @if(request('search'))
                <div class="contact-info">
                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-muted">No Results Found</h4>
                    <p class="text-muted mb-3">
                        No products found for "{{ request('search') }}" in {{ $category->name }}.
                    </p>
                    <a href="{{ route('catalog.category', $category->slug) }}" class="btn btn-catalog">
                        View All Products in {{ $category->name }}
                    </a>
                </div>
                @else
                <p class="text-muted">No products available in this category.</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
@php
use Illuminate\Support\Facades\Storage;
@endphp

<style>
.product-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}

.product-meta small {
    display: flex;
    align-items: center;
}

@media (max-width: 768px) {
    .product-meta {
        flex-direction: column;
        gap: 0.5rem;
    }

    .row > .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endpush