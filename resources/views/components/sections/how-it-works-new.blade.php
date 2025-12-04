@props(['section' => null, 'steps' => collect()])

@php
    $section = $section ?? (object)[];
    $displaySteps = $steps->count() > 0 ? $steps : collect();
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = '';
    if ($isTranslatable) {
        $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
            ? $currentTranslation->title 
            : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : '');
    }
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('How It Works');
    }
    
    $subtitle = '';
    if ($isTranslatable) {
        $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
            ? $currentTranslation->subtitle 
            : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : '');
    }
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
@endphp

<!-- How It Works Section -->
@if($displaySteps->count() > 0)
    <section class="section" style="padding: 80px 0; background: #ffffff;" aria-labelledby="how-it-works-heading">
        <div class="container">
            @if($title)
                <h2 class="section-title" id="how-it-works-heading" style="font-size: 2.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 1rem; text-align: center;">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="section-subtitle" style="font-size: 1.1rem; color: #6b7280; text-align: center; margin-bottom: 4rem;">{{ $subtitle }}</p>
            @endif
            
            <div class="how-it-works-steps-wrapper" style="position: relative;">
                <div class="row align-items-start">
                    @foreach($displaySteps as $index => $step)
                        @php
                            $stepTitle = '';
                            $stepDesc = '';
                            if (is_object($step) && method_exists($step, 'translate')) {
                                $stepCurrent = $step->translate($currentLocale);
                                $stepEn = $step->translate('en');
                                $stepTitle = ($stepCurrent && isset($stepCurrent->title) && $stepCurrent->title) 
                                    ? $stepCurrent->title 
                                    : (($stepEn && isset($stepEn->title) && $stepEn->title) ? $stepEn->title : '');
                                $stepDesc = ($stepCurrent && isset($stepCurrent->description) && $stepCurrent->description) 
                                    ? $stepCurrent->description 
                                    : (($stepEn && isset($stepEn->description) && $stepEn->description) ? $stepEn->description : '');
                            }
                            if (empty($stepTitle)) {
                                $stepTitle = is_array($step->title ?? '') ? ($step->title[$currentLocale] ?? $step->title['en'] ?? '') : ($step->title ?? '');
                            }
                            if (empty($stepDesc)) {
                                $stepDesc = is_array($step->description ?? '') ? ($step->description[$currentLocale] ?? $step->description['en'] ?? '') : ($step->description ?? '');
                            }
                            
                            $stepIcon = $step->icon ?? 'fas fa-check-circle';
                            $stepImage = $step->image_path ?? null;
                            $isLast = $index === $displaySteps->count() - 1;
                        @endphp
                        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 position-relative">
                            <div class="how-it-works-step" style="text-align: center; padding: 0 1rem;">
                                {{-- Step Icon Circle --}}
                                <div class="step-icon-circle" style="width: 80px; height: 80px; background: var(--bg-primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 4px 12px rgba(30, 58, 95, 0.3);">
                                    @if($stepImage)
                                        <img src="{{ asset('storage/' . $stepImage) }}" alt="{{ $stepTitle }}" style="width: 50px; height: 50px; object-fit: contain; filter: brightness(0) invert(1);">
                                    @elseif($stepIcon)
                                        <i class="{{ $stepIcon }}" style="font-size: 2rem; color: #ffffff;"></i>
                                    @else
                                        <i class="fas fa-check-circle" style="font-size: 2rem; color: #ffffff;"></i>
                                    @endif
                                </div>
                                
                                {{-- Step Title --}}
                                <h4 class="step-title" style="font-size: 1.25rem; font-weight: 700; color: #1a1a1a; margin-bottom: 1rem; line-height: 1.3;">
                                    {{ $stepTitle }}
                                </h4>
                                
                                {{-- Step Description --}}
                                <p class="step-description" style="color: #6b7280; font-size: 0.95rem; line-height: 1.7; margin: 0;">
                                    {{ $stepDesc }}
                                </p>
                            </div>
                            
                            {{-- Arrow Connector (except for last step) --}}
                            {{-- @if(!$isLast)
                                <div class="step-arrow" style="position: absolute; top: 40px; right: -15px; width: 30px; height: 2px; background: var(--bg-primary-color); z-index: 1; display: none;">
                                    <div style="position: absolute;right: -6px;top: -4px;width: 0;height: 0;border-left: 8px solid var(--bg-primary-color);border-top: 5px solid transparent;border-bottom: 5px solid transparent;"></div>
                                </div>
                            @endif --}}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif

<style>
.how-it-works-step {
    transition: transform 0.3s ease;
}

.how-it-works-step:hover {
    transform: translateY(-5px);
}

.how-it-works-step:hover .step-icon-circle {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(30, 58, 95, 0.4);
}

.step-icon-circle {
    transition: all 0.3s ease;
}

/* Arrow connectors for desktop */
@media (min-width: 992px) {
    .step-arrow {
        display: block !important;
    }
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .how-it-works-steps-wrapper .row {
        flex-direction: column;
    }
    
    .step-arrow {
        display: none !important;
    }
    
    .how-it-works-step {
        margin-bottom: 2rem !important;
    }
}
</style>

