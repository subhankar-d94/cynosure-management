<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Product Catalog') - {{ config('app.name', 'Cynosure Titli Management') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --text-color: #2c3e50;
            --border-color: #ecf0f1;
            --light-bg: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: white;
            color: var(--text-color);
            line-height: 1.6;
        }

        .catalog-header {
            background: white;
            border-bottom: 2px solid var(--border-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .catalog-title {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-color);
            margin: 0;
        }

        .catalog-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }

        .category-section {
            margin-bottom: 3rem;
            page-break-inside: avoid;
        }

        .category-header {
            background: var(--light-bg);
            padding: 1rem 1.5rem;
            border-left: 4px solid var(--accent-color);
            margin-bottom: 1.5rem;
        }

        .category-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .category-description {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0.25rem 0 0 0;
        }

        .product-item {
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 0;
            display: flex;
            gap: 1.5rem;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            background: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid var(--border-color);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-content {
            flex: 1;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .product-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }

        .product-details {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-sku {
            font-size: 0.85rem;
            color: #6c757d;
            background: var(--light-bg);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .product-badge {
            font-size: 0.8rem;
            background: var(--accent-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
        }

        .contact-info {
            background: var(--light-bg);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            margin: 2rem 0;
        }

        .search-box {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.5rem 1rem;
            width: 300px;
        }

        .search-box:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-catalog {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-catalog:hover {
            background: #2980b9;
            color: white;
        }

        .nav-categories {
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .category-nav-item {
            display: inline-block;
            margin-right: 2rem;
            margin-bottom: 0.5rem;
        }

        .category-nav-link {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .category-nav-link:hover,
        .category-nav-link.active {
            color: var(--accent-color);
            border-bottom-color: var(--accent-color);
        }

        .catalog-footer {
            background: var(--light-bg);
            padding: 2rem 0;
            margin-top: 3rem;
            border-top: 1px solid var(--border-color);
        }

        @media print {
            .catalog-header,
            .nav-categories,
            .catalog-footer {
                display: none !important;
            }

            .product-item {
                page-break-inside: avoid;
            }

            .category-section {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .product-item {
                flex-direction: column;
                gap: 1rem;
            }

            .product-image {
                width: 100%;
                height: 200px;
            }

            .search-box {
                width: 100%;
            }

            .category-nav-item {
                display: block;
                margin-right: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Header -->
    <header class="catalog-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="catalog-title">Product Catalog</h1>
                    <p class="catalog-subtitle">{{ config('app.name', 'Cynosure Titli Management') }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <form class="d-inline-flex gap-2" method="GET" action="{{ route('catalog.search') }}">
                        <input class="form-control search-box" type="search" name="q"
                               placeholder="Search products..." value="{{ request('q') }}">
                        <button class="btn btn-catalog" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    @if(!request()->routeIs('catalog.index'))
    <nav class="nav-categories">
        <div class="container">
            <a href="{{ route('catalog.index') }}" class="category-nav-item">
                <span class="category-nav-link {{ request()->routeIs('catalog.index') ? 'active' : '' }}">
                    <i class="bi bi-house me-1"></i>All Categories
                </span>
            </a>

            @php
                $navCategories = \App\Models\Category::where('is_active', true)
                    ->withCount(['products' => function ($query) {
                        $query->whereNotNull('images')->where('images', '!=', '[]');
                    }])
                    ->having('products_count', '>', 0)
                    ->orderBy('name')
                    ->get();
            @endphp

            @foreach($navCategories as $navCategory)
            <a href="{{ route('catalog.category', $navCategory->slug) }}" class="category-nav-item">
                <span class="category-nav-link">
                    {{ $navCategory->name }} ({{ $navCategory->products_count }})
                </span>
            </a>
            @endforeach
        </div>
    </nav>
    @endif

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="catalog-footer">
        <div class="container">
            <div class="contact-info">
                <h5 class="mb-3">Contact Us for Orders</h5>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <p class="mb-3 text-muted">
                            Interested in any of our products? Contact us for pricing, availability, and orders.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="tel:+918250346616" class="btn btn-catalog">
                                <i class="bi bi-telephone me-2"></i>Call: +91 8250 346616
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
            </div>

            <div class="text-center mt-3">
                <p class="mb-0 text-muted small">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>