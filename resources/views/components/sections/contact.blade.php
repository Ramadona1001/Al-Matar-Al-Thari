@props(['section' => null])

@php
    $site = \App\Models\SiteSetting::getSettings();
    $section = $section ?? (object)[];
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Contact Us'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Contact Us');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : __('Get in Touch'));
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
    if (empty($subtitle)) {
        $subtitle = __('Get in Touch');
    }
    
    $sectionData = isset($section->data) && is_array($section->data) ? $section->data : [];
    $nameLabel = $sectionData['name_label'] ?? __('Full Name');
    $emailLabel = $sectionData['email_label'] ?? __('Email Address');
    $phoneLabel = $sectionData['phone_label'] ?? __('Phone Number');
    $messageLabel = $sectionData['message_label'] ?? __('Message');
    $sendButtonText = $sectionData['button_text'] ?? __('Send Message');
@endphp

<section class="section" style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ $title }}</h2>
                @if($subtitle)
                    <p style="font-size: 1.2rem; color: #6c757d;">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4">
                <div style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                    <form action="{{ route('public.contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" style="font-weight: 600; color: #3D4F60; margin-bottom: 0.5rem; display: block;">
                                    {{ $nameLabel }} <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required
                                       style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e0e0e0; border-radius: 10px; transition: all 0.3s ease;"
                                       onfocus="this.style.borderColor='#17A2B8'; this.style.boxShadow='0 0 0 0.2rem rgba(23, 162, 184, 0.25)';"
                                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                                @error('name')
                                    <p style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" style="font-weight: 600; color: #3D4F60; margin-bottom: 0.5rem; display: block;">
                                    {{ $emailLabel }} <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required
                                       style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e0e0e0; border-radius: 10px; transition: all 0.3s ease;"
                                       onfocus="this.style.borderColor='#17A2B8'; this.style.boxShadow='0 0 0 0.2rem rgba(23, 162, 184, 0.25)';"
                                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                                @error('email')
                                    <p style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" style="font-weight: 600; color: #3D4F60; margin-bottom: 0.5rem; display: block;">
                                    {{ $phoneLabel }}
                                </label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e0e0e0; border-radius: 10px; transition: all 0.3s ease;"
                                       onfocus="this.style.borderColor='#17A2B8'; this.style.boxShadow='0 0 0 0.2rem rgba(23, 162, 184, 0.25)';"
                                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                                @error('phone')
                                    <p style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="subject" style="font-weight: 600; color: #3D4F60; margin-bottom: 0.5rem; display: block;">
                                    {{ __('Subject') }} <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" 
                                       id="subject" 
                                       name="subject" 
                                       value="{{ old('subject') }}"
                                       required
                                       style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e0e0e0; border-radius: 10px; transition: all 0.3s ease;"
                                       onfocus="this.style.borderColor='#17A2B8'; this.style.boxShadow='0 0 0 0.2rem rgba(23, 162, 184, 0.25)';"
                                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                                @error('subject')
                                    <p style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="message" style="font-weight: 600; color: #3D4F60; margin-bottom: 0.5rem; display: block;">
                                    {{ $messageLabel }} <span style="color: #dc3545;">*</span>
                                </label>
                                <textarea id="message" 
                                          name="message" 
                                          rows="6" 
                                          required
                                          style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e0e0e0; border-radius: 10px; transition: all 0.3s ease; resize: none;"
                                          onfocus="this.style.borderColor='#17A2B8'; this.style.boxShadow='0 0 0 0.2rem rgba(23, 162, 184, 0.25)';"
                                          onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">{{ old('message') }}</textarea>
                                @error('message')
                                    <p style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" 
                                        style="background: #4BB543; border: none; padding: 0.75rem 2rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px; color: #ffffff; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                                    <i class="fas fa-paper-plane me-2"></i>{{ $sendButtonText }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4">
                <div style="background: #f8f9fa; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); height: 100%;">
                    <h3 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1.5rem;">{{ __('Contact Information') }}</h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        @if($site->contact_email)
                            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                                <div style="width: 50px; height: 50px; border-radius: 10px; background: #17A2B8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-envelope" style="color: #ffffff;"></i>
                                </div>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #3D4F60; margin-bottom: 0.25rem;">{{ __('Email') }}</h4>
                                    <a href="mailto:{{ $site->contact_email }}" style="color: #17A2B8; text-decoration: none;">{{ $site->contact_email }}</a>
                                </div>
                            </div>
                        @endif
                        
                        @if($site->contact_phone)
                            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                                <div style="width: 50px; height: 50px; border-radius: 10px; background: #17A2B8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-phone" style="color: #ffffff;"></i>
                                </div>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #3D4F60; margin-bottom: 0.25rem;">{{ __('Phone') }}</h4>
                                    <a href="tel:{{ $site->contact_phone }}" style="color: #17A2B8; text-decoration: none;">{{ $site->contact_phone }}</a>
                                </div>
                            </div>
                        @endif
                        
                        @if($site->contact_address)
                            <div style="display: flex; align-items: start; gap: 1rem;">
                                <div style="width: 50px; height: 50px; border-radius: 10px; background: #17A2B8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-map-marker-alt" style="color: #ffffff;"></i>
                                </div>
                                <div>
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #3D4F60; margin-bottom: 0.25rem;">{{ __('Address') }}</h4>
                                    <p style="color: #6c757d; margin: 0;">{{ $site->contact_address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
button[type="submit"]:hover {
    background: #3a9a32 !important;
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15) !important;
}
</style>
