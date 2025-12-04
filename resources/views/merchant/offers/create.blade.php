@extends('layouts.dashboard')

@section('title', __('Create Offer'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('merchant.offers.index') }}">{{ __('Offers') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Create New Offer') }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('merchant.offers.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                        <ul class="nav nav-tabs" id="titleTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="title-en-tab" data-bs-toggle="tab" data-bs-target="#title-en" type="button" role="tab" aria-controls="title-en" aria-selected="true">{{ __('English') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="title-ar-tab" data-bs-toggle="tab" data-bs-target="#title-ar" type="button" role="tab" aria-controls="title-ar" aria-selected="false">{{ __('Arabic') }}</button>
                            </li>
                        </ul>
                        <div class="tab-content border border-top-0 p-3" id="titleTabsContent">
                            <div class="tab-pane fade show active" id="title-en" role="tabpanel" aria-labelledby="title-en-tab">
                                <input type="text" class="form-control @error('title_en') is-invalid @enderror" 
                                       id="title_en" name="title_en" value="{{ old('title_en') }}" required>
                                @error('title_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="tab-pane fade" id="title-ar" role="tabpanel" aria-labelledby="title-ar-tab">
                                <input type="text" class="form-control @error('title_ar') is-invalid @enderror" 
                                       id="title_ar" name="title_ar" value="{{ old('title_ar') }}">
                                @error('title_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <ul class="nav nav-tabs" id="descriptionTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-en-tab" data-bs-toggle="tab" data-bs-target="#description-en" type="button" role="tab" aria-controls="description-en" aria-selected="true">{{ __('English') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="description-ar-tab" data-bs-toggle="tab" data-bs-target="#description-ar" type="button" role="tab" aria-controls="description-ar" aria-selected="false">{{ __('Arabic') }}</button>
                            </li>
                        </ul>
                        <div class="tab-content border border-top-0 p-3" id="descriptionTabsContent">
                            <div class="tab-pane fade show active" id="description-en" role="tabpanel" aria-labelledby="description-en-tab">
                                <textarea class="form-control @error('description_en') is-invalid @enderror" 
                                          id="description_en" name="description_en" rows="3">{{ old('description_en') }}</textarea>
                                @error('description_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="tab-pane fade" id="description-ar" role="tabpanel" aria-labelledby="description-ar-tab">
                                <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                                          id="description_ar" name="description_ar" rows="3">{{ old('description_ar') }}</textarea>
                                @error('description_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">{{ __('Offer Type') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>{{ __('Percentage Discount') }}</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount Discount') }}</option>
                                    <option value="free_shipping" {{ old('type') == 'free_shipping' ? 'selected' : '' }}>{{ __('Free Shipping') }}</option>
                                    <option value="buy_x_get_y" {{ old('type') == 'buy_x_get_y' ? 'selected' : '' }}>{{ __('Buy X Get Y') }}</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">{{ __('Category') }}</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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

                    <div class="row" id="discountFields">
                        <div class="col-md-6" id="percentageField" style="display: none;">
                            <div class="mb-3">
                                <label for="discount_percentage" class="form-label">{{ __('Discount Percentage') }} (%)</label>
                                <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                       id="discount_percentage" name="discount_percentage" 
                                       value="{{ old('discount_percentage') }}" step="0.01" min="0" max="100">
                                @error('discount_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6" id="fixedField" style="display: none;">
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label">{{ __('Discount Amount') }}</label>
                                <input type="number" class="form-control @error('discount_amount') is-invalid @enderror" 
                                       id="discount_amount" name="discount_amount" 
                                       value="{{ old('discount_amount') }}" step="0.01" min="0">
                                @error('discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_purchase" class="form-label">{{ __('Minimum Purchase') }}</label>
                                <input type="number" class="form-control @error('minimum_purchase') is-invalid @enderror" 
                                       id="minimum_purchase" name="minimum_purchase" 
                                       value="{{ old('minimum_purchase') }}" step="0.01" min="0">
                                @error('minimum_purchase')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">{{ __('Image') }}</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                {{ __('Mark as Featured') }}
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('merchant.offers.index') }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
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
    });
    
    // Trigger on page load
    document.getElementById('type').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection

