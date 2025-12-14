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

@section('meta_title', __('Forgot Password'))
@section('meta_description', __('Reset your password'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endsection

@section('content')
    <!-- Forgot Password Section -->
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
                                    <i class="fas fa-key"></i>
                                @endif
                            </div>
                        </a>
                        <h1 class="brand-title">{{ __('Reset Password') }}</h1>
                        <p class="brand-subtitle">{{ __("No worries! Enter your email address and we'll send you a link to reset your password.") }}</p>
                        <div class="brand-features">
                            <div class="feature-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ __('Check Your Email') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>{{ __('Secure Process') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ __('Quick Recovery') }}</span>
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
                                <i class="fas fa-key"></i>
                            </div>
                            <h2 class="auth-title">{{ __('Forgot Password') }}</h2>
                            <p class="auth-subtitle">{{ __('Enter your email to receive a password reset link') }}</p>
                        </div>

                        <!-- Session Status -->
                        @if(session('status'))
                            <div class="alert alert-success-custom" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <!-- Info Message -->
                        <div class="info-message">
                            <i class="fas fa-info-circle"></i>
                            <p>{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
                        </div>

                        <!-- Forgot Password Form -->
                        <form method="POST" action="{{ route('password.email', ['locale' => app()->getLocale()]) }}" class="auth-form" id="forgotPasswordForm">
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
                                           placeholder="{{ __('Enter your email address') }}" 
                                           required 
                                           autofocus 
                                           autocomplete="email">
                                    <span class="input-focus-line"></span>
                                </div>
                                @error('email')
                                    <div class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="auth-submit-btn">
                                <span class="btn-content">
                                    <span class="btn-text">{{ __('Send Reset Link') }}</span>
                                    <i class="fas fa-paper-plane btn-arrow"></i>
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
            const form = document.getElementById('forgotPasswordForm');
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
