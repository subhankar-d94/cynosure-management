@extends('layouts.catalog')

@section('title', 'Search Results - Product Catalog')

@section('content')
<div class="container">
    <!-- Search Results Section -->
    <div class="category-section">
        <!-- Search Header -->
        <div class="category-header">
            <h2 class="category-title">Search Results</h2>
            <p class="category-description">
                @if($products->total() > 0)
                    Found {{ $products->total() }} result{{ $products->total() !== 1 ? 's' : '' }} for "{{ $query }}"
                @else
                    No results found for "{{ $query }}"
                @endif
            </p>
        </div>

        <!-- Search Options -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" action="{{ route('catalog.search') }}" class="d-flex gap-2">
                    <input type="text" class="form-control" name="q"
                           placeholder="Search products..."
                           value="{{ $query }}">
                    <button class="btn btn-catalog" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
            <div class="col-md-6 text-end mt-2 mt-md-0">
                @if($products->total() > 0)
                <small class="text-muted">Showing {{ $products->count() }} of {{ $products->total() }} results</small>
                @endif
            </div>
        </div>

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
                        <span class="product-badge" style="background: var(--secondary-color);">{{ $product->category->name }}</span>
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
                            <a href="{{ route('catalog.product', [$product->category->slug, $product->id]) }}" class="text-decoration-none">
                                <i class="bi bi-eye me-1"></i>View Details & Images
                            </a>
                        </small>

                        <small class="text-muted">
                            <a href="https://wa.me/918250346616?text=Hi, I'm interested in {{ urlencode($product->name) }} (SKU: {{ $product->sku }}) from your catalog."
                               class="text-decoration-none" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i>Inquire
                            </a>
                        </small>

                        <small class="text-muted">
                            <a href="{{ route('catalog.category', $product->category->slug) }}" class="text-decoration-none">
                                <i class="bi bi-grid me-1"></i>View {{ $product->category->name }}
                            </a>
                        </small>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="text-center mt-4">
                {{ $products->appends(['q' => $query])->links() }}
            </div>
            @endif

        @else
            <div class="text-center py-4">
                <div class="contact-info">
                    <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Results Found</h4>
                    <p class="text-muted mb-4">
                        We couldn't find any products matching "{{ $query }}".
                    </p>

                    <!-- Search Suggestions -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Search Tips:</h6>
                        <ul class="list-unstyled text-muted small">
                            <li>• Check your spelling</li>
                            <li>• Try more general keywords</li>
                            <li>• Use different words or synonyms</li>
                        </ul>
                    </div>

                    <!-- Browse Categories -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Browse Our Categories:</h6>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            @foreach($allCategories->take(6) as $category)
                            <a href="{{ route('catalog.category', $category->slug) }}" class="btn btn-outline-secondary btn-sm">
                                {{ $category->name }} ({{ $category->products_count }})
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('catalog.index') }}" class="btn btn-catalog">
                            <i class="bi bi-house me-1"></i>View Full Catalog
                        </a>
                        <a href="mailto:deydebiparna297@gmail.com?subject=Product Inquiry&body=Hi, I was looking for: {{ urlencode($query) }}. Can you help me find what I need?"
                           class="btn btn-outline-primary">
                            <i class="bi bi-envelope me-1"></i>Contact Us
                        </a>
                    </div>
                </div>
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