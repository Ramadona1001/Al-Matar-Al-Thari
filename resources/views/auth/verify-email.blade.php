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

@section('meta_title', __('Verify Email'))
@section('meta_description', __('Verify your email address'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endsection

@section('content')
    <!-- Verify Email Section -->
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
                                    <i class="fas fa-envelope-circle-check"></i>
                                @endif
                            </div>
                        </a>
                        <h1 class="brand-title">{{ __('Verify Your Email') }}</h1>
                        <p class="brand-subtitle">{{ __("We've sent you a verification link. Please check your email and click the link to verify your account.") }}</p>
                        <div class="brand-features">
                            <div class="feature-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ __('Check Email') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-link"></i>
                                <span>{{ __('Click Link') }}</span>
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
                                <i class="fas fa-envelope-circle-check"></i>
                            </div>
                            <h2 class="auth-title">{{ __('Verify Your Email') }}</h2>
                            <p class="auth-subtitle">{{ __('Please verify your email address') }}</p>
                        </div>

                        <!-- Info Message -->
                        <div class="info-message">
                            <i class="fas fa-info-circle"></i>
                            <p>{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
                        </div>

                        <!-- Success Message -->
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success-custom" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="verify-actions">
                            <!-- Resend Verification Email -->
                            <form method="POST" action="{{ route('verification.send', ['locale' => app()->getLocale()]) }}" class="verify-form">
                                @csrf
                                <button type="submit" class="auth-submit-btn">
                                    <span class="btn-content">
                                        <span class="btn-text">{{ __('Resend Verification Email') }}</span>
                                        <i class="fas fa-paper-plane btn-arrow"></i>
                                    </span>
                                    <span class="btn-loader">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </form>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}" class="verify-form">
                                @csrf
                                <button type="submit" class="auth-logout-btn">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>

                        <!-- Back to Login -->
                        <div class="auth-footer">
                            <p class="footer-text">
                                {{ __('Need help?') }}
                                <a href="{{ route('public.contact', ['locale' => app()->getLocale()]) }}" class="footer-link">
                                    {{ __('Contact Support') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission loading state
            const forms = document.querySelectorAll('.verify-form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('.auth-submit-btn, .auth-logout-btn');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
