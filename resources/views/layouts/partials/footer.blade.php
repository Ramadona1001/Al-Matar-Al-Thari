<!-- Call to Action Section (Integrated with Footer) -->
@php
    $currentLocale = app()->getLocale();
    $ctaTitle = __('Ready to Get Started?');
    $ctaDescription = __('Join thousands of satisfied customers and merchants');
    $ctaButtonText = __('Join Now');
    $ctaButtonLink = route('register', ['type' => 'customer']);
    $ctaSecondaryButtonText = __('Contact Us');
    $ctaSecondaryButtonLink = route('public.contact');
    
    // Try to get CTA from section settings if available
    try {
        $ctaSection = \App\Models\SectionSetting::where('key', 'cta')->active()->first();
        if ($ctaSection) {
            $ctaTranslation = $ctaSection->translate($currentLocale);
            $ctaEn = $ctaSection->translate('en');
            if ($ctaTranslation && $ctaTranslation->title) {
                $ctaTitle = $ctaTranslation->title;
            } elseif ($ctaEn && $ctaEn->title) {
                $ctaTitle = $ctaEn->title;
            }
            if ($ctaTranslation && $ctaTranslation->content) {
                $ctaDescription = $ctaTranslation->content;
            } elseif ($ctaEn && $ctaEn->content) {
                $ctaDescription = $ctaEn->content;
            }
            if (isset($ctaSection->data['button_text'])) {
                $ctaButtonText = $ctaSection->data['button_text'];
            }
            if (isset($ctaSection->data['button_link'])) {
                $ctaButtonLink = $ctaSection->data['button_link'];
            }
        }
    } catch (\Exception $e) {
        // Use defaults
    }
@endphp

<section class="footer-cta-section" style="background: linear-gradient(135deg, var(--gradient-start-color, #1B4332) 0%, var(--gradient-end-color, #2D5016) 100%); color: var(--text-on-primary-color, #ffffff); padding: 60px 0; text-align: center; position: relative; overflow: hidden;">
    <div class="container" style="position: relative; z-index: 1;">
        <h2 class="footer-cta-title" style="font-size: 2.25rem; font-weight: 700; margin-bottom: 1rem; color: white;">{{ $ctaTitle }}</h2>
        <p class="footer-cta-description" style="font-size: 1.15rem; margin-bottom: 2rem; opacity: 0.95; color: rgba(255, 255, 255, 0.95); max-width: 700px; margin-left: auto; margin-right: auto;">{{ $ctaDescription }}</p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center align-items-center">
            <a href="{{ $ctaButtonLink }}" class="footer-cta-primary-btn" style="background: var(--theme-secondary-color, #D4AF37); border: none; padding: 0.875rem 2.5rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px; color: var(--text-on-primary-color, #ffffff); text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); display: inline-block; white-space: nowrap;">
                {{ $ctaButtonText }}
            </a>
            <a href="{{ $ctaSecondaryButtonLink }}" class="footer-cta-secondary-btn" style="background: rgba(255, 255, 255, 0.15); border: 2px solid rgba(255, 255, 255, 0.4); padding: 0.875rem 2.5rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px; color: #ffffff; text-decoration: none; transition: all 0.3s ease; backdrop-filter: blur(10px); display: inline-block; white-space: nowrap;">
                {{ $ctaSecondaryButtonText }}
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
@php
    $site = \App\Models\SiteSetting::getSettings();
    $footerBgImage = !empty($site->footer_bg_image_path) ? asset('storage/'.$site->footer_bg_image_path) : null;
@endphp
<footer class="footer-modern" style="background: var(--bg-dark-color, #1B4332); color: var(--text-on-primary-color, #ffffff); padding: 4rem 0 2rem; position: relative; overflow: hidden;">
    @if($footerBgImage)
        <!-- Background Image Overlay (Full Section) -->
        <div class="footer-bg-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; opacity: 0.15; background-image: url('{{ $footerBgImage }}'); background-size: cover; background-position: center; background-repeat: no-repeat; pointer-events: none; z-index: 0;"></div>
    @endif
    
    <div class="container footer-content" style="position: relative; z-index: 1;">
        <div class="row g-4">
            <!-- Left Column: Logo, Description, Social Media, Copyright -->
            <div class="col-lg-4 col-md-6 mb-4">
                @php
                    $currentLocale = app()->getLocale();
                    $brandName = is_array($site->brand_name) ? ($site->brand_name[$currentLocale] ?? reset($site->brand_name)) : ($site->brand_name ?? config('app.name'));
                    $footerText = is_array($site->footer_text) ? ($site->footer_text[$currentLocale] ?? reset($site->footer_text)) : ($site->footer_text ?? '');
                @endphp
                
                <!-- Logo -->
                <div class="footer-logo-modern" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    @if(!empty($site->footer_logo_path))
                        <a href="{{ route('public.home') }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                            <img src="{{ asset('storage/'.$site->footer_logo_path) }}" alt="{{ $brandName }}" style="width: auto;">
                        </a>
                    @elseif(!empty($site->logo_path))
                        <a href="{{ route('public.home') }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                            <img src="{{ asset('storage/'.$site->logo_path) }}" alt="{{ $brandName }}" style="width: auto;">
                        </a>
                    @else
                        <a href="{{ route('public.home') }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                            <div class="footer-logo-icon-modern" style="width: 50px; height: 50px; background: var(--theme-secondary-color, #D4AF37); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: var(--bg-dark-color, #1B4332);">
                                <i class="bi bi-sun-fill"></i>
                            </div>
                            <span style="font-size: 1.5rem; font-weight: 700; color: white; font-family: sans-serif;">{{ $brandName }}</span>
                        </a>
                    @endif
                </div>
                
                <!-- Company Description -->
                @if($footerText)
                    <div class="footer-description-modern" style="margin-bottom: 2rem; color: rgba(255, 255, 255, 0.85); font-size: 0.9rem; line-height: 1.7;">
                        {!! nl2br(e($footerText)) !!}
                    </div>
                @else
                    <div class="footer-description-modern" style="margin-bottom: 2rem; color: rgba(255, 255, 255, 0.85); font-size: 0.9rem; line-height: 1.7;">
                        {{ __('We are committed to delivering reliable, efficient, and sustainable solutions, from residential installations to commercial systems. We aim to harness the power of innovation and reduce your costs while protecting the environment.') }}
                    </div>
                @endif
                
                <!-- Newsletter Subscription -->
                @php
                    $newsletterTitle = __('Subscribe Our Newsletter');
                    $newsletterSubtitle = __('Stay updated with the latest offers, rewards, and exclusive deals.');
                    
                    // Try to get newsletter section settings
                    try {
                        $newsletterSection = \App\Models\SectionSetting::where('key', 'newsletter_section')->active()->first();
                        if ($newsletterSection) {
                            $newsletterTranslation = $newsletterSection->translate($currentLocale);
                            $newsletterEn = $newsletterSection->translate('en');
                            if ($newsletterTranslation && $newsletterTranslation->title) {
                                $newsletterTitle = $newsletterTranslation->title;
                            } elseif ($newsletterEn && $newsletterEn->title) {
                                $newsletterTitle = $newsletterEn->title;
                            }
                            if ($newsletterTranslation && $newsletterTranslation->subtitle) {
                                $newsletterSubtitle = $newsletterTranslation->subtitle;
                            } elseif ($newsletterEn && $newsletterEn->subtitle) {
                                $newsletterSubtitle = $newsletterEn->subtitle;
                            }
                        }
                    } catch (\Exception $e) {
                        // Use defaults
                    }
                @endphp
                
                <div class="footer-newsletter-modern" style="margin-bottom: 2rem;">
                    <h6 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.75rem; color: white;">{{ $newsletterTitle }}</h6>
                    <p style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.75); margin-bottom: 1rem; line-height: 1.5;">
                        {{ $newsletterSubtitle }}
                    </p>
                    <form action="{{ route('public.newsletter.subscribe') }}" method="POST" class="footer-newsletter-form" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        @csrf
                        <input type="email" 
                               name="email" 
                               class="footer-newsletter-input" 
                               placeholder="{{ __('Enter Your Email') }}" 
                               required 
                               style="flex: 1; min-width: 200px; padding: 0.7rem 1.25rem; border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 50px; background: rgba(255, 255, 255, 0.1); color: white; font-size: 0.9rem; outline: none; transition: all 0.3s ease; backdrop-filter: blur(10px);"
                               onfocus="this.style.background='rgba(255, 255, 255, 0.15)'; this.style.borderColor='rgba(255, 255, 255, 0.4)';"
                               onblur="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='rgba(255, 255, 255, 0.2)';">
                        <button type="submit" 
                                class="footer-newsletter-btn" 
                                aria-label="{{ __('Subscribe') }}"
                                style="background: var(--theme-secondary-color, #D4AF37); border: none; padding: 0.7rem 1.5rem; font-size: 0.9rem; font-weight: 600; border-radius: 50px; color: var(--text-on-primary-color, #ffffff); white-space: nowrap; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); display: flex; align-items: center; gap: 0.5rem;">
                            <span>{{ __('Subscribe') }}</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                    @if(session('success'))
                        <div class="alert alert-success mt-2" style="padding: 0.5rem 0.75rem; font-size: 0.8rem; background: rgba(75, 181, 67, 0.2); border: 1px solid rgba(75, 181, 67, 0.4); color: #4BB543; border-radius: 8px; margin-top: 0.75rem;">
                            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if($errors->has('email'))
                        <div class="alert alert-danger mt-2" style="padding: 0.5rem 0.75rem; font-size: 0.8rem; background: rgba(220, 53, 69, 0.2); border: 1px solid rgba(220, 53, 69, 0.4); color: #ff6b6b; border-radius: 8px; margin-top: 0.75rem;">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first('email') }}
                        </div>
                    @endif
                </div>
                
                <!-- Social Media Icons -->
                @php
                    $socialLinks = [];
                    if (isset($site->social_links) && is_array($site->social_links) && !empty($site->social_links)) {
                        $socialLinks = $site->social_links;
                    } else {
                        if (isset($site->facebook_url) && $site->facebook_url) {
                            $socialLinks['facebook'] = $site->facebook_url;
                        }
                        if (isset($site->twitter_url) && $site->twitter_url) {
                            $socialLinks['twitter'] = $site->twitter_url;
                        }
                        if (isset($site->whatsapp_url) && $site->whatsapp_url) {
                            $socialLinks['whatsapp'] = $site->whatsapp_url;
                        }
                        if (isset($site->instagram_url) && $site->instagram_url) {
                            $socialLinks['instagram'] = $site->instagram_url;
                        }
                        if (isset($site->youtube_url) && $site->youtube_url) {
                            $socialLinks['youtube'] = $site->youtube_url;
                        }
                        if (isset($site->linkedin_url) && $site->linkedin_url) {
                            $socialLinks['linkedin'] = $site->linkedin_url;
                        }
                    }
                @endphp
                
                @if(!empty($socialLinks))
                    <div class="footer-social-modern" style="display: flex; gap: 0.75rem; margin-bottom: 2rem; flex-wrap: wrap;">
                        @foreach($socialLinks as $platform => $url)
                            @if($url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($platform) }}" 
                                   style="width: 40px; height: 40px; border-radius: 50%; background: transparent; border: 1px solid rgba(255, 255, 255, 0.3); display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: all 0.3s ease; font-size: 1.1rem;"
                                   onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='rgba(255, 255, 255, 0.5)'; this.style.transform='translateY(-2px)';" 
                                   onmouseout="this.style.background='transparent'; this.style.borderColor='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(0)';">
                                    @if($platform === 'facebook')
                                        <i class="fab fa-facebook-f"></i>
                                    @elseif($platform === 'twitter')
                                        <i class="fab fa-twitter"></i>
                                    @elseif($platform === 'whatsapp')
                                        <i class="fab fa-whatsapp"></i>
                                    @elseif($platform === 'instagram')
                                        <i class="fab fa-instagram"></i>
                                    @elseif($platform === 'youtube')
                                        <i class="fab fa-youtube"></i>
                                    @elseif($platform === 'linkedin')
                                        <i class="fab fa-linkedin-in"></i>
                                    @else
                                        <i class="bi bi-{{ $platform }}"></i>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
                
                <!-- Copyright -->
                @php
                    $footerCopyright = is_array($site->footer_copyright) ? ($site->footer_copyright[$currentLocale] ?? reset($site->footer_copyright)) : ($site->footer_copyright ?? '');
                @endphp
                <div class="footer-copyright-modern" style="color: rgba(255, 255, 255, 0.7); font-size: 0.875rem; margin-top: 2rem;">
                    @if($footerCopyright)
                        <p style="margin: 0;">{!! $footerCopyright !!} © {{ date('Y') }} - {{ $brandName }}</p>
                    @else
                        <p style="margin: 0;">© {{ date('Y') }} - {{ $brandName }}</p>
                    @endif
                </div>
            </div>

            <!-- Middle Columns: Menu Groups -->
            @if(isset($footerMenuGroups) && $footerMenuGroups->count() > 0)
                @foreach($footerMenuGroups->take(3) as $group)
                    @php
                        $groupTranslation = $group->translate($currentLocale);
                        $groupEn = $group->translate('en');
                        $groupName = ($groupTranslation && $groupTranslation->name) 
                            ? $groupTranslation->name 
                            : (($groupEn && $groupEn->name) ? $groupEn->name : __('Menu Group'));
                    @endphp
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-column-modern">
                            <h6 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; color: white; text-transform: none;">{{ $groupName }}</h6>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @if($group->menuItems->count() > 0)
                                    @foreach($group->menuItems->take(6) as $menuItem)
                                        @php
                                            $menuLabel = '';
                                            if (is_object($menuItem) && method_exists($menuItem, 'translate')) {
                                                $menuTranslation = $menuItem->translate($currentLocale);
                                                $menuEn = $menuItem->translate('en');
                                                $menuLabel = ($menuTranslation && isset($menuTranslation->label) && $menuTranslation->label) 
                                                    ? $menuTranslation->label 
                                                    : (($menuEn && isset($menuEn->label) && $menuEn->label) ? $menuEn->label : '');
                                            }
                                            if (empty($menuLabel)) {
                                                $menuLabel = $menuItem->label ?? '';
                                            }
                                        @endphp
                                        <li style="margin-bottom: 0.85rem;">
                                            <a href="{{ $menuItem->full_url }}" 
                                               style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" 
                                               onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" 
                                               onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">
                                                {{ $menuLabel }}
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li style="margin-bottom: 0.85rem; color: rgba(255, 255, 255, 0.6); font-size: 0.9rem;">{{ __('No items yet') }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Fallback: Our Services -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-column-modern">
                        <h6 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; color: white; text-transform: none;">{{ __('Our Services') }}</h6>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.offers.index') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('Special Offers') }}</a></li>
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.companies.index') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('Partner Companies') }}</a></li>
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.features') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('Features') }}</a></li>
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.how') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('How It Works') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Company -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-column-modern">
                        <h6 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; color: white; text-transform: none;">{{ __('Company') }}</h6>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.home') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('Home') }}</a></li>
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.services.index') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('Our Services') }}</a></li>
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.about') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('About Us') }}</a></li>
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.blog.index') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('Blog') }}</a></li>
                            <li style="margin-bottom: 0.85rem;"><a href="{{ route('public.contact') }}" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; display: inline-block; line-height: 1.5;" onmouseover="this.style.color='white'; this.style.paddingLeft='5px';" onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'; this.style.paddingLeft='0';">{{ __('Contact') }}</a></li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Contact Us Column -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-column-modern">
                    <h6 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; color: white; text-transform: none;">{{ __('Contact Us') }}</h6>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @php
                            $contactAddress = is_array($site->contact_address) ? ($site->contact_address[$currentLocale] ?? reset($site->contact_address)) : ($site->contact_address ?? '');
                        @endphp
                        @if($contactAddress)
                            <li style="margin-bottom: 1rem; display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-geo-alt-fill" style="color: #ffc107; font-size: 1.1rem; margin-top: 2px; flex-shrink: 0;"></i>
                                <span style="color: rgba(255, 255, 255, 0.85); font-size: 0.9rem; line-height: 1.6;">{{ $contactAddress }}</span>
                            </li>
                        @endif
                        @if($site->contact_phone)
                            <li style="margin-bottom: 1rem; display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-telephone-fill" style="color: #ffc107; font-size: 1.1rem; margin-top: 2px; flex-shrink: 0;"></i>
                                <a href="tel:{{ $site->contact_phone }}" style="color: rgba(255, 255, 255, 0.85); text-decoration: none; font-size: 0.9rem; transition: color 0.3s ease; line-height: 1.6;" onmouseover="this.style.color='white';" onmouseout="this.style.color='rgba(255, 255, 255, 0.85)';">{{ $site->contact_phone }}</a>
                            </li>
                        @endif
                        @if($site->contact_email)
                            <li style="margin-bottom: 1rem; display: flex; align-items: start; gap: 0.75rem;">
                                <i class="bi bi-envelope-fill" style="color: #ffc107; font-size: 1.1rem; margin-top: 2px; flex-shrink: 0;"></i>
                                <a href="mailto:{{ $site->contact_email }}" style="color: rgba(255, 255, 255, 0.85); text-decoration: none; font-size: 0.9rem; transition: color 0.3s ease; word-break: break-word; line-height: 1.6;" onmouseover="this.style.color='white';" onmouseout="this.style.color='rgba(255, 255, 255, 0.85)';">{{ $site->contact_email }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Bar: Terms & Privacy -->
        <div class="footer-bottom-modern" style="border-top: 1px solid rgba(255, 255, 255, 0.15); padding-top: 1.5rem; margin-top: 3rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.875rem;">
                <!-- Empty space for alignment -->
            </div>
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <a href="{{ route('public.terms') }}" style="color: rgba(255, 255, 255, 0.85); text-decoration: none; font-size: 0.875rem; transition: color 0.3s ease;" onmouseover="this.style.color='white';" onmouseout="this.style.color='rgba(255, 255, 255, 0.85)';">{{ __('Terms & Conditions') }}</a>
                <a href="{{ route('public.privacy') }}" style="color: rgba(255, 255, 255, 0.85); text-decoration: none; font-size: 0.875rem; transition: color 0.3s ease;" onmouseover="this.style.color='white';" onmouseout="this.style.color='rgba(255, 255, 255, 0.85)';">{{ __('Privacy Policy') }}</a>
            </div>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" title="Scroll to top" aria-label="Scroll to top" style="position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px; background: var(--brand-primary); color: white; border: none; border-radius: 50%; display: none; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2);" onmouseover="this.style.background='#3D4F60'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#17A2B8'; this.style.transform='translateY(0)';">
    <i class="fas fa-arrow-up" aria-hidden="true"></i>
</button>

<style>
.footer-modern {
    background: #1a2332 !important;
}

.footer-bg-overlay {
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
}

@media (max-width: 991.98px) {
    .footer-bg-overlay {
        opacity: 0.12 !important;
    }
}

@media (max-width: 767.98px) {
    .footer-modern {
        padding: 3rem 0 1.5rem !important;
    }
    
    .footer-bg-overlay {
        opacity: 0.1 !important;
    }
    
    .footer-bottom-modern {
        flex-direction: column !important;
        text-align: center !important;
    }
    
    .footer-bottom-modern > div:last-child {
        justify-content: center !important;
    }
}

@media (max-width: 575.98px) {
    .footer-social-modern {
        justify-content: flex-start !important;
    }
    
    .footer-copyright-modern {
        margin-top: 1.5rem !important;
    }
}

/* Footer CTA Section Styles */
.footer-cta-section {
    position: relative;
}

.footer-cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.footer-cta-primary-btn:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 20px rgba(75, 181, 67, 0.4) !important;
    background: #3a9a32 !important;
}

.footer-cta-secondary-btn:hover {
    transform: translateY(-3px) !important;
    background: rgba(255, 255, 255, 0.25) !important;
    border-color: rgba(255, 255, 255, 0.6) !important;
    box-shadow: 0 6px 20px rgba(255, 255, 255, 0.15) !important;
}

@media (max-width: 767.98px) {
    .footer-cta-section {
        padding: 50px 0 !important;
    }
    
    .footer-cta-title {
        font-size: 1.75rem !important;
    }
    
    .footer-cta-description {
        font-size: 1rem !important;
        padding: 0 1rem;
    }
    
    .footer-cta-primary-btn,
    .footer-cta-secondary-btn {
        padding: 0.75rem 2rem !important;
        font-size: 1rem !important;
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 575.98px) {
    .footer-cta-section {
        padding: 40px 0 !important;
    }
    
    .footer-cta-title {
        font-size: 1.5rem !important;
    }
    
    .footer-cta-description {
        font-size: 0.95rem !important;
    }
}

/* Footer Newsletter Styles */
.footer-newsletter-form input::placeholder {
    color: rgba(255, 255, 255, 0.6) !important;
}

.footer-newsletter-form input::-webkit-input-placeholder {
    color: rgba(255, 255, 255, 0.6) !important;
}

.footer-newsletter-form input::-moz-placeholder {
    color: rgba(255, 255, 255, 0.6) !important;
    opacity: 1;
}

.footer-newsletter-form input:-ms-input-placeholder {
    color: rgba(255, 255, 255, 0.6) !important;
}

.footer-newsletter-btn:hover {
    background: #3a9a32 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(75, 181, 67, 0.4) !important;
}

.footer-newsletter-form {
    display: flex !important;
}

@media (max-width: 767.98px) {
    .footer-newsletter-form {
        flex-direction: column !important;
    }
    
    .footer-newsletter-input {
        width: 100% !important;
        min-width: 100% !important;
    }
    
    .footer-newsletter-btn {
        width: 100% !important;
        justify-content: center !important;
    }
}

@media (max-width: 575.98px) {
    .footer-newsletter-modern h6 {
        font-size: 0.95rem !important;
    }
    
    .footer-newsletter-modern p {
        font-size: 0.8rem !important;
    }
    
    .footer-newsletter-input,
    .footer-newsletter-btn {
        font-size: 0.85rem !important;
        padding: 0.6rem 1rem !important;
    }
}
</style>
