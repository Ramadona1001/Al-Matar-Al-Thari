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

@section('meta_title', __('Password Reset Success'))
@section('meta_description', __('Your password has been reset successfully'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endsection

@section('content')
    <!-- Password Reset Success Section -->
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
                                    <i class="fas fa-check-circle"></i>
                                @endif
                            </div>
                        </a>
                        <h1 class="brand-title">{{ __('Password Reset!') }}</h1>
                        <p class="brand-subtitle">{{ __('password_reset_success_message') }}</p>
                        <div class="brand-features">
                            <div class="feature-item">
                                <i class="fas fa-check"></i>
                                <span>{{ __('reset_complete') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>{{ __('Secure') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>{{ __('ready_to_login') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="branding-pattern"></div>
                </div>

                <!-- Right Side - Success Message -->
                <div class="auth-form-wrapper">
                    <div class="auth-form-card">
                        <!-- Success Icon -->
                        <div class="success-icon-wrapper">
                            <div class="success-icon-circle">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="auth-header text-center">
                            <h2 class="auth-title text-success">{{ __('password_reset_successful') }}</h2>
                            <p class="auth-subtitle">{{ __('password_reset_success_message') }}</p>
                        </div>

                        <!-- Success Message -->
                        <div class="success-message-box">
                            <div class="success-message-content">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <div>
                                    <h5 class="mb-1">{{ __('password_changed') }}</h5>
                                    <p class="mb-0 text-muted">{{ __('password_changed_message') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="verify-actions">
                            <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="auth-submit-btn">
                                <span class="btn-content">
                                    <span class="btn-text">{{ __('go_to_login') }}</span>
                                    <i class="fas fa-arrow-right btn-arrow"></i>
                                </span>
                            </a>
                        </div>

                        <!-- Additional Info -->
                        <div class="auth-footer text-center">
                            <p class="footer-text">
                                {{ __('need_help') }}
                                <a href="{{ route('public.contact', ['locale' => app()->getLocale()]) }}" class="footer-link">
                                    {{ __('contact_support') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        .success-icon-wrapper {
            text-align: center;
            margin-bottom: 30px;
        }
        .success-icon-circle {
            width: 120px;
            height: 120px;
            margin: 0 auto;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon-circle i {
            font-size: 60px;
            color: #ffffff;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        .success-message-box {
            background: #f0fdf4;
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
        }
        .success-message-content {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        .success-message-content i {
            font-size: 24px;
            margin-top: 2px;
        }
        .success-message-content h5 {
            color: #059669;
            font-weight: 600;
            margin: 0;
        }
        .verify-actions {
            margin-top: 30px;
        }
        .auth-submit-btn {
            width: 100%;
            display: block;
            text-align: center;
            text-decoration: none;
        }
    </style>
    @endpush
@endsection

