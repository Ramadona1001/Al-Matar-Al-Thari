@extends('layouts.auth')

@php
    try {
        $site = \App\Models\SiteSetting::getSettings();
    } catch (\Exception $e) {
        $site = new \App\Models\SiteSetting();
    }

    $currentLocale = app()->getLocale();
    $brandName = is_array($site->brand_name ?? null)
        ? ($site->brand_name[$currentLocale] ?? reset($site->brand_name ?? []))
        : ($site->brand_name ?? config('app.name'));
@endphp

@section('meta_title', __('Login'))
@section('meta_description', __('Sign in to your account'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endsection

@section('content')
    <!-- Login Section -->
    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-wrapper">
                <!-- Left Side - Branding (Desktop Only) -->
                <div class="auth-branding d-none d-lg-flex">
                    <div class="branding-content">
                        <a href="{{ route('public.home', ['locale' => app()->getLocale()]) }}" class="brand-logo-link">
                            <div class="brand-logo">
                                @if(!empty($site->logo_path))
                                    <img src="{{ asset('storage/'.$site->logo_path) }}" alt="{{ $brandName }}" >
                                @else
                                    <i class="bi bi-cloud-rain"></i>
                                @endif
                            </div>
                        </a>
                        <h1 class="brand-title">{{ __('Welcome Back') }}</h1>
                        <p class="brand-subtitle">{{ __('Sign in to continue to your account and access all features') }}</p>
                        <div class="brand-features">
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>{{ __('Secure & Safe') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-bolt"></i>
                                <span>{{ __('Fast Access') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-mobile-alt"></i>
                                <span>{{ __('Mobile Friendly') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="branding-pattern"></div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="auth-form-wrapper">
                    <div class="auth-form-card">
                        <!-- Header -->
                        <div class="auth-header">
                            <div class="auth-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <h2 class="auth-title">{{ __('Login') }}</h2>
                            <p class="auth-subtitle">{{ __('Sign in to your account') }}</p>
                        </div>

                        <!-- Session Status -->
                        @if(session('status'))
                            <div class="alert alert-success-custom" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login', ['locale' => app()->getLocale()]) }}" class="auth-form" >
                            @csrf

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
                                           value="{{ old('email') }}" 
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
                                    {{ __('Password') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-input @error('password') is-invalid @enderror" 
                                           placeholder="{{ __('Enter your password') }}" 
                                           required 
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

                            <!-- Remember Me & Forgot Password -->
                            <div class="form-options">
                                <label class="checkbox-wrapper">
                                    <input type="checkbox" id="remember_me" name="remember" class="checkbox-input">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-label">{{ __('Remember me') }}</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request', ['locale' => app()->getLocale()]) }}" class="forgot-link">
                                        <i class="fas fa-key me-1"></i>
                                        {{ __('Forgot password?') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="auth-submit-btn">
                                <span class="btn-content">
                                    <span class="btn-text">{{ __('Log in') }}</span>
                                    <i class="fas fa-arrow-right btn-arrow"></i>
                                </span>
                                <span class="btn-loader">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>

                            <!-- Divider -->
                            <div class="auth-divider">
                                <span class="divider-line"></span>
                                <span class="divider-text">{{ __('Or continue with') }}</span>
                                <span class="divider-line"></span>
                            </div>

                            <!-- Social Login -->
                            <div class="social-login">
                                <button type="button" class="social-btn social-google" disabled>
                                    <i class="fab fa-google"></i>
                                    <span>{{ __('Google') }}</span>
                                </button>
                                <button type="button" class="social-btn social-facebook" disabled>
                                    <i class="fab fa-facebook-f"></i>
                                    <span>{{ __('Facebook') }}</span>
                                </button>
                            </div>

                            <!-- Register Link -->
                            <div class="auth-footer">
                                <p class="footer-text">
                                    {{ __("Don't have an account?") }}
                                    <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="footer-link">
                                        {{ __('Create account') }}
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    @endpush

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
            const form = document.getElementById('loginForm');
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
