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

@section('meta_title', __('Register'))
@section('meta_description', __('Create your account'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endsection

@section('content')
    <!-- Register Section -->
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
                        <h1 class="brand-title">{{ __('Join Us Today') }}</h1>
                        <p class="brand-subtitle">{{ __('Create your account and start enjoying exclusive benefits and rewards') }}</p>
                        <div class="brand-features">
                            <div class="feature-item">
                                <i class="fas fa-gift"></i>
                                <span>{{ __('Exclusive Offers') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-coins"></i>
                                <span>{{ __('Earn Points') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-star"></i>
                                <span>{{ __('Special Rewards') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="branding-pattern"></div>
                </div>

                <!-- Right Side - Register Form -->
                <div class="auth-form-wrapper">
                    <div class="auth-form-card">
                        <!-- Header -->
                        <div class="auth-header">
                            <div class="auth-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h2 class="auth-title">{{ __('Create Account') }}</h2>
                            <p class="auth-subtitle">{{ __('Choose your account type and get started') }}</p>
                        </div>

                        <!-- Account Type Selection -->
                        <div class="account-type-selection">
                            <label class="selection-label">{{ __('I want to register as') }}</label>
                            <div class="type-cards">
                                <div class="type-card-wrapper">
                                    <input type="radio" name="user_type" id="user_type_customer" value="customer" 
                                           {{ old('user_type', 'customer') === 'customer' ? 'checked' : '' }} required>
                                    <label for="user_type_customer" class="type-card type-card-customer">
                                        <div class="type-card-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <h5 class="type-card-title">{{ __('Customer') }}</h5>
                                        <p class="type-card-desc">{{ __('Shop, earn points, and redeem rewards') }}</p>
                                        <div class="type-card-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                </div>
                                <div class="type-card-wrapper">
                                    <input type="radio" name="user_type" id="user_type_merchant" value="merchant" 
                                           {{ old('user_type') === 'merchant' ? 'checked' : '' }} required>
                                    <label for="user_type_merchant" class="type-card type-card-merchant">
                                        <div class="type-card-icon">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <h5 class="type-card-title">{{ __('Merchant') }}</h5>
                                        <p class="type-card-desc">{{ __('Create offers and grow your business') }}</p>
                                        <div class="type-card-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('user_type')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Register Form -->
                        <form method="POST" action="{{ route('register', ['locale' => app()->getLocale()]) }}" class="auth-form" id="registerForm" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Hidden user_type field -->
                            <input type="hidden" name="user_type" id="hidden_user_type" value="{{ old('user_type', 'customer') }}">

                            <!-- Customer Fields -->
                            <div id="customerFields" class="form-fields-group">
                                <div class="form-field">
                                    <label for="name" class="field-label">
                                        <i class="fas fa-user field-label-icon"></i>
                                        {{ __('Full Name') }}
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               class="form-input @error('name') is-invalid @enderror" 
                                               value="{{ old('name') }}" 
                                               placeholder="{{ __('Enter your full name') }}" 
                                               autofocus 
                                               autocomplete="name">
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('name')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

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

                                <div class="form-field">
                                    <label for="phone" class="field-label">
                                        <i class="fas fa-phone field-label-icon"></i>
                                        {{ __('Phone Number') }}
                                        <span class="field-optional">({{ __('Optional') }})</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               class="form-input @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone') }}" 
                                               placeholder="{{ __('Enter your phone number') }}" 
                                               autocomplete="tel">
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('phone')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Merchant Fields -->
                            <div id="merchantFields" class="form-fields-group" style="display: none;">
                                <div class="form-field">
                                    <label for="company_name" class="field-label">
                                        <i class="fas fa-building field-label-icon"></i>
                                        {{ __('Company Name') }}
                                        <span class="field-required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" 
                                               id="company_name" 
                                               name="company_name" 
                                               class="form-input @error('company_name') is-invalid @enderror" 
                                               value="{{ old('company_name') }}" 
                                               placeholder="{{ __('Enter your company name') }}" 
                                               autocomplete="organization">
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('company_name')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="merchant_name" class="field-label">
                                        <i class="fas fa-user-tie field-label-icon"></i>
                                        {{ __('Owner / Manager Name') }}
                                        <span class="field-required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" 
                                               id="merchant_name" 
                                               name="merchant_name" 
                                               class="form-input @error('merchant_name') is-invalid @enderror" 
                                               value="{{ old('merchant_name') }}" 
                                               placeholder="{{ __('Enter owner or manager name') }}" 
                                               autocomplete="name">
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('merchant_name')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="merchant_email" class="field-label">
                                        <i class="fas fa-envelope field-label-icon"></i>
                                        {{ __('Email Address') }}
                                        <span class="field-required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="email" 
                                               id="merchant_email" 
                                               name="merchant_email" 
                                               class="form-input @error('merchant_email') is-invalid @enderror" 
                                               value="{{ old('merchant_email') }}" 
                                               placeholder="{{ __('Enter your business email') }}" 
                                               autocomplete="username">
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('merchant_email')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="merchant_phone" class="field-label">
                                        <i class="fas fa-phone field-label-icon"></i>
                                        {{ __('Phone Number') }}
                                        <span class="field-required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="tel" 
                                               id="merchant_phone" 
                                               name="merchant_phone" 
                                               class="form-input @error('merchant_phone') is-invalid @enderror" 
                                               value="{{ old('merchant_phone') }}" 
                                               placeholder="{{ __('Enter your business phone') }}" 
                                               autocomplete="tel">
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('merchant_phone')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="tax_number" class="field-label">
                                        <i class="fas fa-file-invoice field-label-icon"></i>
                                        {{ __('Tax Number') }}
                                        <span class="field-optional">({{ __('Optional') }})</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" 
                                               id="tax_number" 
                                               name="tax_number" 
                                               class="form-input @error('tax_number') is-invalid @enderror" 
                                               value="{{ old('tax_number') }}" 
                                               placeholder="{{ __('Enter tax number if available') }}">
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('tax_number')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="commercial_register" class="field-label">
                                        <i class="fas fa-certificate field-label-icon"></i>
                                        {{ __('Commercial Register') }}
                                        <span class="field-optional">({{ __('Optional') }})</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" 
                                               id="commercial_register" 
                                               name="commercial_register" 
                                               class="form-input @error('commercial_register') is-invalid @enderror" 
                                               value="{{ old('commercial_register') }}" 
                                               placeholder="{{ __('Enter commercial register number') }}">
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('commercial_register')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="address" class="field-label">
                                        <i class="fas fa-map-marker-alt field-label-icon"></i>
                                        {{ __('Business Address') }}
                                        <span class="field-optional">({{ __('Optional') }})</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <textarea id="address" 
                                                  name="address" 
                                                  class="form-input form-textarea @error('address') is-invalid @enderror" 
                                                  rows="3" 
                                                  placeholder="{{ __('Enter your business address') }}">{{ old('address') }}</textarea>
                                        <span class="input-focus-line"></span>
                                    </div>
                                    @error('address')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="agreement" class="field-label">
                                        <i class="fas fa-file-contract field-label-icon"></i>
                                        {{ __('Agreement Contract') }}
                                        <span class="field-required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="file" 
                                               id="agreement" 
                                               name="agreement" 
                                               class="form-input @error('agreement') is-invalid @enderror" 
                                               accept=".pdf,.doc,.docx"
                                               required>
                                        <span class="input-focus-line"></span>
                                        <small class="form-text text-muted d-block mt-1">
                                            {{ __('Upload the agreement contract file (PDF, DOC, DOCX, Max: 10MB)') }}
                                        </small>
                                    </div>
                                    @error('agreement')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Fields (Common) -->
                            <div class="form-fields-group">
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
                                               autocomplete="new-password">
                                        <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Toggle password visibility">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <span class="input-focus-line"></span>
                                    </div>
                                    <small class="field-hint">{{ __('Password must be at least 6 characters long') }}</small>
                                    @error('password')
                                        <div class="field-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

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
                                               placeholder="{{ __('Confirm your password') }}" 
                                               required 
                                               autocomplete="new-password">
                                        <button type="button" class="password-toggle-btn" id="togglePasswordConfirmation" aria-label="Toggle password visibility">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <span class="input-focus-line"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="form-field">
                                <label class="checkbox-wrapper">
                                    <input type="checkbox" 
                                           id="terms" 
                                           name="terms" 
                                           value="1" 
                                           class="checkbox-input @error('terms') is-invalid @enderror" 
                                           {{ old('terms') ? 'checked' : '' }} 
                                           required>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-label">
                                        {{ __('I agree to the') }} 
                                        <a href="{{ route('public.terms', ['locale' => app()->getLocale()]) }}" target="_blank" class="checkbox-link">{{ __('Terms and Conditions') }}</a> 
                                        {{ __('and') }} 
                                        <a href="{{ route('public.privacy', ['locale' => app()->getLocale()]) }}" target="_blank" class="checkbox-link">{{ __('Privacy Policy') }}</a>
                                    </span>
                                </label>
                                @error('terms')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="auth-submit-btn">
                                <span class="btn-content">
                                    <span class="btn-text">{{ __('Create Account') }}</span>
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

                            <!-- Social Registration -->
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

                            <!-- Login Link -->
                            <div class="auth-footer">
                                <p class="footer-text">
                                    {{ __('Already have an account?') }}
                                    <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="footer-link">
                                        {{ __('Sign in') }}
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
            const customerRadio = document.getElementById('user_type_customer');
            const merchantRadio = document.getElementById('user_type_merchant');
            const customerFields = document.getElementById('customerFields');
            const merchantFields = document.getElementById('merchantFields');
            const hiddenUserType = document.getElementById('hidden_user_type');
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            const phoneField = document.getElementById('phone');
            const companyNameField = document.getElementById('company_name');
            const merchantNameField = document.getElementById('merchant_name');
            const merchantEmailField = document.getElementById('merchant_email');
            const merchantPhoneField = document.getElementById('merchant_phone');
            const agreementField = document.getElementById('agreement');

            function toggleFields() {
                if (customerRadio.checked) {
                    customerFields.style.display = 'block';
                    merchantFields.style.display = 'none';
                    hiddenUserType.value = 'customer';
                    nameField?.setAttribute('required', 'required');
                    emailField?.setAttribute('required', 'required');
                    phoneField?.removeAttribute('required');
                    companyNameField?.removeAttribute('required');
                    merchantNameField?.removeAttribute('required');
                    merchantEmailField?.removeAttribute('required');
                    merchantPhoneField?.removeAttribute('required');
                    agreementField?.removeAttribute('required');
                } else if (merchantRadio.checked) {
                    customerFields.style.display = 'none';
                    merchantFields.style.display = 'block';
                    hiddenUserType.value = 'merchant';
                    nameField?.removeAttribute('required');
                    emailField?.removeAttribute('required');
                    phoneField?.removeAttribute('required');
                    companyNameField?.setAttribute('required', 'required');
                    merchantNameField?.setAttribute('required', 'required');
                    merchantEmailField?.setAttribute('required', 'required');
                    merchantPhoneField?.setAttribute('required', 'required');
                    agreementField?.setAttribute('required', 'required');
                }
            }

            customerRadio?.addEventListener('change', toggleFields);
            merchantRadio?.addEventListener('change', toggleFields);
            toggleFields();

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
            const form = document.getElementById('registerForm');
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
