@extends('layouts.app')

@section('title', 'Edit Product - ' . $product->name)

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Edit Product</h2>
                    <p class="text-muted mb-0">Update product information and inventory settings</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye me-1"></i>View Product
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="productForm" class="needs-validation" novalidate enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Main Product Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-box-seam me-2"></i>Product Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{ old('name', $product->name) }}" required>
                                <div class="invalid-feedback">Please provide a product name.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sku" name="sku"
                                       value="{{ old('sku', $product->sku) }}" required>
                                <div class="form-text">Unique product identifier</div>
                                <div class="invalid-feedback">Please provide a unique SKU.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @if($category->children->count() > 0)
                                            @foreach($category->children as $child)
                                                <option value="{{ $child->id }}"
                                                        {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>
                                                    {{ $category->name }} → {{ $child->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="base_price" class="form-label">Base Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="base_price" name="base_price"
                                       value="{{ old('base_price', $product->base_price) }}" step="0.01" min="0" required>
                                <div class="invalid-feedback">Please provide a valid price.</div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                          placeholder="Enter product description...">{{ old('description', $product->description) }}</textarea>
                                <div class="form-text">Maximum 2000 characters</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Specifications -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-rulers me-2"></i>Specifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="weight" class="form-label">Weight (kg)</label>
                                <input type="number" class="form-control" id="weight" name="weight"
                                       value="{{ old('weight', $product->weight) }}" step="0.001" min="0">
                                <div class="form-text">Product weight in kilograms</div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_customizable" name="is_customizable"
                                           value="1" {{ old('is_customizable', $product->is_customizable) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_customizable">
                                        Customizable Product
                                    </label>
                                    <div class="form-text">Allow customers to customize this product</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dimensions (cm)</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="dimensions[length]"
                                               placeholder="Length" step="0.1" min="0"
                                               value="{{ old('dimensions.length', $product->dimensions['length'] ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="dimensions[width]"
                                               placeholder="Width" step="0.1" min="0"
                                               value="{{ old('dimensions.width', $product->dimensions['width'] ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="dimensions[height]"
                                               placeholder="Height" step="0.1" min="0"
                                               value="{{ old('dimensions.height', $product->dimensions['height'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Information -->
                @if($product->inventory)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-boxes me-2"></i>Inventory Settings
                        </h5>
                        <a href="{{ route('inventory.show', $product->inventory) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>View Inventory Details
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Inventory quantities should be adjusted through the Inventory Management section.
                            Only settings can be modified here.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Current Stock</label>
                                <input type="text" class="form-control" value="{{ $product->inventory->quantity_in_stock }}" readonly>
                                <div class="form-text">Use inventory adjustment to change stock levels</div>
                            </div>

                            <div class="col-md-4">
                                <label for="reorder_level" class="form-label">Reorder Level</label>
                                <input type="number" class="form-control" id="reorder_level" name="reorder_level"
                                       value="{{ old('reorder_level', $product->inventory->reorder_level) }}" min="0">
                                <div class="form-text">Alert when stock reaches this level</div>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Stock Value</label>
                                <input type="text" class="form-control" id="stock_value" readonly>
                                <div class="form-text">Calculated automatically</div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-boxes me-2"></i>Setup Inventory Tracking
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            This product doesn't have inventory tracking enabled. You can set it up below.
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="enable_inventory" name="enable_inventory" value="1">
                            <label class="form-check-label" for="enable_inventory">
                                Enable inventory tracking for this product
                            </label>
                        </div>

                        <div id="inventoryFields" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="initial_stock" class="form-label">Initial Stock Quantity</label>
                                    <input type="number" class="form-control" id="initial_stock" name="initial_stock" min="0" value="0">
                                </div>

                                <div class="col-md-4">
                                    <label for="reorder_level" class="form-label">Reorder Level</label>
                                    <input type="number" class="form-control" name="reorder_level" min="0" value="10">
                                </div>

                                <div class="col-md-4">
                                    <label for="cost_per_unit" class="form-label">Cost per Unit (₹)</label>
                                    <input type="number" class="form-control" name="cost_per_unit" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label for="supplier_id" class="form-label">Primary Supplier</label>
                                    <select class="form-select" name="supplier_id">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Update Product
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="saveAndContinue()">
                                <i class="bi bi-check2-all me-1"></i>Save & Continue Editing
                            </button>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>View Product
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Product Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle me-1"></i>Product Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Created</label>
                                <p class="text-muted mb-0">{{ $product->created_at->format('F j, Y g:i A') }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Last Updated</label>
                                <p class="text-muted mb-0">{{ $product->updated_at->format('F j, Y g:i A') }}</p>
                            </div>
                            @if($product->inventory)
                            <div class="col-6">
                                <label class="form-label">Current Stock</label>
                                <h5 class="mb-0 {{ $product->inventory->quantity_in_stock <= $product->inventory->reorder_level ? 'text-warning' : 'text-success' }}">
                                    {{ $product->inventory->quantity_in_stock }}
                                </h5>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Stock Status</label>
                                @if($product->inventory->quantity_in_stock <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($product->inventory->quantity_in_stock <= $product->inventory->reorder_level)
                                    <span class="badge bg-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-success">In Stock</span>
                                @endif
                            </div>
                            @endif
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
                        <div class="mb-3">
                            <input type="file" class="form-control" id="product_images" name="product_images[]"
                                   multiple accept="image/*" onchange="previewImages()">
                            <div class="form-text">Upload new product images</div>
                        </div>
                        <div id="imagePreview" class="row g-2">
                            @if($product->hasImages())
                                @foreach($product->images as $index => $imagePath)
                                <div class="col-6">
                                    <div class="position-relative">
                                        <img src="{{ Storage::url($imagePath) }}" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                                onclick="removeExistingImage('{{ $imagePath }}', {{ $index }})" style="margin: 2px;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                            <div class="col-12 text-center py-3">
                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted small mb-0 mt-2">No images uploaded</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-clock-history me-1"></i>Recent Activity
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @if($product->orderItems->count() > 0)
                                @foreach($product->orderItems->take(3) as $item)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Order #{{ $item->order_id }}</h6>
                                        <p class="text-muted small mb-1">{{ $item->quantity }} units ordered</p>
                                        <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-3">
                                    <i class="bi bi-clock text-muted"></i>
                                    <p class="text-muted small mb-0 mt-2">No recent activity</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="text-success mb-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                </div>
                <h4 class="mb-3">Product Updated Successfully!</h4>
                <p class="text-muted mb-4">Your changes have been saved.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-primary">View Product</a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Back to Products</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate stock value when cost per unit or stock changes
    calculateStockValue();

    $('#cost_per_unit').on('input', calculateStockValue);

    // Enable inventory fields toggle
    $('#enable_inventory').on('change', function() {
        $('#inventoryFields').toggle($(this).is(':checked'));
    });

    // Set cost per unit to base price by default
    $('#base_price').on('input', function() {
        if (!$('#cost_per_unit').val()) {
            $('#cost_per_unit').val($(this).val());
            calculateStockValue();
        }
    });

    // Form submission
    $('#productForm').on('submit', function(e) {
        e.preventDefault();

        if (this.checkValidity()) {
            updateProduct();
        } else {
            this.classList.add('was-validated');
        }
    });
});

function calculateStockValue() {
    const stockQty = {{ $product->inventory->quantity_in_stock ?? 0 }};
    const costPerUnit = parseFloat($('#cost_per_unit').val()) || 0;
    const stockValue = stockQty * costPerUnit;

    $('#stock_value').val('₹' + stockValue.toFixed(2));
}

function updateProduct() {
    const formData = new FormData();

    // Collect all form data
    const form = document.getElementById('productForm');
    new FormData(form).forEach((value, key) => {
        formData.append(key, value);
    });

    // Handle images
    const images = document.getElementById('product_images').files;
    for (let i = 0; i < images.length; i++) {
        formData.append('product_images[]', images[i]);
    }

    // Handle images to remove
    imagesToRemove.forEach(imagePath => {
        formData.append('images_to_remove[]', imagePath);
    });

    // Show loading state
    const submitBtn = $('#productForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Updating...');

    $.ajax({
        url: '{{ route("products.update", $product) }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            $('#successModal').modal('show');
        } else {
            showAlert(response.message || 'Error updating product', 'danger');
            if (response.errors) {
                displayValidationErrors(response.errors);
            }
        }
    })
    .fail(function(xhr) {
        if (xhr.status === 422) {
            const response = xhr.responseJSON;
            if (response.errors) {
                displayValidationErrors(response.errors);
            }
            showAlert('Please check the form for errors', 'danger');
        } else {
            showAlert('Error updating product. Please try again.', 'danger');
        }
    })
    .always(function() {
        submitBtn.prop('disabled', false).html(originalText);
    });
}

function saveAndContinue() {
    updateProduct();
    // Keep the form open for continued editing
    $('#successModal').on('hidden.bs.modal', function() {
        showAlert('Product updated successfully! You can continue editing.', 'success');
    });
}

function displayValidationErrors(errors) {
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    // Display new errors
    Object.keys(errors).forEach(function(field) {
        const input = $(`[name="${field}"]`);
        if (input.length) {
            input.addClass('is-invalid');

            // Create or update error message
            let feedback = input.siblings('.invalid-feedback');
            if (feedback.length === 0) {
                feedback = $('<div class="invalid-feedback"></div>');
                input.after(feedback);
            }
            feedback.text(errors[field][0]).show();
        }
    });
}

function previewImages() {
    const preview = document.getElementById('imagePreview');
    const files = document.getElementById('product_images').files;

    if (files.length === 0) {
        preview.innerHTML = `
            <div class="col-12 text-center py-3">
                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted small mb-0 mt-2">No images uploaded</p>
            </div>
        `;
        return;
    }

    preview.innerHTML = '';

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                onclick="removeImage(${i})" style="margin: 2px;">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    }
}

function removeImage(index) {
    const input = document.getElementById('product_images');
    const dt = new DataTransfer();

    for (let i = 0; i < input.files.length; i++) {
        if (i !== index) {
            dt.items.add(input.files[i]);
        }
    }

    input.files = dt.files;
    previewImages();
}

function uploadImages() {
    $('#product_images').click();
}

let imagesToRemove = [];

function removeExistingImage(imagePath, index) {
    // Add to removal list
    imagesToRemove.push(imagePath);

    // Remove from display
    const imageContainer = event.target.closest('.col-6');
    imageContainer.remove();

    // Check if no images left
    const preview = document.getElementById('imagePreview');
    if (preview.children.length === 0) {
        preview.innerHTML = `
            <div class="col-12 text-center py-3">
                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted small mb-0 mt-2">No images uploaded</p>
            </div>
        `;
    }
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

<style>
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 12px;
    width: 2px;
    height: calc(100% + 8px);
    background-color: #dee2e6;
}

.timeline-content h6 {
    font-size: 0.875rem;
}
</style>
@endpush