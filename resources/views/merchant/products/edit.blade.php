@extends('layouts.dashboard')

@section('title', __('Edit Product'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('merchant.products.index') }}">{{ __('Products') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-0">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>{{ __('Edit Product') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('merchant.products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Language Tabs -->
                    <ul class="nav nav-tabs mb-4" id="langTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="lang-en-tab" data-bs-toggle="tab" data-bs-target="#lang-en" type="button" role="tab" aria-controls="lang-en" aria-selected="true">
                                <i class="fas fa-language me-1"></i>{{ __('English') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lang-ar-tab" data-bs-toggle="tab" data-bs-target="#lang-ar" type="button" role="tab" aria-controls="lang-ar" aria-selected="false">
                                <i class="fas fa-language me-1"></i>{{ __('Arabic') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="langTabsContent">
                        <!-- English Tab -->
                        <div class="tab-pane fade show active" id="lang-en" role="tabpanel" aria-labelledby="lang-en-tab">
                            <!-- Basic Information Section -->
                            <div class="mb-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>{{ __('Basic Information') }}
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="name_en" class="form-label fw-semibold">{{ __('Product Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror" 
                                           id="name_en" name="name_en" value="{{ old('name_en', $product->name['en'] ?? '') }}" 
                                           placeholder="{{ __('Enter product name in English') }}" required>
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description_en" class="form-label fw-semibold">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description_en') is-invalid @enderror" 
                                              id="description_en" name="description_en" rows="4"
                                              placeholder="{{ __('Enter product description in English') }}">{{ old('description_en', $product->description['en'] ?? '') }}</textarea>
                                    @error('description_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Arabic Tab -->
                        <div class="tab-pane fade" id="lang-ar" role="tabpanel" aria-labelledby="lang-ar-tab">
                            <!-- Basic Information Section -->
                            <div class="mb-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>{{ __('Basic Information') }}
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label fw-semibold">{{ __('Product Name') }}</label>
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" 
                                           id="name_ar" name="name_ar" value="{{ old('name_ar', $product->name['ar'] ?? '') }}"
                                           placeholder="{{ __('Enter product name in Arabic') }}">
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description_ar" class="form-label fw-semibold">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                                              id="description_ar" name="description_ar" rows="4"
                                              placeholder="{{ __('Enter product description in Arabic') }}">{{ old('description_ar', $product->description['ar'] ?? '') }}</textarea>
                                    @error('description_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Product Details Section -->
                    <div class="mb-4">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-tags me-2"></i>{{ __('Product Details') }}
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku" class="form-label fw-semibold">{{ __('SKU') }}</label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
                                           placeholder="{{ __('Stock Keeping Unit (Optional)') }}">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-semibold">{{ __('Category') }}</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                        <option value="">{{ __('Select Category (Optional)') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->localized_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label fw-semibold">{{ __('Price') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">SAR</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $product->price) }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="compare_price" class="form-label fw-semibold">{{ __('Compare Price') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">SAR</span>
                                        <input type="number" class="form-control @error('compare_price') is-invalid @enderror" 
                                               id="compare_price" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" 
                                               step="0.01" min="0">
                                    </div>
                                    @error('compare_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label fw-semibold">{{ __('Sort Order') }}</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" 
                                           min="0" step="1">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Stock Management Section -->
                    <div class="mb-4">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-warehouse me-2"></i>{{ __('Stock Management') }}
                        </h5>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="track_stock" name="track_stock" value="1" 
                                               {{ old('track_stock', $product->track_stock) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="track_stock">
                                            {{ __('Track Stock') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label fw-semibold">{{ __('Stock Quantity') }}</label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                           min="0" step="1">
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="in_stock" name="in_stock" value="1" 
                                               {{ old('in_stock', $product->in_stock) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="in_stock">
                                            {{ __('In Stock') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Media Section -->
                    <div class="mb-4">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-image me-2"></i>{{ __('Product Images') }}
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-semibold">{{ __('Main Image') }}</label>
                                    @if($product->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 alt="{{ $product->localized_name }}" 
                                                 class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                            <p class="text-muted small mt-1">{{ __('Current image') }}</p>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*"
                                           onchange="previewImage(this, 'mainPreview')">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="mainPreview" class="mt-2" style="display: none;">
                                        <img id="mainPreviewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="images" class="form-label fw-semibold">{{ __('Additional Images') }}</label>
                                    @if($product->images && count($product->images) > 0)
                                        <div class="mb-2 d-flex flex-wrap gap-2">
                                            @foreach($product->images as $img)
                                                <img src="{{ asset('storage/' . $img) }}" 
                                                     alt="{{ $product->localized_name }}" 
                                                     class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                            @endforeach
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                           id="images" name="images[]" accept="image/*" multiple
                                           onchange="previewMultipleImages(this)">
                                    @error('images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="imagesPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Settings Section -->
                    <div class="mb-4">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-cog me-2"></i>{{ __('Settings') }}
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        <option value="out_of_stock" {{ old('status', $product->status) == 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_featured">
                                            <i class="fas fa-star me-1 text-warning"></i>{{ __('Mark as Featured') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('merchant.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>{{ __('Update Product') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const previewImg = document.getElementById(previewId + 'Img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }

    function previewMultipleImages(input) {
        const preview = document.getElementById('imagesPreview');
        preview.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endpush
@endsection

