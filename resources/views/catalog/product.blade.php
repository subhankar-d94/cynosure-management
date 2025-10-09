@extends('layouts.catalog')

@section('title', $product->name . ' - Product Catalog')

@section('content')
<div class="container">
    <!-- Product Details Section -->
    <div class="category-section">
        <!-- Product Header -->
        <div class="category-header">
            <h2 class="category-title">{{ $product->name }}</h2>
            <p class="category-description">{{ $product->category->name }}</p>
        </div>

        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6 mb-4">
                @if($product->hasImages())
                <div class="product-gallery">
                    <!-- Main Image -->
                    <div class="main-image mb-3">
                        <img id="mainImage" src="{{ Storage::url($product->first_image) }}"
                             alt="{{ $product->name }}" class="img-fluid rounded"
                             style="width: 100%; height: 400px; object-fit: cover; cursor: pointer; border: 1px solid var(--border-color);"
                             onclick="openImageModal(this.src)">
                    </div>

                    <!-- Thumbnail Images -->
                    @if(count($product->images) > 1)
                    <div class="row g-2">
                        @foreach($product->images as $index => $imagePath)
                        <div class="col-3">
                            <img src="{{ Storage::url($imagePath) }}" alt="{{ $product->name }}"
                                 class="img-thumbnail thumbnail-image"
                                 style="width: 100%; height: 80px; object-fit: cover; cursor: pointer;"
                                 onclick="changeMainImage(this.src)">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @else
                <div class="text-center py-5 bg-light rounded">
                    <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-2">No images available</p>
                </div>
                @endif
            </div>

            <!-- Product Information -->
            <div class="col-lg-6">
                <div class="product-details-section">
                    <!-- Basic Info -->
                    <div class="mb-4">
                        <h3 class="product-price">₹{{ number_format($product->base_price, 2) }}</h3>
                        <p class="text-muted mb-2">SKU: {{ $product->sku }}</p>
                        @if($product->is_customizable)
                        <span class="product-badge">Customizable Product</span>
                        @endif
                    </div>

                    <!-- Description -->
                    @if($product->description)
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>
                    @endif

                    <!-- Specifications -->
                    <div class="mb-4">
                        <h5>Specifications</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-semibold text-muted" style="width: 30%;">Category:</td>
                                <td>{{ $product->category->name }}</td>
                            </tr>
                            @if($product->weight)
                            <tr>
                                <td class="fw-semibold text-muted">Weight:</td>
                                <td>{{ $product->weight }} kg</td>
                            </tr>
                            @endif
                            @if($product->dimensions && isset($product->dimensions['length']))
                            <tr>
                                <td class="fw-semibold text-muted">Dimensions:</td>
                                <td>{{ $product->dimensions['length'] }} × {{ $product->dimensions['width'] ?? 'N/A' }} × {{ $product->dimensions['height'] ?? 'N/A' }} cm</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-semibold text-muted">Customizable:</td>
                                <td>
                                    @if($product->is_customizable)
                                        <span class="text-success"><i class="bi bi-check-circle me-1"></i>Yes</span>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Contact for Order -->
                    <div class="contact-info">
                        <h5 class="mb-3">Interested in this product?</h5>
                        <p class="text-muted mb-3">
                            Contact us for pricing, availability, customization options, and to place your order.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="https://wa.me/918250346616?text=Hi, I'm interested in {{ urlencode($product->name) }} (SKU: {{ $product->sku }}) from your catalog. Can you provide more details?"
                               class="btn btn-catalog" target="_blank">
                                <i class="bi bi-whatsapp me-2"></i>WhatsApp Inquiry
                            </a>
                            <div class="row">
                                <div class="col-6">
                                    <a href="tel:+918250346616" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-telephone me-1"></i>Call
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="mailto:deydebiparna297@gmail.com?subject=Inquiry about {{ urlencode($product->name) }}&body=Hi, I'm interested in {{ urlencode($product->name) }} (SKU: {{ $product->sku }}). Please provide more details."
                                       class="btn btn-outline-primary w-100">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-5">
            <h4 class="mb-4">More from {{ $product->category->name }}</h4>

            @foreach($relatedProducts as $relatedProduct)
            <div class="product-item">
                <div class="product-image" style="width: 100px; height: 100px;">
                    @if($relatedProduct->hasImages())
                        <img src="{{ Storage::url($relatedProduct->first_image) }}" alt="{{ $relatedProduct->name }}">
                    @else
                        <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                    @endif
                </div>

                <div class="product-content">
                    <h5 class="product-name">{{ $relatedProduct->name }}</h5>
                    @if($relatedProduct->description)
                    <p class="product-description">{{ Str::limit($relatedProduct->description, 120) }}</p>
                    @endif
                    <div class="product-details">
                        <span class="product-price">₹{{ number_format($relatedProduct->base_price, 2) }}</span>
                        <span class="product-sku">SKU: {{ $relatedProduct->sku }}</span>
                    </div>
                    <div class="product-meta">
                        <small class="text-muted">
                            <a href="{{ route('catalog.product', [$product->category->slug, $relatedProduct->id]) }}" class="text-decoration-none">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="{{ $product->name }}">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@php
use Illuminate\Support\Facades\Storage;
@endphp

<style>
.thumbnail-image {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.thumbnail-image:hover {
    border-color: var(--accent-color);
}

.product-details-section h5 {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.main-image img {
    transition: all 0.3s ease;
}

.main-image img:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .main-image {
        margin-bottom: 1rem;
    }

    .main-image img {
        height: 250px !important;
    }

    .thumbnail-image {
        height: 60px !important;
    }

    .product-details-section .table td {
        padding: 0.5rem 0.25rem;
        font-size: 0.85rem;
    }

    .product-details-section h5 {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }

    .contact-info .d-grid .btn {
        padding: 1rem;
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }

    .contact-info .row .btn {
        padding: 0.75rem 0.5rem;
        font-size: 0.85rem;
    }

    .product-gallery .row.g-2 {
        gap: 0.5rem !important;
    }

    .product-gallery .col-3 {
        flex: 0 0 calc(25% - 0.375rem);
    }
}

@media (max-width: 480px) {
    .main-image img {
        height: 200px !important;
    }

    .product-gallery .col-3 {
        flex: 0 0 calc(50% - 0.25rem);
    }

    .thumbnail-image {
        height: 50px !important;
    }

    .contact-info .row {
        flex-direction: column;
    }

    .contact-info .row .col-6 {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;

    // Update active thumbnail
    document.querySelectorAll('.thumbnail-image').forEach(img => {
        if (img.src === src) {
            img.style.borderColor = 'var(--accent-color)';
        } else {
            img.style.borderColor = 'transparent';
        }
    });
}

function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Set first thumbnail as active on load
document.addEventListener('DOMContentLoaded', function() {
    const firstThumbnail = document.querySelector('.thumbnail-image');
    if (firstThumbnail) {
        firstThumbnail.style.borderColor = 'var(--accent-color)';
    }
});
</script>
@endpush