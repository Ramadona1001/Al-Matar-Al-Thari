@extends('layouts.new-design')

@section('meta_title', __('Confirm Password'))
@section('meta_description', __('Confirm your password to continue'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endsection

@section('content')
    <!-- Confirm Password Section -->
    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-wrapper">
                <!-- Left Side - Branding (Desktop Only) -->
                <div class="auth-branding d-none d-lg-flex">
                    <div class="branding-content">
                        <div class="brand-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h1 class="brand-title">{{ __('Security Check') }}</h1>
                        <p class="brand-subtitle">{{ __('Please confirm your password to access this secure area of the application') }}</p>
                        <div class="brand-features">
                            <div class="feature-item">
                                <i class="fas fa-lock"></i>
                                <span>{{ __('Secure Area') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-user-shield"></i>
                                <span>{{ __('Protected') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ __('Verified') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="branding-pattern"></div>
                </div>

                <!-- Right Side - Form -->
                <div class="auth-form-wrapper">
                    <div class="auth-form-card">
                        <!-- Header -->
                        <div class="auth-header">
                            <div class="auth-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h2 class="auth-title">{{ __('Confirm Password') }}</h2>
                            <p class="auth-subtitle">{{ __('Please confirm your password before continuing') }}</p>
                        </div>

                        <!-- Info Message -->
                        <div class="info-message">
                            <i class="fas fa-info-circle"></i>
                            <p>{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
                        </div>

                        <!-- Confirm Password Form -->
                        <form method="POST" action="{{ route('password.confirm', ['locale' => app()->getLocale()]) }}" class="auth-form" id="confirmPasswordForm">
                            @csrf

                            <!-- Password Field -->
                            <div class="form-field">
                                <label for="password" class="field-label">
                                    <i class="fas fa-lock field-label-icon"></i>
                                    {{ __('Password') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-input @error('password') is-invalid @enderror" 
                                           placeholder="{{ __('Enter your password') }}" 
                                           required 
                                           autofocus 
                                           autocomplete="current-password">
                                    <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <span class="input-focus-line"></span>
                                </div>
                                @error('password')
                                    <div class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="auth-submit-btn">
                                <span class="btn-content">
                                    <span class="btn-text">{{ __('Confirm') }}</span>
                                    <i class="fas fa-arrow-right btn-arrow"></i>
                                </span>
                                <span class="btn-loader">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }

            // Form submission loading state
            const form = document.getElementById('confirmPasswordForm');
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('.auth-submit-btn');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                    }
                });
            }
        });
    </script>
    @endpush
@endsection
