<p class="text-muted mb-4">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>

<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="row g-4">
        <div class="col-12">
            <label for="update_password_current_password" class="form-label fw-semibold">{{ __('Current Password') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" 
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                       id="update_password_current_password" 
                       name="current_password" 
                       autocomplete="current-password"
                       required>
            </div>
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="update_password_password" class="form-label fw-semibold">{{ __('New Password') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
                <input type="password" 
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                       id="update_password_password" 
                       name="password" 
                       autocomplete="new-password"
                       required>
            </div>
            @error('password', 'updatePassword')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="update_password_password_confirmation" class="form-label fw-semibold">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
                <input type="password" 
                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                       id="update_password_password_confirmation" 
                       name="password_confirmation" 
                       autocomplete="new-password"
                       required>
            </div>
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-primary btn-animated">
                    <i class="fas fa-save me-2"></i>{{ __('Update Password') }}
                </button>
            </div>
        </div>
    </div>
</form>
