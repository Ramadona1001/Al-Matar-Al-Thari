@props(['section' => null, 'services' => collect()])

@php
    if (!function_exists('getLocalizedValue')) {
        function getLocalizedValue($value, $locale = null) {
            if (empty($value)) return '';
            $locale = $locale ?? app()->getLocale();
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? ($value[array_key_first($value)] ?? '');
            }
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    return $decoded[$locale] ?? $decoded['en'] ?? ($decoded[array_key_first($decoded)] ?? '');
                }
                return $value;
            }
            return '';
        }
    }
    $section = $section ?? (object)[];
    $title = getLocalizedValue($section->title ?? '') ?: __('Estimate Price');
    $subtitle = getLocalizedValue($section->subtitle ?? '') ?: __('Call To Action');
    $sectionData = isset($section->data) && is_array($section->data) ? $section->data : [];
    $formAction = $sectionData['form_action'] ?? route('public.contact.submit');
@endphp

<!-- pricing-cta area start  -->
<section class="pricing-cta pt-120 pb-120 fix">
    <div class="container">
        <div class="row wow fadeInUp" data-wow-delay=".3s">
            <div class="col-lg-8">
                <div class="section-title style-2">
                    <span class="section-subtitle">[ {{ $subtitle }} ]</span>
                    <h2 class="section-main-title mb-45">{{ $title }}</h2>
                </div>
            </div>
        </div>
        <div class="pricing-cta-wrapper wow fadeInUp" data-wow-delay=".3s">
            <div class="pricing-cta-inner">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="pricing-cta-form">
                            <form action="{{ $formAction }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="single-input-field field-name">
                                            <label for="name">{{ __('Full Name') }}</label>
                                            <input type="text" placeholder="{{ __('Enter here...') }}" name="name" id="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="single-input-field field-email">
                                            <label for="email">{{ __('Email Address') }}</label>
                                            <input type="email" placeholder="{{ __('Enter here...') }}" name="email" id="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="single-input-field field-number">
                                            <label for="phone">{{ __('Phone Number') }}</label>
                                            <input type="text" placeholder="{{ __('Enter here...') }}" name="phone" id="phone" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="single-input-field field-address">
                                            <label for="address">{{ __('Address') }}</label>
                                            <input type="text" placeholder="{{ __('Enter here...') }}" name="address" id="address">
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="select-service-button">
                                            <div class="single-input-field field-service">
                                                <label for="select-service">{{ __('Name of Service') }}</label>
                                                <select class="select-service" name="service" id="select-service">
                                                    <option value="">{{ __('Select Service') }}</option>
                                                    @if($services->count() > 0)
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}">{{ $service->title ?? __('Service') }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <button type="submit" class="fill-btn">{{ __('Get a Quote') }}<i class="fal fa-long-arrow-right"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="estimated-price-wrapper">
                                            <p>{{ __('Estimated Price') }}</p>
                                            <span class="es-price-value">{{ isset($section->estimated_price) ? $section->estimated_price : '$45,000' }}</span>
                                            <img class="es-price-tag" src="{{ asset('assets/img/icon/price-tag.png') }}" alt="img not found">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="pricing-cta-img">
                            <img src="{{ isset($section->image_path) && $section->image_path ? asset('storage/'.$section->image_path) : asset('assets/img/bg/pricing-cta-img.png') }}" alt="img not found">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- pricing-cta area end  -->

