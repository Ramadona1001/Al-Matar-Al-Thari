@props(['section' => null, 'statistics' => collect()])

@php
    $section = $section ?? (object)[];
    $items = isset($section->activeItems) ? $section->activeItems : collect();
    // For statistics sections, always use $statistics if available, otherwise use activeItems
    $sectionType = $section->type ?? $section->component_name ?? '';
    if (in_array($sectionType, ['statistics', 'counters']) && ($statistics ?? collect())->count() > 0) {
        $displayStats = $statistics;
    } else {
        $displayStats = $items->count() > 0 ? $items : ($statistics ?? collect());
    }
    
    $isTranslatable = is_object($section) && method_exists($section, 'translate');
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;
    
    $title = ($currentTranslation && isset($currentTranslation->title) && $currentTranslation->title) 
        ? $currentTranslation->title 
        : (($enTranslation && isset($enTranslation->title) && $enTranslation->title) ? $enTranslation->title : __('Our Impact'));
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title) ? $section->title : (is_array($section->title) ? ($section->title[$currentLocale] ?? $section->title['en'] ?? '') : '');
    }
    if (empty($title)) {
        $title = __('Our Impact');
    }
    
    $subtitle = ($currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle) 
        ? $currentTranslation->subtitle 
        : (($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle) ? $enTranslation->subtitle : '');
    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle) ? $section->subtitle : (is_array($section->subtitle) ? ($section->subtitle[$currentLocale] ?? $section->subtitle['en'] ?? '') : '');
    }
@endphp

@if($displayStats->count() > 0)
    <section class="section statistics-section" style="padding: 80px 0;background: linear-gradient( color-mix(in srgb, var(--brand-primary) 70%, transparent), color-mix(in srgb, var(--gradient-end-color) 70%, transparent) );position: relative;overflow: hidden;">
        
        
        <div class="container" style="position: relative; z-index: 1;">
            @if($title)
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--bg-secondary-color); margin-bottom: 1rem;">{{ $title }}</h2>
                        @if($subtitle)
                            <p style="font-size: 1.2rem; color: rgba(255, 255, 255, 0.9);">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
            @endif
            
            <div class="row align-items-center justify-content-center">
                @foreach($displayStats as $index => $stat)
                    @php
                        $statLabel = '';
                        $statValue = '';
                        $statDesc = '';
                        if (is_object($stat) && method_exists($stat, 'translate')) {
                            $statCurrent = $stat->translate($currentLocale);
                            $statEn = $stat->translate('en');
                            $statLabel = ($statCurrent && isset($statCurrent->label) && $statCurrent->label) 
                                ? $statCurrent->label 
                                : (($statEn && isset($statEn->label) && $statEn->label) ? $statEn->label : '');
                            $statDesc = ($statCurrent && isset($statCurrent->description) && $statCurrent->description) 
                                ? $statCurrent->description 
                                : (($statEn && isset($statEn->description) && $statEn->description) ? $statEn->description : '');
                        }
                        if (empty($statLabel)) {
                            $statLabel = is_array($stat->label ?? '') ? ($stat->label[$currentLocale] ?? $stat->label['en'] ?? '') : ($stat->label ?? $stat->title ?? '');
                        }
                        if (empty($statDesc)) {
                            $statDesc = is_array($stat->description ?? '') ? ($stat->description[$currentLocale] ?? $stat->description['en'] ?? '') : ($stat->description ?? '');
                        }
                        $statValue = $stat->value ?? '';
                        $statIcon = $stat->icon ?? 'fas fa-chart-bar';
                        $statSuffix = $stat->suffix ?? '';
                    @endphp
                    <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
                        <div class="stat-item text-center" style="padding: 0 1rem;">
                            {{-- Icon Circle (Primary Color Background) --}}
                            <div class="stat-icon-wrapper" style="width: 100px; height: 100px; background: var(--theme-secondary-color, #D4AF37); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4); transition: all 0.3s ease;">
                                @if($statIcon)
                                    <i class="{{ $statIcon }}" style="font-size: 2.5rem; color: var(--bg-secondary-color);"></i>
                                @else
                                    <i class="fas fa-chart-bar" style="font-size: 2.5rem; color: var(--bg-secondary-color);"></i>
                                @endif
                            </div>
                            
                            {{-- Value (Large White Number) --}}
                            @if($statValue)
                                <div class="stat-value" style="font-size: 3.5rem; font-weight: 700; color: var(--bg-secondary-color); margin-bottom: 1rem; line-height: 1.2;">
                                    {{ $statValue }}{{ $statSuffix }}
                                </div>
                            @endif
                            
                            {{-- Label (White Text, Uppercase) --}}
                            @if($statLabel)
                                <div class="stat-label" style="font-size: 0.95rem; font-weight: 600; color: var(--bg-secondary-color); text-transform: uppercase; letter-spacing: 1px; line-height: 1.4;">
                                    {{ $statLabel }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

<style>
.statistics-section .stat-item {
    transition: transform 0.3s ease;
}

.statistics-section .stat-item:hover {
    transform: translateY(-8px);
}

.statistics-section .stat-item:hover .stat-icon-wrapper {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(212, 175, 55, 0.6);
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .statistics-section {
        padding: 60px 0 !important;
    }
    
    .statistics-section .stat-icon-wrapper {
        width: 80px !important;
        height: 80px !important;
        margin-bottom: 1.5rem !important;
    }
    
    .statistics-section .stat-icon-wrapper i {
        font-size: 2rem !important;
    }
    
    .statistics-section .stat-value {
        font-size: 2.5rem !important;
    }
}

@media (max-width: 575.98px) {
    .statistics-section .stat-item {
        margin-bottom: 2.5rem !important;
    }
}
</style>
