@extends('layouts.catalog')

@section('title', 'Product Catalog')

@section('content')
<div class="container">
    @if($categories->count() > 0)
        @foreach($categories as $category)
        <div class="category-section">
            <!-- Category Header -->
            <div class="category-header">
                <h2 class="category-title">{{ $category->name }}</h2>
                @if($category->description)
                <p class="category-description">{{ $category->description }}</p>
                @endif
                <small class="text-muted">{{ $category->products_count }} item{{ $category->products_count !== 1 ? 's' : '' }}</small>
            </div>

            <!-- Category Products -->
            @php
                $categoryProducts = \App\Models\Product::where('category_id', $category->id)
                    ->whereNotNull('images')
                    ->where('images', '!=', '[]')
                    ->with('category')
                    ->orderBy('name')
                    ->get();
            @endphp

            @if($categoryProducts->count() > 0)
                @foreach($categoryProducts as $product)
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
                                    <i class="bi bi-eye me-1"></i>View Details
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <p class="text-muted">No products available in this category.</p>
                </div>
            @endif
        </div>
        @endforeach
    @else
        <div class="text-center py-5">
            <div class="contact-info">
                <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-3 text-muted">Catalog Coming Soon</h3>
                <p class="text-muted mb-4">
                    We're preparing our product catalog. Please contact us for current product availability.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="tel:+918250346616" class="btn btn-catalog">
                        <i class="bi bi-telephone me-2"></i>Call Us
                    </a>
                    <a href="https://wa.me/918250346616" class="btn btn-catalog" target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>WhatsApp
                    </a>
                    <a href="mailto:deydebiparna297@gmail.com" class="btn btn-catalog">
                        <i class="bi bi-envelope me-2"></i>Email Us
                    </a>
                </div>
            </div>
        </div>
    @endif
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
}
</style>
@endpush