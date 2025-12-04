@extends('layouts.dashboard')

@section('title', __('Edit Company'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">{{ __('Companies') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Edit Company') }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.companies.update', $company) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Company Name') }} <span class="text-danger">*</span></label>
                                <ul class="nav nav-tabs" id="nameTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="name-en-tab" data-bs-toggle="tab" data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en" aria-selected="true">{{ __('English') }}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="name-ar-tab" data-bs-toggle="tab" data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar" aria-selected="false">{{ __('Arabic') }}</button>
                                    </li>
                                </ul>
                                <div class="tab-content border border-top-0 p-3" id="nameTabsContent">
                                    <div class="tab-pane fade show active" id="name-en" role="tabpanel" aria-labelledby="name-en-tab">
                                        <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en" name="name_en" value="{{ old('name_en', $company->name['en'] ?? (is_string($company->getRawOriginal('name')) ? $company->getRawOriginal('name') : '')) }}" required>
                                        @error('name_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="tab-pane fade" id="name-ar" role="tabpanel" aria-labelledby="name-ar-tab">
                                        <input type="text" class="form-control @error('name_ar') is-invalid @enderror" id="name_ar" name="name_ar" value="{{ old('name_ar', $company->name['ar'] ?? '') }}">
                                        @error('name_ar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $company->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">{{ __('Merchant') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id" required>
                                    <option value="">{{ __('Select Merchant') }}</option>
                                    @foreach($merchants as $merchant)
                                        <option value="{{ $merchant->id }}" {{ old('user_id', $company->user_id) == $merchant->id ? 'selected' : '' }}>
                                            {{ $merchant->full_name }} ({{ $merchant->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="network_id" class="form-label">{{ __('Network') }}</label>
                                <select class="form-select @error('network_id') is-invalid @enderror"
                                        id="network_id" name="network_id">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($networks as $network)
                                        <option value="{{ $network->id }}" {{ old('network_id', $company->network_id) == $network->id ? 'selected' : '' }}>
                                            {{ $network->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('network_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="can_display_cards_on_homepage" name="can_display_cards_on_homepage" value="1" {{ old('can_display_cards_on_homepage', $company->can_display_cards_on_homepage) ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_display_cards_on_homepage">{{ __('Can display cards on homepage') }}</label>
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
                                <textarea class="form-control @error('description_en') is-invalid @enderror" id="description_en" name="description_en" rows="3">{{ old('description_en', $company->description['en'] ?? (is_string($company->getRawOriginal('description')) ? $company->getRawOriginal('description') : '')) }}</textarea>
                                @error('description_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="tab-pane fade" id="description-ar" role="tabpanel" aria-labelledby="description-ar-tab">
                                <textarea class="form-control @error('description_ar') is-invalid @enderror" id="description_ar" name="description_ar" rows="3">{{ old('description_ar', $company->description['ar'] ?? '') }}</textarea>
                                @error('description_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="website" class="form-label">{{ __('Website') }}</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website', $company->website) }}">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="logo" class="form-label">{{ __('Logo') }}</label>
                                @if($company->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->localized_name }}" 
                                             class="img-thumbnail" style="max-width: 100px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                       id="logo" name="logo" accept="image/*">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('Address') }}</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address', $company->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="city" class="form-label">{{ __('City') }}</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', $company->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="country" class="form-label">{{ __('Country') }}</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" value="{{ old('country', $company->country) }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tax_number" class="form-label">{{ __('Tax Number') }}</label>
                                <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                       id="tax_number" name="tax_number" value="{{ old('tax_number', $company->tax_number) }}">
                                @error('tax_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="commercial_register" class="form-label">{{ __('Commercial Register') }}</label>
                                <input type="text" class="form-control @error('commercial_register') is-invalid @enderror" 
                                       id="commercial_register" name="commercial_register" value="{{ old('commercial_register', $company->commercial_register) }}">
                                @error('commercial_register')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="affiliate_commission_rate" class="form-label">{{ __('Affiliate Commission Rate (%)') }}</label>
                                <input type="number" class="form-control @error('affiliate_commission_rate') is-invalid @enderror" 
                                       id="affiliate_commission_rate" name="affiliate_commission_rate" 
                                       value="{{ old('affiliate_commission_rate', $company->affiliate_commission_rate) }}" step="0.01" min="0" max="100">
                                @error('affiliate_commission_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="pending" {{ old('status', $company->status) == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="approved" {{ old('status', $company->status) == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                            <option value="rejected" {{ old('status', $company->status) == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('Update Company') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

