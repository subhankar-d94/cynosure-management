@extends('layouts.catalog')

@section('title', 'Product Catalog')

@section('content')
<!-- Hero Banner -->
<div class="catalog-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Product Catalog</h1>
            <p class="hero-subtitle">Browse our complete collection of quality products</p>
        </div>
    </div>
</div>

<div class="container catalog-container">
    @if($categories->count() > 0)
        <!-- Category Navigation Tabs -->
        <div class="category-tabs-wrapper">
            <div class="category-tabs">
                @foreach($categories as $index => $category)
                <a href="#category-{{ $category->slug }}"
                   class="category-tab {{ $index === 0 ? 'active' : '' }}"
                   data-category="{{ $category->slug }}">
                    <span class="tab-name">{{ $category->name }}</span>
                    <span class="tab-count">{{ $category->products_count }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Products by Category -->
        @foreach($categories as $category)
        @php
            $categoryProducts = \App\Models\Product::where('category_id', $category->id)
                ->with(['category', 'inventory'])
                ->orderBy('name')
                ->get();
        @endphp

        @if($categoryProducts->count() > 0)
        <div class="category-section" id="category-{{ $category->slug }}">
            <!-- Category Header -->
            <div class="section-header">
                <div class="section-header-content">
                    <h2 class="section-title">{{ $category->name }}</h2>
                    @if($category->description)
                    <p class="section-description">{{ $category->description }}</p>
                    @endif
                </div>
                <div class="section-badge">
                    {{ $categoryProducts->count() }} Product{{ $categoryProducts->count() !== 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Product Grid -->
            <div class="products-grid">
                @foreach($categoryProducts as $product)
                @php
                    $inventory = $product->inventory;
                    $stockQty = $inventory ? $inventory->quantity_in_stock : 0;
                    $isInStock = $stockQty > 0;
                @endphp

                <div class="product-card {{ !$isInStock ? 'out-of-stock' : '' }}">
                    <!-- Product Image -->
                    <div class="product-image-container">
                        @if($product->hasImages())
                            <img src="{{ Storage::url($product->first_image) }}"
                                 alt="{{ $product->name }}"
                                 class="product-image"
                                 loading="lazy">
                        @else
                            <div class="product-image-placeholder">
                                <i class="bi bi-image"></i>
                                <span>No Image</span>
                            </div>
                        @endif

                        <!-- Stock Badge -->
                        <div class="stock-badge {{ $isInStock ? 'in-stock' : 'out-stock' }}">
                            <i class="bi bi-{{ $isInStock ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                            <span>{{ $isInStock ? 'In Stock' : 'Out of Stock' }}</span>
                        </div>

                        <!-- Customizable Badge -->
                        @if($product->is_customizable)
                        <div class="custom-badge">
                            <i class="bi bi-star-fill"></i>
                            <span>Custom</span>
                        </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="product-info">
                        <div class="product-category">{{ $category->name }}</div>
                        <h3 class="product-title">{{ $product->name }}</h3>

                        @if($product->description)
                        <p class="product-description">{{ Str::limit($product->description, 100) }}</p>
                        @endif

                        <!-- Product Meta -->
                        <div class="product-meta">
                            <div class="meta-item">
                                <i class="bi bi-upc-scan"></i>
                                <span>{{ $product->sku }}</span>
                            </div>
                            @if($product->weight)
                            <div class="meta-item">
                                <i class="bi bi-box"></i>
                                <span>{{ $product->weight }} kg</span>
                            </div>
                            @endif
                        </div>

                        <!-- Price and Action -->
                        <div class="product-footer">
                            <div class="product-price">
                                <span class="price-label">Price</span>
                                <span class="price-value">₹{{ number_format($product->base_price, 2) }}</span>
                            </div>
                            <a href="{{ route('catalog.product', [$category->slug, $product->id]) }}"
                               class="btn-details">
                                <span>Details</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h3 class="empty-title">No Products Available</h3>
            <p class="empty-text">Our catalog is being updated. Please check back soon or contact us for more information.</p>
            <div class="contact-actions">
                <a href="tel:+918250346616" class="contact-btn phone">
                    <i class="bi bi-telephone-fill"></i>
                    <span>Call Us</span>
                </a>
                <a href="https://wa.me/918250346616" class="contact-btn whatsapp" target="_blank">
                    <i class="bi bi-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
                <a href="mailto:deydebiparna297@gmail.com" class="contact-btn email">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Email</span>
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Scroll to Top Button -->
<button class="scroll-top" id="scrollTop" aria-label="Scroll to top">
    <i class="bi bi-arrow-up"></i>
</button>
@endsection

@push('styles')
@php
use Illuminate\Support\Facades\Storage;
@endphp

<style>
/* CSS Variables */
:root {
    --primary: #667eea;
    --primary-dark: #5568d3;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --dark: #1f2937;
    --gray: #6b7280;
    --light-gray: #f3f4f6;
    --border: #e5e7eb;
    --white: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --radius-sm: 8px;
    --radius: 12px;
    --radius-lg: 16px;
}

/* Hero Banner */
.catalog-hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: 2.5rem 0;
    margin-bottom: 2rem;
}

.hero-content {
    text-align: center;
}

.hero-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--white);
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.5px;
}

.hero-subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-weight: 400;
}

/* Container */
.catalog-container {
    padding: 0 1rem 3rem;
}

/* Category Tabs */
.category-tabs-wrapper {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1rem;
    margin-bottom: 2.5rem;
    box-shadow: var(--shadow);
    position: sticky;
    top: 80px;
    z-index: 100;
}

.category-tabs {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    padding-bottom: 0.5rem;
}

.category-tabs::-webkit-scrollbar {
    height: 6px;
}

.category-tabs::-webkit-scrollbar-track {
    background: var(--light-gray);
    border-radius: 10px;
}

.category-tabs::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

.category-tab {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: var(--light-gray);
    border-radius: var(--radius);
    text-decoration: none;
    color: var(--dark);
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.category-tab:hover {
    background: var(--primary);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.category-tab.active {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary-dark);
}

.tab-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
}

.category-tab.active .tab-count {
    background: rgba(255, 255, 255, 0.3);
}

/* Section Header */
.category-section {
    margin-bottom: 3rem;
    scroll-margin-top: 150px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid var(--light-gray);
}

.section-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0 0 0.25rem 0;
}

.section-description {
    font-size: 1rem;
    color: var(--gray);
    margin: 0;
}

.section-badge {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    white-space: nowrap;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

/* Product Card */
.product-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    position: relative;
}

.product-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

.product-card.out-of-stock {
    opacity: 0.85;
}

/* Product Image */
.product-image-container {
    position: relative;
    height: 240px;
    background: var(--light-gray);
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: var(--gray);
}

.product-image-placeholder i {
    font-size: 3rem;
}

.product-image-placeholder span {
    font-size: 0.9rem;
    font-weight: 500;
}

/* Badges */
.stock-badge {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.85rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    box-shadow: var(--shadow);
}

.stock-badge.in-stock {
    background: rgba(16, 185, 129, 0.95);
    color: var(--white);
}

.stock-badge.out-stock {
    background: rgba(239, 68, 68, 0.95);
    color: var(--white);
}

.custom-badge {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.85rem;
    background: rgba(245, 158, 11, 0.95);
    color: var(--white);
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    box-shadow: var(--shadow);
}

/* Product Info */
.product-info {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-category {
    display: inline-block;
    background: rgba(102, 126, 234, 0.1);
    color: var(--primary);
    padding: 0.35rem 0.75rem;
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
    width: fit-content;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-description {
    font-size: 0.9rem;
    color: var(--gray);
    line-height: 1.5;
    margin: 0 0 1rem 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: var(--light-gray);
    border-radius: var(--radius-sm);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.85rem;
    color: var(--gray);
}

.meta-item i {
    color: var(--primary);
    font-size: 1rem;
}

/* Product Footer */
.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 1rem;
    border-top: 2px solid var(--light-gray);
}

.product-price {
    display: flex;
    flex-direction: column;
}

.price-label {
    font-size: 0.75rem;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.price-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1;
}

.btn-details {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.btn-details:hover {
    transform: translateX(3px);
    box-shadow: var(--shadow);
    color: var(--white);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 5rem;
    color: var(--border);
    margin-bottom: 1.5rem;
}

.empty-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0 0 0.5rem 0;
}

.empty-text {
    font-size: 1rem;
    color: var(--gray);
    margin: 0 0 2rem 0;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.contact-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.75rem;
    border-radius: var(--radius);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
    color: var(--white);
}

.contact-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--white);
}

.contact-btn.phone {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.contact-btn.whatsapp {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
}

.contact-btn.email {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

/* Scroll to Top */
.scroll-top {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-lg);
    z-index: 1000;
    font-size: 1.25rem;
}

.scroll-top.visible {
    opacity: 1;
    visibility: visible;
}

.scroll-top:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.2);
}

/* Tablet Responsive */
@media (max-width: 1024px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.25rem;
    }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 1.75rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .catalog-container {
        padding: 0 0.75rem 2rem;
    }

    .category-tabs-wrapper {
        position: static;
        padding: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .category-tab {
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
    }

    .section-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .section-title {
        font-size: 1.5rem;
    }

    .section-badge {
        align-self: flex-start;
    }

    .products-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    .product-image-container {
        height: 200px;
    }

    .product-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .product-price {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }

    .price-value {
        font-size: 1.35rem;
    }

    .btn-details {
        justify-content: center;
        width: 100%;
    }

    .scroll-top {
        bottom: 1.5rem;
        right: 1.5rem;
        width: 45px;
        height: 45px;
    }

    .contact-actions {
        flex-direction: column;
        width: 100%;
        max-width: 320px;
        margin: 0 auto;
    }

    .contact-btn {
        width: 100%;
        justify-content: center;
    }
}

/* Small Mobile */
@media (max-width: 480px) {
    .catalog-hero {
        padding: 2rem 0;
    }

    .hero-title {
        font-size: 1.5rem;
    }

    .hero-subtitle {
        font-size: 0.9rem;
    }

    .category-tab {
        padding: 0.6rem 0.85rem;
        font-size: 0.85rem;
    }

    .section-title {
        font-size: 1.25rem;
    }

    .product-image-container {
        height: 180px;
    }

    .product-title {
        font-size: 1rem;
    }

    .stock-badge,
    .custom-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.7rem;
    }
}

/* Print Styles */
@media print {
    .catalog-hero,
    .category-tabs-wrapper,
    .scroll-top,
    .btn-details {
        display: none !important;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .product-card {
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid var(--border);
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to top button
    const scrollTop = document.getElementById('scrollTop');

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollTop.classList.add('visible');
        } else {
            scrollTop.classList.remove('visible');
        }
    });

    scrollTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Category tabs smooth scroll
    const categoryTabs = document.querySelectorAll('.category-tab');

    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();

            // Update active state
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Smooth scroll to category
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                const offset = 160; // Account for sticky header
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Update active tab on scroll
    const categorySection = document.querySelectorAll('.category-section');

    window.addEventListener('scroll', function() {
        let current = '';

        categorySection.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;

            if (window.pageYOffset >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });

        categoryTabs.forEach(tab => {
            tab.classList.remove('active');
            if (tab.getAttribute('href') === '#' + current) {
                tab.classList.add('active');
            }
        });
    });
});
</script>
@endpush
