@extends('layouts.dashboard')

@section('title', __('Create Offer'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('merchant.offers.index') }}">{{ __('Offers') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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
                    <i class="fas fa-plus-circle me-2"></i>{{ __('Create New Offer') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('merchant.offers.store') }}" enctype="multipart/form-data">
                    @csrf

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
                                           id="title_en" name="title_en" value="{{ old('title_en') }}" 
                                           placeholder="{{ __('Enter offer title in English') }}" required>
                                    @error('title_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description_en" class="form-label fw-semibold">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description_en') is-invalid @enderror" 
                                              id="description_en" name="description_en" rows="4"
                                              placeholder="{{ __('Enter offer description in English') }}">{{ old('description_en') }}</textarea>
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
                                           id="title_ar" name="title_ar" value="{{ old('title_ar') }}"
                                           placeholder="{{ __('Enter offer title in Arabic') }}">
                                    @error('title_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description_ar" class="form-label fw-semibold">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                                              id="description_ar" name="description_ar" rows="4"
                                              placeholder="{{ __('Enter offer description in Arabic') }}">{{ old('description_ar') }}</textarea>
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
                                    <label for="type" class="form-label fw-semibold">{{ __('Offer Type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>{{ __('Percentage Discount') }}</option>
                                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount Discount') }}</option>
                                        <option value="free_shipping" {{ old('type') == 'free_shipping' ? 'selected' : '' }}>{{ __('Free Shipping') }}</option>
                                        <option value="buy_x_get_y" {{ old('type') == 'buy_x_get_y' ? 'selected' : '' }}>{{ __('Buy X Get Y') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Select the type of discount for this offer') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-semibold">{{ __('Category') }}</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                        <option value="">{{ __('Select Category (Optional)') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->localized_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Categorize this offer for better organization') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="discountFields" style="display: none;">
                            <div class="col-md-6" id="percentageField" style="display: none;">
                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label fw-semibold">{{ __('Discount Percentage') }} (%) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                               id="discount_percentage" name="discount_percentage" 
                                               value="{{ old('discount_percentage') }}" step="0.01" min="0" max="100"
                                               placeholder="0.00">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('discount_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Enter discount percentage (0-100)') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6" id="fixedField" style="display: none;">
                                <div class="mb-3">
                                    <label for="discount_amount" class="form-label fw-semibold">{{ __('Discount Amount') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">SAR</span>
                                        <input type="number" class="form-control @error('discount_amount') is-invalid @enderror" 
                                               id="discount_amount" name="discount_amount" 
                                               value="{{ old('discount_amount') }}" step="0.01" min="0"
                                               placeholder="0.00">
                                    </div>
                                    @error('discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Enter fixed discount amount in SAR') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="minimum_purchase" class="form-label fw-semibold">{{ __('Minimum Purchase') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">SAR</span>
                                        <input type="number" class="form-control @error('minimum_purchase') is-invalid @enderror" 
                                               id="minimum_purchase" name="minimum_purchase" 
                                               value="{{ old('minimum_purchase') }}" step="0.01" min="0"
                                               placeholder="0.00">
                                    </div>
                                    @error('minimum_purchase')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Minimum purchase amount to apply this offer') }}</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label fw-semibold">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('When the offer becomes active') }}</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label fw-semibold">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('When the offer expires') }}</small>
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
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*"
                                           onchange="previewImage(this)">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Recommended size: 800x600px. Max size: 2MB') }}</small>
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('Draft: Not visible to customers. Active: Visible and available. Inactive: Hidden from customers.') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                                       {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_featured">
                                    <i class="fas fa-star me-1 text-warning"></i>{{ __('Mark as Featured') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">{{ __('Featured offers will be highlighted and shown prominently') }}</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('merchant.offers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>{{ __('Create Offer') }}
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
        
        // Show/hide discount fields row
        const discountFieldsRow = document.getElementById('discountFields');
        if (type === 'percentage' || type === 'fixed') {
            discountFieldsRow.style.display = 'flex';
        } else {
            discountFieldsRow.style.display = 'none';
        }
    });
    
    // Trigger on page load
    document.getElementById('type').dispatchEvent(new Event('change'));
    
    // Image preview function
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
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
    
    // Date validation: end date must be after start date
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
        
        // Set minimum date for end date
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

