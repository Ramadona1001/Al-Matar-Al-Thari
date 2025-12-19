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
    <section class="section statistics-section" style="padding: 0 0 80px 0;  position: relative; overflow: hidden;">
        <div class="container" style="position: relative; z-index: 1;">
           
            {{-- Statistics Cards Grid - 4 cards per row --}}
            <div class="row g-4">
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
                        $statSuffix = $stat->suffix ?? '+';
                    @endphp
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="stat-card" style="background: #fff; border-radius: 12px; padding: 2.5rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: all 0.3s ease; height: 100%;">
                            {{-- Value (Large Dark Number with +) --}}
                            @if($statValue)
                                <div class="stat-value" style="font-size: 3.5rem; font-weight: 700; color: #333; margin-bottom: 1rem; line-height: 1.2;">
                                    {{ $statValue }}<span style="color: var(--brand-primary);">{{ $statSuffix }}</span>
                                </div>
                            @endif
                            
                            {{-- Label (Dark Text, Underlined) --}}
                            @if($statLabel)
                                <div class="stat-label" style="font-size: 0.9rem; font-weight: 500; color: #666; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.4; border-bottom: 2px solid var(--brand-primary); padding-bottom: 0.5rem; display: inline-block;">
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
.statistics-section .stat-card {
    transition: all 0.3s ease;
}

.statistics-section .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
}

.statistics-section .stat-card:hover .stat-value {
    color: var(--brand-primary) !important;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .statistics-section {
        padding: 60px 0 !important;
    }
    
    .statistics-section .stat-value {
        font-size: 2.8rem !important;
    }
    
    .statistics-section .stat-card {
        padding: 2rem 1.5rem !important;
    }
}

@media (max-width: 767.98px) {
    .statistics-section h2 {
        font-size: 1.75rem !important;
    }
    
    .statistics-section .stat-value {
        font-size: 2.5rem !important;
    }
    
    .statistics-section .stat-card {
        padding: 1.5rem 1.25rem !important;
        margin-bottom: 1rem;
    }
}

@media (max-width: 575.98px) {
    .statistics-section {
        padding: 40px 0 !important;
    }
    
    .statistics-section .stat-value {
        font-size: 2rem !important;
    }
    
    .statistics-section .stat-label {
        font-size: 0.8rem !important;
    }
}
</style>
