@extends('layouts.dashboard')

@section('title', __('Edit Offer'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.offers.index') }}">{{ __('Offers') }}</a></li>
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
                    <i class="fas fa-edit me-2"></i>{{ __('Edit Offer') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.offers.update', $offer) }}" enctype="multipart/form-data">
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
                                    <label for="title_en" class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title_en') is-invalid @enderror" 
                                           id="title_en" name="title_en" value="{{ old('title_en', $offer->title['en'] ?? '') }}" 
                                           placeholder="{{ __('Enter offer title in English') }}" required>
                                    @error('title_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description_en" class="form-label fw-semibold">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description_en') is-invalid @enderror" 
                                              id="description_en" name="description_en" rows="4"
                                              placeholder="{{ __('Enter offer description in English') }}">{{ old('description_en', $offer->description['en'] ?? '') }}</textarea>
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
                                    <label for="title_ar" class="form-label fw-semibold">{{ __('Title') }}</label>
                                    <input type="text" class="form-control @error('title_ar') is-invalid @enderror" 
                                           id="title_ar" name="title_ar" value="{{ old('title_ar', $offer->title['ar'] ?? '') }}"
                                           placeholder="{{ __('Enter offer title in Arabic') }}">
                                    @error('title_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description_ar" class="form-label fw-semibold">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                                              id="description_ar" name="description_ar" rows="4"
                                              placeholder="{{ __('Enter offer description in Arabic') }}">{{ old('description_ar', $offer->description['ar'] ?? '') }}</textarea>
                                    @error('description_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Offer Details Section -->
                    <div class="mb-4">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-tags me-2"></i>{{ __('Offer Details') }}
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_id" class="form-label fw-semibold">{{ __('Company') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                                        <option value="">{{ __('Select Company') }}</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id', $offer->company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->localized_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
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
                                            <option value="{{ $category->id }}" {{ old('category_id', $offer->category_id) == $category->id ? 'selected' : '' }}>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label fw-semibold">{{ __('Product') }}</label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
                                        <option value="">{{ __('Select Product (Optional)') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id', $offer->product_id) == $product->id ? 'selected' : '' }}>
                                                {{ $product->localized_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label fw-semibold">{{ __('Offer Type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="percentage" {{ old('type', $offer->type) == 'percentage' ? 'selected' : '' }}>{{ __('Percentage Discount') }}</option>
                                        <option value="fixed" {{ old('type', $offer->type) == 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount Discount') }}</option>
                                        <option value="free_shipping" {{ old('type', $offer->type) == 'free_shipping' ? 'selected' : '' }}>{{ __('Free Shipping') }}</option>
                                        <option value="buy_x_get_y" {{ old('type', $offer->type) == 'buy_x_get_y' ? 'selected' : '' }}>{{ __('Buy X Get Y') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row" id="discountFields">
                            <div class="col-md-6" id="percentageField" style="display: {{ old('type', $offer->type) == 'percentage' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label fw-semibold">{{ __('Discount Percentage') }} (%)</label>
                                    <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                           id="discount_percentage" name="discount_percentage" 
                                           value="{{ old('discount_percentage', $offer->discount_percentage) }}" step="0.01" min="0" max="100">
                                    @error('discount_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="fixedField" style="display: {{ old('type', $offer->type) == 'fixed' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="discount_amount" class="form-label fw-semibold">{{ __('Discount Amount') }}</label>
                                    <input type="number" class="form-control @error('discount_amount') is-invalid @enderror" 
                                           id="discount_amount" name="discount_amount" 
                                           value="{{ old('discount_amount', $offer->discount_amount) }}" step="0.01" min="0">
                                    @error('discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="minimum_purchase" class="form-label fw-semibold">{{ __('Minimum Purchase') }}</label>
                                    <input type="number" class="form-control @error('minimum_purchase') is-invalid @enderror" 
                                           id="minimum_purchase" name="minimum_purchase" 
                                           value="{{ old('minimum_purchase', $offer->minimum_purchase) }}" step="0.01" min="0">
                                    @error('minimum_purchase')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label fw-semibold">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', $offer->start_date ? $offer->start_date->format('Y-m-d') : '') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label fw-semibold">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $offer->end_date ? $offer->end_date->format('Y-m-d') : '') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Media & Settings Section -->
                    <div class="mb-4">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-image me-2"></i>{{ __('Media & Settings') }}
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-semibold">{{ __('Offer Image') }}</label>
                                    @if($offer->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $offer->image) }}" 
                                                 alt="{{ $offer->localized_title }}" 
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
                                    <small class="form-text text-muted">{{ __('Recommended size: 800x600px. Max size: 2MB') }}</small>
                                    <div id="mainPreview" class="mt-2" style="display: none;">
                                        <img id="mainPreviewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="draft" {{ old('status', $offer->status) == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                        <option value="active" {{ old('status', $offer->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ old('status', $offer->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        <option value="expired" {{ old('status', $offer->status) == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                        <option value="paused" {{ old('status', $offer->status) == 'paused' ? 'selected' : '' }}>{{ __('Paused') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                                       {{ old('is_featured', $offer->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_featured">
                                    <i class="fas fa-star me-1 text-warning"></i>{{ __('Mark as Featured') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>{{ __('Update Offer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide discount fields based on type
    document.getElementById('type').addEventListener('change', function() {
        const type = this.value;
        document.getElementById('percentageField').style.display = type === 'percentage' ? 'block' : 'none';
        document.getElementById('fixedField').style.display = type === 'fixed' ? 'block' : 'none';
        
        const discountFieldsRow = document.getElementById('discountFields');
        if (type === 'percentage' || type === 'fixed') {
            discountFieldsRow.style.display = 'flex';
        } else {
            discountFieldsRow.style.display = 'none';
        }
    });
    
    // Image preview function
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
    
    // Date validation
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const endDateInput = document.getElementById('end_date');
        
        if (endDateInput.value) {
            const endDate = new Date(endDateInput.value);
            if (endDate <= startDate) {
                alert('{{ __("End date must be after start date") }}');
                endDateInput.value = '';
            }
        }
        
        const nextDay = new Date(startDate);
        nextDay.setDate(nextDay.getDate() + 1);
        endDateInput.min = nextDay.toISOString().split('T')[0];
    });
    
    document.getElementById('end_date').addEventListener('change', function() {
        const startDateInput = document.getElementById('start_date');
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(this.value);
        
        if (endDate <= startDate) {
            alert('{{ __("End date must be after start date") }}');
            this.value = '';
        }
    });
</script>
@endpush
@endsection

