<div class="alert alert-warning border-warning mb-4">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>{{ __('Warning:') }}</strong> {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
</div>

<button type="button" 
        class="btn btn-danger btn-animated" 
        data-bs-toggle="modal" 
        data-bs-target="#confirmUserDeletionModal">
    <i class="fas fa-trash me-2"></i>{{ __('Delete Account') }}
</button>

<!-- Modal -->
<div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger" id="confirmUserDeletionModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('Delete Account') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <p class="mb-3">{{ __('Are you sure you want to delete your account?') }}</p>
                    <p class="text-muted small mb-3">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">{{ __('Password') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" 
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="{{ __('Enter your password') }}"
                                   required>
                        </div>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger btn-animated">
                        <i class="fas fa-trash me-2"></i>{{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
