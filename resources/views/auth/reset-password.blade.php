@extends('layouts.new-design')

@section('meta_title', __('Reset Password'))
@section('meta_description', __('Set a new password for your account'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endsection

@section('content')
    <!-- Reset Password Section -->
    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-wrapper">
                <!-- Left Side - Branding (Desktop Only) -->
                <div class="auth-branding d-none d-lg-flex">
                    <div class="branding-content">
                        <div class="brand-logo">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h1 class="brand-title">{{ __('Set New Password') }}</h1>
                        <p class="brand-subtitle">{{ __('Create a strong password to secure your account and protect your information') }}</p>
                        <div class="brand-features">
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>{{ __('Secure') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ __('Verified') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-user-shield"></i>
                                <span>{{ __('Protected') }}</span>
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
                                <i class="fas fa-lock"></i>
                            </div>
                            <h2 class="auth-title">{{ __('Reset Password') }}</h2>
                            <p class="auth-subtitle">{{ __('Enter your new password below') }}</p>
                        </div>

                        <!-- Reset Password Form -->
                        <form method="POST" action="{{ route('password.store', ['locale' => app()->getLocale()]) }}" class="auth-form" id="resetPasswordForm">
                            @csrf

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- Email Field -->
                            <div class="form-field">
                                <label for="email" class="field-label">
                                    <i class="fas fa-envelope field-label-icon"></i>
                                    {{ __('Email Address') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           class="form-input @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $request->email) }}" 
                                           placeholder="{{ __('Enter your email') }}" 
                                           required 
                                           autofocus 
                                           autocomplete="username">
                                    <span class="input-focus-line"></span>
                                </div>
                                @error('email')
                                    <div class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="form-field">
                                <label for="password" class="field-label">
                                    <i class="fas fa-lock field-label-icon"></i>
                                    {{ __('New Password') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-input @error('password') is-invalid @enderror" 
                                           placeholder="{{ __('Enter your new password') }}" 
                                           required 
                                           autocomplete="new-password">
                                    <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <span class="input-focus-line"></span>
                                </div>
                                <small class="field-hint">{{ __('Password must be at least 8 characters long') }}</small>
                                @error('password')
                                    <div class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="form-field">
                                <label for="password_confirmation" class="field-label">
                                    <i class="fas fa-lock field-label-icon"></i>
                                    {{ __('Confirm Password') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           class="form-input" 
                                           placeholder="{{ __('Confirm your new password') }}" 
                                           required 
                                           autocomplete="new-password">
                                    <button type="button" class="password-toggle-btn" id="togglePasswordConfirmation" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <span class="input-focus-line"></span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="auth-submit-btn">
                                <span class="btn-content">
                                    <span class="btn-text">{{ __('Reset Password') }}</span>
                                    <i class="fas fa-arrow-right btn-arrow"></i>
                                </span>
                                <span class="btn-loader">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>

                            <!-- Back to Login -->
                            <div class="auth-footer">
                                <p class="footer-text">
                                    {{ __('Remember your password?') }}
                                    <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="footer-link">
                                        {{ __('Back to login') }}
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggles
            function setupPasswordToggle(toggleId, inputId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                if (toggle && input) {
                    toggle.addEventListener('click', function() {
                        const icon = this.querySelector('i');
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    });
                }
            }

            setupPasswordToggle('togglePassword', 'password');
            setupPasswordToggle('togglePasswordConfirmation', 'password_confirmation');

            // Form submission loading state
            const form = document.getElementById('resetPasswordForm');
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
