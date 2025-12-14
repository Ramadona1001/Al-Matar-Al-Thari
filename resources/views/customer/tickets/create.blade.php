@extends('layouts.dashboard')

@section('title', __('Create Ticket'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.tickets.index') }}">{{ __('Tickets') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Create New Ticket') }}</h6>
            </div>
            <div class="card-body">
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

                <form method="POST" action="{{ route('customer.tickets.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="category" class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">{{ __('Select Category') }}</option>
                            <option value="service_not_delivered" {{ old('category') == 'service_not_delivered' ? 'selected' : '' }}>{{ __('Service Not Delivered') }}</option>
                            <option value="payment_issue" {{ old('category') == 'payment_issue' ? 'selected' : '' }}>{{ __('Payment Issue') }}</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="company_id" class="form-label">{{ __('Company') }}</label>
                        <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror">
                            <option value="">{{ __('Select Company (Optional)') }}</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->localized_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="service_id" class="form-label">{{ __('Service') }}</label>
                        <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror">
                            <option value="">{{ __('Select Service (Optional)') }}</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->localized_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">{{ __('Subject') }} <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" 
                               value="{{ old('subject') }}" required maxlength="255">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="6" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">{{ __('Please provide detailed information about your issue') }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="attachments" class="form-label">{{ __('Attachments') }}</label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control @error('attachments.*') is-invalid @enderror" 
                               multiple accept="image/*,application/pdf,video/*">
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            {{ __('You can upload up to 5 files (Images, PDF, or Video). Max size: 10MB per file') }}
                        </small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('customer.tickets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Submit Ticket') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview attachments
    document.getElementById('attachments').addEventListener('change', function(e) {
        const files = e.target.files;
        if (files.length > 5) {
            alert('{{ __("You can only upload up to 5 files") }}');
            e.target.value = '';
            return;
        }
        
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 10 * 1024 * 1024) {
                alert('{{ __("File size must be less than 10MB") }}: ' + files[i].name);
                e.target.value = '';
                return;
            }
        }
    });
</script>
@endsection

