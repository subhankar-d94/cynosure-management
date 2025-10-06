@extends('layouts.app')

@section('title', 'Supplier Materials')

@push('styles')
<style>
    .materials-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .supplier-info {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .materials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .material-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid #28a745;
    }

    .material-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }

    .material-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 15px;
    }

    .material-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin: 0;
    }

    .material-category {
        background: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .material-details {
        margin-bottom: 15px;
    }

    .material-spec {
        display: flex;
        justify-content: between;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .spec-label {
        color: #6c757d;
        font-weight: 500;
    }

    .spec-value {
        color: #495057;
        font-weight: 600;
    }

    .price-section {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .price-main {
        font-size: 1.25rem;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 5px;
    }

    .price-details {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .stock-indicator {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .stock-status {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .stock-in { background-color: #28a745; }
    .stock-low { background-color: #ffc107; }
    .stock-out { background-color: #dc3545; }

    .material-actions {
        display: flex;
        gap: 8px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .filters-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .category-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .category-tab {
        padding: 8px 16px;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        background: #fff;
        color: #495057;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .category-tab:hover, .category-tab.active {
        background: #28a745;
        border-color: #28a745;
        color: white;
        text-decoration: none;
    }

    .materials-stats {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .quality-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .quality-a { background: #d4edda; color: #155724; }
    .quality-b { background: #d1ecf1; color: #0c5460; }
    .quality-c { background: #fff3cd; color: #856404; }

    .material-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .placeholder-image {
        width: 100%;
        height: 120px;
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 2rem;
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .materials-grid {
            grid-template-columns: 1fr;
        }

        .materials-header {
            padding: 20px;
            text-align: center;
        }

        .category-tabs {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="materials-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0">Materials Catalog</h1>
                <p class="mb-0 opacity-75">Browse and manage supplier materials, specifications, and pricing</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog"></i> Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportMaterials()">
                            <i class="fas fa-download"></i> Export Catalog
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="importMaterials()">
                            <i class="fas fa-upload"></i> Import Materials
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="requestQuote()">
                            <i class="fas fa-quote-left"></i> Request Quote
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Supplier Info -->
    @if(isset($supplier))
    <div class="supplier-info">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">{{ $supplier->company_name ?? 'TechCorp Solutions Ltd.' }}</h5>
                <p class="text-muted mb-0">
                    <i class="fas fa-layer-group"></i> {{ $supplier->total_materials ?? '127' }} Materials Available •
                    <i class="fas fa-tags"></i> {{ $supplier->categories ?? '8' }} Categories •
                    <i class="fas fa-star"></i> {{ $supplier->rating ?? '4.2' }}/5.0 Rating
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('suppliers.show', $supplier->id ?? 1) }}" class="btn btn-outline-primary btn-sm me-2">
                    <i class="fas fa-eye"></i> View Profile
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> All Suppliers
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Materials Statistics -->
    <div class="materials-stats">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $totalMaterials ?? '127' }}</div>
                <div class="stat-label">Total Materials</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $availableMaterials ?? '98' }}</div>
                <div class="stat-label">In Stock</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $categoriesCount ?? '8' }}</div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">${{ number_format($avgPrice ?? 125.50, 2) }}</div>
                <div class="stat-label">Avg Price</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $newMaterials ?? '12' }}</div>
                <div class="stat-label">New This Month</div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filters-section">
        <!-- Category Tabs -->
        <div class="category-tabs">
            <a href="#" class="category-tab active" data-category="all">All Materials</a>
            <a href="#" class="category-tab" data-category="raw_materials">Raw Materials</a>
            <a href="#" class="category-tab" data-category="electronics">Electronics</a>
            <a href="#" class="category-tab" data-category="textiles">Textiles</a>
            <a href="#" class="category-tab" data-category="chemicals">Chemicals</a>
            <a href="#" class="category-tab" data-category="metals">Metals</a>
            <a href="#" class="category-tab" data-category="plastics">Plastics</a>
            <a href="#" class="category-tab" data-category="packaging">Packaging</a>
            <a href="#" class="category-tab" data-category="tools">Tools</a>
        </div>

        <!-- Search and Filters -->
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchMaterials" placeholder="Search materials, SKU, specifications...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="qualityFilter">
                    <option value="">All Quality</option>
                    <option value="A">Grade A</option>
                    <option value="B">Grade B</option>
                    <option value="C">Grade C</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="stockFilter">
                    <option value="">All Stock</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="priceSort">
                    <option value="">Sort by Price</option>
                    <option value="low_to_high">Low to High</option>
                    <option value="high_to_low">High to Low</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100" onclick="requestBulkQuote()">
                    <i class="fas fa-shopping-cart"></i> Request Quote
                </button>
            </div>
        </div>
    </div>

    <!-- Materials Grid -->
    <div class="materials-grid" id="materialsGrid">
        <!-- Material Card 1 -->
        <div class="material-card" data-category="electronics" data-quality="A" data-stock="in_stock" data-price="125.50">
            <div class="placeholder-image">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="material-header">
                <h6 class="material-title">High-Performance Microcontroller</h6>
                <span class="material-category">Electronics</span>
            </div>

            <div class="material-details">
                <div class="material-spec">
                    <span class="spec-label">SKU:</span>
                    <span class="spec-value">MC-ARM-2024</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Specification:</span>
                    <span class="spec-value">32-bit ARM Cortex-M4</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Package:</span>
                    <span class="spec-value">LQFP-100</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Quality:</span>
                    <span class="quality-badge quality-a">Grade A</span>
                </div>
            </div>

            <div class="price-section">
                <div class="price-main">$125.50</div>
                <div class="price-details">Per unit • MOQ: 100 pcs</div>
            </div>

            <div class="stock-indicator">
                <span class="stock-status stock-in"></span>
                <span>In Stock (2,450 units)</span>
            </div>

            <div class="material-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="viewMaterial('MC-ARM-2024')">
                    <i class="fas fa-eye"></i> Details
                </button>
                <button class="btn btn-success btn-sm" onclick="addToQuote('MC-ARM-2024')">
                    <i class="fas fa-plus"></i> Quote
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="downloadDatasheet('MC-ARM-2024')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>

        <!-- Material Card 2 -->
        <div class="material-card" data-category="raw_materials" data-quality="A" data-stock="in_stock" data-price="45.00">
            <div class="placeholder-image">
                <i class="fas fa-industry"></i>
            </div>
            <div class="material-header">
                <h6 class="material-title">Stainless Steel Rod</h6>
                <span class="material-category">Raw Materials</span>
            </div>

            <div class="material-details">
                <div class="material-spec">
                    <span class="spec-label">SKU:</span>
                    <span class="spec-value">SS-ROD-316L</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Material:</span>
                    <span class="spec-value">316L Stainless Steel</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Diameter:</span>
                    <span class="spec-value">10mm x 2m</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Quality:</span>
                    <span class="quality-badge quality-a">Grade A</span>
                </div>
            </div>

            <div class="price-section">
                <div class="price-main">$45.00</div>
                <div class="price-details">Per meter • MOQ: 50 meters</div>
            </div>

            <div class="stock-indicator">
                <span class="stock-status stock-in"></span>
                <span>In Stock (1,250 meters)</span>
            </div>

            <div class="material-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="viewMaterial('SS-ROD-316L')">
                    <i class="fas fa-eye"></i> Details
                </button>
                <button class="btn btn-success btn-sm" onclick="addToQuote('SS-ROD-316L')">
                    <i class="fas fa-plus"></i> Quote
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="downloadDatasheet('SS-ROD-316L')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>

        <!-- Material Card 3 -->
        <div class="material-card" data-category="textiles" data-quality="B" data-stock="low_stock" data-price="18.75">
            <div class="placeholder-image">
                <i class="fas fa-tshirt"></i>
            </div>
            <div class="material-header">
                <h6 class="material-title">Organic Cotton Fabric</h6>
                <span class="material-category">Textiles</span>
            </div>

            <div class="material-details">
                <div class="material-spec">
                    <span class="spec-label">SKU:</span>
                    <span class="spec-value">COT-ORG-200</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Weight:</span>
                    <span class="spec-value">200 GSM</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Width:</span>
                    <span class="spec-value">150cm</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Quality:</span>
                    <span class="quality-badge quality-b">Grade B</span>
                </div>
            </div>

            <div class="price-section">
                <div class="price-main">$18.75</div>
                <div class="price-details">Per yard • MOQ: 200 yards</div>
            </div>

            <div class="stock-indicator">
                <span class="stock-status stock-low"></span>
                <span>Low Stock (45 yards)</span>
            </div>

            <div class="material-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="viewMaterial('COT-ORG-200')">
                    <i class="fas fa-eye"></i> Details
                </button>
                <button class="btn btn-success btn-sm" onclick="addToQuote('COT-ORG-200')">
                    <i class="fas fa-plus"></i> Quote
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="downloadDatasheet('COT-ORG-200')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>

        <!-- Material Card 4 -->
        <div class="material-card" data-category="chemicals" data-quality="A" data-stock="in_stock" data-price="89.00">
            <div class="placeholder-image">
                <i class="fas fa-flask"></i>
            </div>
            <div class="material-header">
                <h6 class="material-title">Industrial Solvent</h6>
                <span class="material-category">Chemicals</span>
            </div>

            <div class="material-details">
                <div class="material-spec">
                    <span class="spec-label">SKU:</span>
                    <span class="spec-value">SOL-IPA-99</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Purity:</span>
                    <span class="spec-value">99.8% IPA</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Package:</span>
                    <span class="spec-value">25L Drum</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Quality:</span>
                    <span class="quality-badge quality-a">Grade A</span>
                </div>
            </div>

            <div class="price-section">
                <div class="price-main">$89.00</div>
                <div class="price-details">Per 25L drum • MOQ: 4 drums</div>
            </div>

            <div class="stock-indicator">
                <span class="stock-status stock-in"></span>
                <span>In Stock (67 drums)</span>
            </div>

            <div class="material-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="viewMaterial('SOL-IPA-99')">
                    <i class="fas fa-eye"></i> Details
                </button>
                <button class="btn btn-success btn-sm" onclick="addToQuote('SOL-IPA-99')">
                    <i class="fas fa-plus"></i> Quote
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="downloadDatasheet('SOL-IPA-99')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>

        <!-- Material Card 5 -->
        <div class="material-card" data-category="plastics" data-quality="B" data-stock="out_of_stock" data-price="32.25">
            <div class="placeholder-image">
                <i class="fas fa-cube"></i>
            </div>
            <div class="material-header">
                <h6 class="material-title">ABS Plastic Pellets</h6>
                <span class="material-category">Plastics</span>
            </div>

            <div class="material-details">
                <div class="material-spec">
                    <span class="spec-label">SKU:</span>
                    <span class="spec-value">ABS-PLT-BLK</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Color:</span>
                    <span class="spec-value">Black</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Grade:</span>
                    <span class="spec-value">Injection Molding</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Quality:</span>
                    <span class="quality-badge quality-b">Grade B</span>
                </div>
            </div>

            <div class="price-section">
                <div class="price-main">$32.25</div>
                <div class="price-details">Per kg • MOQ: 500 kg</div>
            </div>

            <div class="stock-indicator">
                <span class="stock-status stock-out"></span>
                <span>Out of Stock (ETA: 2 weeks)</span>
            </div>

            <div class="material-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="viewMaterial('ABS-PLT-BLK')">
                    <i class="fas fa-eye"></i> Details
                </button>
                <button class="btn btn-outline-warning btn-sm" onclick="notifyRestock('ABS-PLT-BLK')">
                    <i class="fas fa-bell"></i> Notify
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="downloadDatasheet('ABS-PLT-BLK')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>

        <!-- Material Card 6 -->
        <div class="material-card" data-category="tools" data-quality="A" data-stock="in_stock" data-price="156.80">
            <div class="placeholder-image">
                <i class="fas fa-tools"></i>
            </div>
            <div class="material-header">
                <h6 class="material-title">Precision Cutting Tool</h6>
                <span class="material-category">Tools</span>
            </div>

            <div class="material-details">
                <div class="material-spec">
                    <span class="spec-label">SKU:</span>
                    <span class="spec-value">CUT-CBN-12</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Material:</span>
                    <span class="spec-value">Carbide CBN</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Size:</span>
                    <span class="spec-value">12mm diameter</span>
                </div>
                <div class="material-spec">
                    <span class="spec-label">Quality:</span>
                    <span class="quality-badge quality-a">Grade A</span>
                </div>
            </div>

            <div class="price-section">
                <div class="price-main">$156.80</div>
                <div class="price-details">Per piece • MOQ: 10 pieces</div>
            </div>

            <div class="stock-indicator">
                <span class="stock-status stock-in"></span>
                <span>In Stock (34 pieces)</span>
            </div>

            <div class="material-actions">
                <button class="btn btn-outline-primary btn-sm" onclick="viewMaterial('CUT-CBN-12')">
                    <i class="fas fa-eye"></i> Details
                </button>
                <button class="btn btn-success btn-sm" onclick="addToQuote('CUT-CBN-12')">
                    <i class="fas fa-plus"></i> Quote
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="downloadDatasheet('CUT-CBN-12')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Showing 1-6 of {{ $totalMaterials ?? '127' }} materials
        </div>
        <nav>
            <ul class="pagination">
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>
                <li class="page-item active">
                    <span class="page-link">1</span>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Material Details Modal -->
<div class="modal fade" id="materialDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Material Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="materialDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Quote Request Modal -->
<div class="modal fade" id="quoteRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Quote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quoteRequestForm">
                    <div class="mb-3">
                        <label class="form-label">Selected Materials</label>
                        <div id="selectedMaterialsList" class="border rounded p-2 mb-2" style="max-height: 150px; overflow-y: auto;">
                            <!-- Selected materials will be listed here -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Delivery Date</label>
                                <input type="date" class="form-control" name="delivery_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="asap">ASAP</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Requirements</label>
                        <textarea class="form-control" name="requirements" rows="3" placeholder="Special requirements, certifications needed, etc."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitQuoteRequest()">Send Quote Request</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedMaterials = [];

$(document).ready(function() {
    bindEventListeners();
    initializeFilters();
});

function bindEventListeners() {
    // Category tabs
    $('.category-tab').on('click', function(e) {
        e.preventDefault();
        $('.category-tab').removeClass('active');
        $(this).addClass('active');

        const category = $(this).data('category');
        filterByCategory(category);
    });

    // Search functionality
    $('#searchMaterials').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterMaterials();
    });

    // Filter dropdowns
    $('#qualityFilter, #stockFilter, #priceSort').on('change', function() {
        filterMaterials();
    });
}

function initializeFilters() {
    // Set today's date as minimum for delivery date
    const today = new Date().toISOString().split('T')[0];
    $('input[name="delivery_date"]').attr('min', today);
}

function filterByCategory(category) {
    const cards = $('.material-card');

    if (category === 'all') {
        cards.show();
    } else {
        cards.each(function() {
            const cardCategory = $(this).data('category');
            $(this).toggle(cardCategory === category);
        });
    }
}

function filterMaterials() {
    const searchTerm = $('#searchMaterials').val().toLowerCase();
    const qualityFilter = $('#qualityFilter').val();
    const stockFilter = $('#stockFilter').val();
    const priceSort = $('#priceSort').val();

    let visibleCards = $('.material-card').filter(function() {
        const card = $(this);
        const text = card.text().toLowerCase();
        const quality = card.data('quality');
        const stock = card.data('stock');

        // Search filter
        if (searchTerm && text.indexOf(searchTerm) === -1) {
            return false;
        }

        // Quality filter
        if (qualityFilter && quality !== qualityFilter) {
            return false;
        }

        // Stock filter
        if (stockFilter && stock !== stockFilter) {
            return false;
        }

        return true;
    });

    // Hide non-matching cards
    $('.material-card').hide();
    visibleCards.show();

    // Sort by price if selected
    if (priceSort) {
        const sortedCards = visibleCards.sort(function(a, b) {
            const priceA = parseFloat($(a).data('price'));
            const priceB = parseFloat($(b).data('price'));

            if (priceSort === 'low_to_high') {
                return priceA - priceB;
            } else {
                return priceB - priceA;
            }
        });

        const container = $('#materialsGrid');
        sortedCards.detach().appendTo(container);
    }
}

function viewMaterial(sku) {
    // Load material details in modal
    $('#materialDetailsContent').html(`
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Loading material details for ${sku}...</p>
        </div>
    `);
    $('#materialDetailsModal').modal('show');

    // Simulate loading material details
    setTimeout(function() {
        $('#materialDetailsContent').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Product Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>SKU:</strong></td><td>${sku}</td></tr>
                        <tr><td><strong>Category:</strong></td><td>Electronics</td></tr>
                        <tr><td><strong>Brand:</strong></td><td>TechCorp</td></tr>
                        <tr><td><strong>Manufacturer:</strong></td><td>ARM Ltd.</td></tr>
                        <tr><td><strong>Lead Time:</strong></td><td>2-3 weeks</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Specifications</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Type:</strong></td><td>32-bit ARM Cortex-M4</td></tr>
                        <tr><td><strong>Speed:</strong></td><td>168 MHz</td></tr>
                        <tr><td><strong>Memory:</strong></td><td>1MB Flash, 192KB RAM</td></tr>
                        <tr><td><strong>Package:</strong></td><td>LQFP-100</td></tr>
                        <tr><td><strong>Temperature:</strong></td><td>-40°C to +85°C</td></tr>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                <h6 class="text-primary">Pricing Tiers</h6>
                <table class="table table-sm">
                    <thead><tr><th>Quantity</th><th>Unit Price</th><th>Total</th></tr></thead>
                    <tbody>
                        <tr><td>100-499 pcs</td><td>$125.50</td><td>$12,550.00</td></tr>
                        <tr><td>500-999 pcs</td><td>$118.75</td><td>$59,375.00</td></tr>
                        <tr><td>1000+ pcs</td><td>$112.00</td><td>$112,000.00</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <h6 class="text-primary">Documentation</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="downloadDatasheet('${sku}')">
                        <i class="fas fa-file-pdf"></i> Datasheet
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="downloadSpecs('${sku}')">
                        <i class="fas fa-file-alt"></i> Specifications
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="downloadSample('${sku}')">
                        <i class="fas fa-vial"></i> Request Sample
                    </button>
                </div>
            </div>
        `);
    }, 1000);
}

function addToQuote(sku) {
    // Add material to quote request
    if (!selectedMaterials.includes(sku)) {
        selectedMaterials.push(sku);
        updateQuoteButton();

        // Show success message
        showToast(`${sku} added to quote request`, 'success');
    } else {
        showToast(`${sku} is already in quote request`, 'warning');
    }
}

function updateQuoteButton() {
    const count = selectedMaterials.length;
    if (count > 0) {
        if (!$('#floatingQuoteBtn').length) {
            $('body').append(`
                <div id="floatingQuoteBtn" class="position-fixed" style="bottom: 20px; right: 20px; z-index: 1050;">
                    <button class="btn btn-success btn-lg" onclick="showQuoteRequest()">
                        <i class="fas fa-shopping-cart"></i> Quote (${count})
                    </button>
                </div>
            `);
        } else {
            $('#floatingQuoteBtn button').html(`<i class="fas fa-shopping-cart"></i> Quote (${count})`);
        }
    } else {
        $('#floatingQuoteBtn').remove();
    }
}

function showQuoteRequest() {
    // Update selected materials list in modal
    let materialsList = '';
    selectedMaterials.forEach(function(sku) {
        materialsList += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>${sku}</span>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromQuote('${sku}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });

    $('#selectedMaterialsList').html(materialsList || '<p class="text-muted mb-0">No materials selected</p>');
    $('#quoteRequestModal').modal('show');
}

function removeFromQuote(sku) {
    selectedMaterials = selectedMaterials.filter(item => item !== sku);
    updateQuoteButton();
    showQuoteRequest(); // Refresh the modal
}

function submitQuoteRequest() {
    if (selectedMaterials.length === 0) {
        showToast('Please select at least one material', 'warning');
        return;
    }

    const formData = new FormData(document.getElementById('quoteRequestForm'));
    const deliveryDate = formData.get('delivery_date');

    if (!deliveryDate) {
        showToast('Please select a delivery date', 'warning');
        return;
    }

    // Simulate quote request submission
    $('#quoteRequestModal').modal('hide');
    showToast(`Quote request submitted for ${selectedMaterials.length} materials`, 'success');

    // Reset
    selectedMaterials = [];
    updateQuoteButton();
}

function requestBulkQuote() {
    if (selectedMaterials.length === 0) {
        showToast('Please select materials first by clicking the "Quote" button on material cards', 'info');
        return;
    }
    showQuoteRequest();
}

function downloadDatasheet(sku) {
    showToast(`Downloading datasheet for ${sku}...`, 'info');
}

function downloadSpecs(sku) {
    showToast(`Downloading specifications for ${sku}...`, 'info');
}

function downloadSample(sku) {
    showToast(`Sample request submitted for ${sku}`, 'success');
}

function notifyRestock(sku) {
    showToast(`You will be notified when ${sku} is restocked`, 'success');
}

function exportMaterials() {
    showToast('Exporting materials catalog...', 'info');
}

function importMaterials() {
    showToast('Import materials functionality would be implemented here', 'info');
}

function requestQuote() {
    if (selectedMaterials.length > 0) {
        showQuoteRequest();
    } else {
        showToast('Please select materials first', 'info');
    }
}

function showToast(message, type = 'info') {
    // Create toast notification
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : type === 'danger' ? 'danger' : 'info'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);

    // Add to toast container (create if doesn't exist)
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>');
    }

    $('#toastContainer').append(toast);

    // Show toast
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();

    // Remove from DOM after hiding
    toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}
</script>
@endpush