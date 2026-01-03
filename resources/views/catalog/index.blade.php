@extends('layouts.catalog')

@section('title', 'Product Catalog')

@section('content')
<!-- Hero Banner -->
<div class="catalog-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Our Product Catalog</h1>
            <p class="hero-subtitle">Discover our complete range of quality products</p>
        </div>
    </div>
</div>

<div class="container">
    @if($categories->count() > 0)
        <!-- Category Quick Navigation -->
        <div class="category-quick-nav">
            <h4 class="quick-nav-title"><i class="bi bi-grid-3x3-gap"></i> Browse by Category</h4>
            <div class="category-grid-nav">
                @foreach($categories as $category)
                <a href="#category-{{ $category->slug }}" class="category-nav-card">
                    <div class="category-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h6 class="category-nav-name">{{ $category->name }}</h6>
                    <span class="category-nav-count">{{ $category->products_count }} items</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Products by Category -->
        @foreach($categories as $category)
        @php
            $categoryProducts = \App\Models\Product::where('category_id', $category->id)
                ->whereNotNull('images')
                ->where('images', '!=', '[]')
                ->with('category')
                ->orderBy('name')
                ->get();
        @endphp

        @if($categoryProducts->count() > 0)
        <div class="category-section" id="category-{{ $category->slug }}">
            <!-- Category Header -->
            <div class="category-header-modern">
                <div class="category-header-left">
                    <div class="category-icon-large">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h2 class="category-title-modern">{{ $category->name }}</h2>
                        @if($category->description)
                        <p class="category-description-modern">{{ $category->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="category-meta">
                    <span class="product-count-badge">
                        <i class="bi bi-tag"></i> {{ $categoryProducts->count() }} Products
                    </span>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="product-grid">
                @foreach($categoryProducts as $product)
                <div class="product-card">
                    <div class="product-card-image">
                        @if($product->hasImages())
                            <img src="{{ Storage::url($product->first_image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-image-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif

                        @if($product->is_customizable)
                        <span class="product-badge-custom">
                            <i class="bi bi-star-fill"></i> Customizable
                        </span>
                        @endif
                    </div>

                    <div class="product-card-body">
                        <div class="product-category-tag">
                            {{ $category->name }}
                        </div>

                        <h3 class="product-card-title">{{ $product->name }}</h3>

                        @if($product->description)
                        <p class="product-card-description">
                            {{ Str::limit($product->description, 120) }}
                        </p>
                        @endif

                        <div class="product-card-info">
                            @if($product->weight)
                            <div class="info-item">
                                <i class="bi bi-box"></i>
                                <span>{{ $product->weight }} kg</span>
                            </div>
                            @endif

                            @if($product->dimensions && isset($product->dimensions['length']))
                            <div class="info-item">
                                <i class="bi bi-rulers"></i>
                                <span>{{ $product->dimensions['length'] }}×{{ $product->dimensions['width'] ?? 'N/A' }}×{{ $product->dimensions['height'] ?? 'N/A' }} cm</span>
                            </div>
                            @endif

                            <div class="info-item">
                                <i class="bi bi-upc-scan"></i>
                                <span>{{ $product->sku }}</span>
                            </div>
                        </div>

                        <div class="product-card-footer">
                            <div class="product-price-tag">
                                ₹{{ number_format($product->base_price, 2) }}
                            </div>
                            <a href="{{ route('catalog.product', [$category->slug, $product->id]) }}" class="btn-view-details">
                                View Details <i class="bi bi-arrow-right"></i>
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
        <div class="empty-catalog">
            <div class="empty-catalog-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <h3 class="empty-catalog-title">Catalog Coming Soon</h3>
            <p class="empty-catalog-text">
                We're preparing our product catalog. Please contact us for current product availability and pricing.
            </p>
            <div class="contact-buttons">
                <a href="tel:+918250346616" class="contact-btn contact-btn-phone">
                    <i class="bi bi-telephone-fill"></i>
                    <span>Call Us</span>
                </a>
                <a href="https://wa.me/918250346616" class="contact-btn contact-btn-whatsapp" target="_blank">
                    <i class="bi bi-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
                <a href="mailto:deydebiparna297@gmail.com" class="contact-btn contact-btn-email">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Email Us</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Back to Top Button -->
    <div class="back-to-top" id="backToTop">
        <i class="bi bi-arrow-up"></i>
    </div>
</div>
@endsection

@push('styles')
@php
use Illuminate\Support\Facades\Storage;
@endphp

<style>
/* Hero Banner */
.catalog-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 3rem 0;
    margin-bottom: 3rem;
    border-radius: 0 0 24px 24px;
}

.hero-content {
    text-align: center;
    color: white;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

/* Category Quick Navigation */
.category-quick-nav {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 3rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.quick-nav-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.category-grid-nav {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
}

.category-nav-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.category-nav-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    border-color: #667eea;
    background: white;
}

.category-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
}

.category-nav-name {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.category-nav-count {
    font-size: 0.85rem;
    color: #6c757d;
}

/* Category Section */
.category-section {
    margin-bottom: 4rem;
}

.category-header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #f8f9fa 0%, white 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    border-left: 6px solid #667eea;
}

.category-header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.category-icon-large {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    flex-shrink: 0;
}

.category-title-modern {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.category-description-modern {
    font-size: 1rem;
    color: #6c757d;
    margin: 0;
}

.product-count-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-size: 0.95rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* Product Grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
}

/* Product Card */
.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
}

.product-card-image {
    position: relative;
    height: 280px;
    background: #f8f9fa;
    overflow: hidden;
}

.product-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.product-card:hover .product-card-image img {
    transform: scale(1.1);
}

.product-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: #dee2e6;
}

.product-badge-custom {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.product-card-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-category-tag {
    display: inline-block;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
    width: fit-content;
}

.product-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.75rem;
    line-height: 1.3;
}

.product-card-description {
    font-size: 0.9rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 1rem;
    flex: 1;
}

.product-card-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.info-item i {
    color: #667eea;
    font-size: 1rem;
}

.product-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 1rem;
    border-top: 2px solid #f8f9fa;
}

.product-price-tag {
    font-size: 1.75rem;
    font-weight: 700;
    color: #667eea;
}

.btn-view-details {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-view-details:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    color: white;
}

/* Empty State */
.empty-catalog {
    text-align: center;
    padding: 5rem 2rem;
}

.empty-catalog-icon {
    font-size: 6rem;
    color: #dee2e6;
    margin-bottom: 2rem;
}

.empty-catalog-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.empty-catalog-text {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.contact-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.contact-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.contact-btn-phone {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.contact-btn-whatsapp {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
}

.contact-btn-email {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

/* Back to Top */
.back-to-top {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
    z-index: 1000;
}

.back-to-top.visible {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .catalog-hero {
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .hero-title {
        font-size: 1.75rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .category-grid-nav {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
    }

    .category-nav-card {
        padding: 1rem;
    }

    .category-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }

    .category-header-modern {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.5rem;
    }

    .category-header-left {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .category-icon-large {
        width: 60px;
        height: 60px;
        font-size: 1.75rem;
    }

    .category-title-modern {
        font-size: 1.5rem;
    }

    .product-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .product-card-image {
        height: 220px;
    }

    .contact-buttons {
        flex-direction: column;
    }

    .contact-btn {
        width: 100%;
        justify-content: center;
    }
}

@media print {
    .catalog-hero,
    .category-quick-nav,
    .back-to-top,
    .btn-view-details {
        display: none !important;
    }

    .product-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .product-card {
        page-break-inside: avoid;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Back to Top functionality
window.addEventListener('scroll', function() {
    const backToTop = document.getElementById('backToTop');
    if (window.pageYOffset > 300) {
        backToTop.classList.add('visible');
    } else {
        backToTop.classList.remove('visible');
    }
});

document.getElementById('backToTop').addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Smooth scroll for category navigation
document.querySelectorAll('.category-nav-card').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endpush
