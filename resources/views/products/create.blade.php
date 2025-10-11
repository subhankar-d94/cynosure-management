@extends('layouts.app')

@section('title', 'Add New Product')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">Add Product</li>
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
                    <h2 class="mb-1">Add New Product</h2>
                    <p class="text-muted mb-0">Create a new product in your catalog</p>
                </div>
                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="productForm" class="needs-validation" novalidate enctype="multipart/form-data">
        @csrf
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
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">Please provide a product name.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sku" name="sku" required>
                                <div class="form-text">Unique product identifier</div>
                                <div class="invalid-feedback">Please provide a unique SKU.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @if($category->children->count() > 0)
                                            @foreach($category->children as $child)
                                                <option value="{{ $child->id }}">{{ $category->name }} → {{ $child->name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="base_price" class="form-label">Base Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="base_price" name="base_price" step="0.01" min="0" required>
                                <div class="invalid-feedback">Please provide a valid price.</div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                          placeholder="Enter product description..."></textarea>
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
                                <input type="number" class="form-control" id="weight" name="weight" step="0.001" min="0">
                                <div class="form-text">Product weight in kilograms</div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_customizable" name="is_customizable" value="1">
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
                                               placeholder="Length" step="0.1" min="0">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="dimensions[width]"
                                               placeholder="Width" step="0.1" min="0">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="dimensions[height]"
                                               placeholder="Height" step="0.1" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-boxes me-2"></i>Inventory Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="initial_stock" class="form-label">Initial Stock Quantity</label>
                                <input type="number" class="form-control" id="initial_stock" name="initial_stock" min="0" value="0">
                                <div class="form-text">Starting inventory count</div>
                            </div>

                            <div class="col-md-4">
                                <label for="reorder_level" class="form-label">Reorder Level</label>
                                <input type="number" class="form-control" id="reorder_level" name="reorder_level" min="0" value="1">
                                <div class="form-text">Alert when stock reaches this level</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-lg me-1"></i>Create Product
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">
                                <i class="bi bi-file-earmark me-1"></i>Save as Draft
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-lg me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-images me-1"></i>Product Images
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="product_images" name="product_images[]"
                                   multiple accept="image/*" onchange="previewImages()">
                            <div class="form-text">Upload product images (PNG, JPG, JPEG)</div>
                        </div>
                        <div id="imagePreview" class="row g-2">
                            <!-- Image previews will appear here -->
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-lightbulb me-1"></i>Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Use descriptive product names for better searchability
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Set accurate reorder levels to avoid stockouts
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Enable customization for products with variants
                            </li>
                            <li class="mb-0">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Add high-quality images to improve sales
                            </li>
                        </ul>
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
                <h4 class="mb-3">Product Created Successfully!</h4>
                <p class="text-muted mb-4">Your product has been added to the catalog.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">View All Products</a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Create Another</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate SKU based on product name
    $('#name').on('input', function() {
        if (!$('#sku').val()) {
            const sku = generateSKU($(this).val());
            $('#sku').val(sku);
        }
    });

    // Set cost per unit to base price by default
    $('#base_price').on('input', function() {
        if (!$('#cost_per_unit').val()) {
            $('#cost_per_unit').val($(this).val());
        }
    });

    // Submit button click
    $('#submitBtn').on('click', function(e) {
        e.preventDefault();
        const form = document.getElementById('productForm');
        if (form.checkValidity()) {
            createProduct();
        } else {
            form.classList.add('was-validated');
        }
    });
});

function generateSKU(productName) {
    // Remove special characters and convert to uppercase
    const cleanName = productName.replace(/[^a-zA-Z0-9\s]/g, '').toUpperCase();
    const words = cleanName.split(/\s+/).filter(word => word.length > 0);

    if (words.length === 0) return '';

    // Take first 3 characters of each word, max 3 words
    let sku = words.slice(0, 3).map(word => word.substring(0, 3)).join('');

    // Add random number
    const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

    return sku + randomNum;
}

function createProduct() {
    const formData = new FormData();

    // Collect all form data
    const form = document.getElementById('productForm');
    new FormData(form).forEach((value, key) => {
        formData.append(key, value);
    });

    // Handle checkbox for is_customizable
    const isCustomizable = document.getElementById('is_customizable');
    if (isCustomizable) {
        formData.set('is_customizable', isCustomizable.checked ? '1' : '0');
    }

    // Handle images
    const images = document.getElementById('product_images').files;
    for (let i = 0; i < images.length; i++) {
        formData.append('product_images[]', images[i]);
    }


    // Show loading state
    const submitBtn = $('#productForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Creating...');

    $.ajax({
        url: '{{ route("products.store") }}',
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
            $('#productForm')[0].reset();
            $('#imagePreview').empty();
        } else {
            showAlert(response.message || 'Error creating product', 'danger');
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
        } else if (xhr.status === 419) {
            showAlert('CSRF token mismatch. Please refresh the page and try again.', 'danger');
        } else {
            showAlert('Error creating product. Please try again. Status: ' + xhr.status, 'danger');
        }
    })
    .always(function() {
        submitBtn.prop('disabled', false).html(originalText);
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
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
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
    // This is a simple implementation - in production you might want more sophisticated file management
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

function saveAsDraft() {
    showAlert('Draft functionality coming soon', 'info');
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
