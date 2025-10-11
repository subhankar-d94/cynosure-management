@extends('layouts.app')

@section('title', 'Product Details - ' . $product->name)

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">{{ $product->name }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <h2 class="mb-0 me-3">{{ $product->name }}</h2>
                        @if($product->is_customizable)
                            <span class="badge bg-info fs-6">Customizable</span>
                        @else
                            <span class="badge bg-secondary fs-6">Standard</span>
                        @endif
                    </div>
                    <p class="text-muted mb-0">
                        <strong>SKU:</strong> <code>{{ $product->sku }}</code> |
                        <strong>Category:</strong> {{ $product->category->name ?? 'Uncategorized' }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-info" onclick="duplicateProduct()">
                        <i class="bi bi-files me-1"></i>Duplicate
                    </button>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots me-1"></i>More
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="generateQRCode()">
                                <i class="bi bi-qr-code me-2"></i>Generate QR Code</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printLabel()">
                                <i class="bi bi-printer me-2"></i>Print Label</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct()">
                                <i class="bi bi-trash me-2"></i>Delete Product</a></li>
                        </ul>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Product Overview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Product Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $product->description ?: 'No description provided.' }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Base Price</h6>
                            <h4 class="text-primary mb-0">₹{{ number_format($product->base_price, 2) }}</h4>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Weight</h6>
                            <p class="mb-0">{{ $product->weight ? $product->weight . ' kg' : 'Not specified' }}</p>
                        </div>

                        @if($product->dimensions)
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Dimensions (L × W × H)</h6>
                            <p class="mb-0">
                                {{ $product->dimensions['length'] ?? 'N/A' }} ×
                                {{ $product->dimensions['width'] ?? 'N/A' }} ×
                                {{ $product->dimensions['height'] ?? 'N/A' }} cm
                            </p>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Created Date</h6>
                            <p class="mb-0">{{ $product->created_at->format('F j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Information -->
            @if($product->inventory)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-boxes me-2"></i>Inventory Status
                    </h5>
                    <a href="{{ route('inventory.show', $product->inventory) }}" class="btn btn-sm btn-outline-primary">
                        View Details
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3 class="mb-1 {{ $product->inventory->quantity_in_stock <= $product->inventory->reorder_level ? 'text-warning' : 'text-success' }}">
                                    {{ $product->inventory->quantity_in_stock }}
                                </h3>
                                <small class="text-muted">Current Stock</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3 class="mb-1 text-info">{{ $product->inventory->reorder_level }}</h3>
                                <small class="text-muted">Reorder Level</small>
                            </div>
                        </div>
                    </div>

                    @if($product->inventory->quantity_in_stock <= $product->inventory->reorder_level)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Low Stock Alert:</strong> Current stock is at or below the reorder level.
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No Inventory Data</h5>
                    <p class="text-muted">This product doesn't have inventory tracking set up.</p>
                    <button class="btn btn-primary" onclick="setupInventory()">
                        <i class="bi bi-plus-lg me-1"></i>Setup Inventory
                    </button>
                </div>
            </div>
            @endif

            <!-- Product Variants (for customizable products) -->
            @if($product->is_customizable)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-palette me-2"></i>Product Variants
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadVariants()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="variantsContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading variants...</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Order History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Orders
                    </h5>
                    <a href="{{ route('orders.index', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-primary">
                        View All Orders
                    </a>
                </div>
                <div class="card-body">
                    @if($product->orderItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->orderItems->take(10) as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.show', $item->order) }}" class="text-decoration-none">
                                                #{{ $item->order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $item->order->customer->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                        <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                        <td>{{ $item->order->order_date->format('M j, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->order->status === 'completed' ? 'success' : ($item->order->status === 'pending' ? 'warning' : 'primary') }}">
                                                {{ ucfirst($item->order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                            <h6 class="mt-3 text-muted">No Orders Yet</h6>
                            <p class="text-muted">This product hasn't been ordered yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up me-1"></i>Product Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="mb-1 text-primary">{{ $stats['total_orders'] }}</h4>
                                <small class="text-muted">Total Orders</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1 text-success">{{ $stats['total_quantity_sold'] }}</h4>
                            <small class="text-muted">Units Sold</small>
                        </div>
                        <div class="col-6">
                            <div class="border-end border-top pt-3">
                                <h4 class="mb-1 text-info">₹{{ number_format($stats['total_revenue'], 2) }}</h4>
                                <small class="text-muted">Total Revenue</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border-top pt-3">
                                <h4 class="mb-1 text-warning">₹{{ number_format($stats['average_order_value'], 2) }}</h4>
                                <small class="text-muted">Avg. Order Value</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-images me-1"></i>Product Images
                    </h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="uploadImages()">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="productImages" class="row g-2">
                        @if($product->hasImages())
                            @foreach($product->images as $imagePath)
                            <div class="col-md-4 col-6">
                                <div class="position-relative">
                                    <img src="{{ Storage::url($imagePath) }}"
                                         class="img-thumbnail w-100"
                                         style="height: 150px; object-fit: cover;"
                                         onclick="showImageModal('{{ Storage::url($imagePath) }}')">
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="col-12 text-center py-4">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">No images uploaded</p>
                            <button class="btn btn-sm btn-outline-primary" onclick="uploadImages()">
                                Upload Images
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Supplier Information -->
            @if($product->inventory && $product->inventory->supplier)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-building me-1"></i>Supplier Information
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">{{ $product->inventory->supplier->company_name }}</h6>
                    <p class="text-muted small mb-2">{{ $product->inventory->supplier->email }}</p>
                    <p class="text-muted small mb-3">{{ $product->inventory->supplier->phone }}</p>
                    <a href="{{ route('suppliers.show', $product->inventory->supplier) }}" class="btn btn-sm btn-outline-primary">
                        View Supplier Details
                    </a>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="addToOrder()">
                            <i class="bi bi-cart-plus me-1"></i>Add to New Order
                        </button>
                        @if($product->inventory)
                        <button class="btn btn-outline-info btn-sm" onclick="adjustStock()">
                            <i class="bi bi-boxes me-1"></i>Adjust Stock
                        </button>
                        @endif
                        <button class="btn btn-outline-warning btn-sm" onclick="createPurchaseOrder()">
                            <i class="bi bi-bag-plus me-1"></i>Create Purchase Order
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="viewReports()">
                            <i class="bi bi-graph-up me-1"></i>View Reports
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if($product->is_customizable)
        loadVariants();
    @endif
});

function loadVariants() {
    $('#variantsContainer').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading variants...</p>
        </div>
    `);

    $.get('{{ route("products.variants", $product) }}')
        .done(function(response) {
            if (response.success && response.data.length > 0) {
                let html = '<div class="row g-2">';
                response.data.forEach(function(variant, index) {
                    html += `
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="mb-2">Variant ${index + 1}</h6>
                                <small class="text-muted">
                                    ${JSON.stringify(variant).replace(/[{}]/g, '').replace(/"/g, '')}
                                </small>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $('#variantsContainer').html(html);
            } else {
                $('#variantsContainer').html(`
                    <div class="text-center py-4">
                        <i class="bi bi-palette text-muted" style="font-size: 2rem;"></i>
                        <p class="mt-2 text-muted">No variants found. Variants are created when customers place customized orders.</p>
                    </div>
                `);
            }
        })
        .fail(function() {
            $('#variantsContainer').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading variants. Please try again.
                </div>
            `);
        });
}

function duplicateProduct() {
    if (confirm('Create a duplicate of this product?')) {
        $.post('{{ route("products.duplicate", $product) }}', {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                setTimeout(function() {
                    window.location.href = '{{ route("products.edit", "") }}/' + response.data.id;
                }, 1500);
            } else {
                showAlert(response.message, 'danger');
            }
        })
        .fail(function() {
            showAlert('Error duplicating product', 'danger');
        });
    }
}

function deleteProduct() {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        $.ajax({
            url: '{{ route("products.destroy", $product) }}',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                setTimeout(function() {
                    window.location.href = '{{ route("products.index") }}';
                }, 1500);
            } else {
                showAlert(response.message, 'danger');
            }
        })
        .fail(function() {
            showAlert('Error deleting product', 'danger');
        });
    }
}

function addToOrder() {
    window.location.href = '{{ route("orders.create") }}?product_id={{ $product->id }}';
}

function adjustStock() {
    // Implementation for stock adjustment modal
    showAlert('Stock adjustment feature coming soon', 'info');
}

function createPurchaseOrder() {
    window.location.href = '{{ route("purchases.create") }}?product_id={{ $product->id }}';
}

function viewReports() {
    window.location.href = '{{ route("reports.sales.product") }}?product_id={{ $product->id }}';
}

function setupInventory() {
    // Implementation for inventory setup
    showAlert('Inventory setup feature coming soon', 'info');
}

function uploadImages() {
    // Redirect to edit page for image upload
    window.location.href = '{{ route("products.edit", $product) }}';
}

function showImageModal(imageUrl) {
    const modal = `
        <div class="modal fade" id="imageModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Product Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imageUrl}" class="img-fluid" alt="Product Image">
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal
    $('#imageModal').remove();

    // Add new modal and show it
    $('body').append(modal);
    $('#imageModal').modal('show');
}

function generateQRCode() {
    // Implementation for QR code generation
    showAlert('QR code generation feature coming soon', 'info');
}

function printLabel() {
    // Implementation for label printing
    showAlert('Label printing feature coming soon', 'info');
}

function showAlert(message, type) {
    const alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.container-fluid').prepend(alert);

    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush
