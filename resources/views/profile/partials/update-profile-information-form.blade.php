<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="row g-4">
        <!-- Avatar Upload -->
        <div class="col-12">
            <label class="form-label fw-semibold">{{ __('Profile Picture') }}</label>
            <div class="d-flex align-items-center gap-4">
                <div class="avatar-preview-wrapper">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" 
                             alt="{{ $user->full_name }}" 
                             class="avatar-preview rounded-circle"
                             id="avatarPreview"
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;">
                    @else
                        <div class="avatar-preview rounded-circle d-flex align-items-center justify-content-center bg-light border"
                             id="avatarPreview"
                             style="width: 120px; height: 120px; border: 3px solid #e9ecef;">
                            <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <input type="file" 
                           class="form-control @error('avatar') is-invalid @enderror" 
                           id="avatar" 
                           name="avatar" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           onchange="previewAvatar(this)">
                    <small class="text-muted d-block mt-2">
                        {{ __('Accepted formats: JPG, PNG, GIF, WEBP. Max size: 2MB') }}
                    </small>
                    @error('avatar')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @if($user->avatar)
                        <button type="button" 
                                class="btn btn-sm btn-outline-danger mt-2" 
                                onclick="removeAvatar()">
                            <i class="fas fa-trash me-1"></i>{{ __('Remove Avatar') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <label for="first_name" class="form-label fw-semibold">{{ __('First Name') }} <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control @error('first_name') is-invalid @enderror" 
                   id="first_name" 
                   name="first_name" 
                   value="{{ old('first_name', $user->first_name) }}" 
                   required 
                   autofocus 
                   autocomplete="given-name">
            @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="last_name" class="form-label fw-semibold">{{ __('Last Name') }} <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control @error('last_name') is-invalid @enderror" 
                   id="last_name" 
                   name="last_name" 
                   value="{{ old('last_name', $user->last_name) }}" 
                   required 
                   autocomplete="family-name">
            @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label fw-semibold">{{ __('Email') }} <span class="text-danger">*</span></label>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $user->email) }}" 
                   required 
                   autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-muted small mb-2">
                        {{ __('Your email address is unverified.') }}
                    </p>
                    <button form="send-verification" type="button" class="btn btn-sm btn-outline-primary">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success small">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <label for="phone" class="form-label fw-semibold">{{ __('Phone') }}</label>
            <input type="text" 
                   class="form-control @error('phone') is-invalid @enderror" 
                   id="phone" 
                   name="phone" 
                   value="{{ old('phone', $user->phone) }}" 
                   autocomplete="tel">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                @php
                    $dashboardRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : (auth()->user()->isMerchant() ? 'merchant.dashboard' : 'customer.dashboard');
                @endphp
                <a href="{{ route($dashboardRoute) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-animated">
                    <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function previewAvatar(input) {
    const preview = document.getElementById('avatarPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace div with img
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = '{{ $user->full_name }}';
                img.className = 'avatar-preview rounded-circle';
                img.style.cssText = 'width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;';
                img.id = 'avatarPreview';
                preview.parentNode.replaceChild(img, preview);
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeAvatar() {
    if (confirm('{{ __("Are you sure you want to remove your profile picture?") }}')) {
        const input = document.getElementById('avatar');
        const preview = document.getElementById('avatarPreview');
        
        // Clear file input
        input.value = '';
        
        // Replace img with default div
        const div = document.createElement('div');
        div.className = 'avatar-preview rounded-circle d-flex align-items-center justify-content-center bg-light border';
        div.style.cssText = 'width: 120px; height: 120px; border: 3px solid #e9ecef;';
        div.id = 'avatarPreview';
        div.innerHTML = '<i class="fas fa-user fa-3x text-muted"></i>';
        preview.parentNode.replaceChild(div, preview);
        
        // Add hidden input to indicate removal
        const form = input.closest('form');
        let removeInput = form.querySelector('input[name="remove_avatar"]');
        if (!removeInput) {
            removeInput = document.createElement('input');
            removeInput.type = 'hidden';
            removeInput.name = 'remove_avatar';
            removeInput.value = '1';
            form.appendChild(removeInput);
        }
    }
}
</script>
