@props(['section' => null])

@php
    $section = $section ?? (object) [];

    // Check if section is a model instance with Translatable
    $isTranslatable = is_object($section) && method_exists($section, 'translate');

    // Get translations using Translatable
    $currentLocale = app()->getLocale();
    $enTranslation = $isTranslatable ? $section->translate('en') : null;
    $currentTranslation = $isTranslatable ? $section->translate($currentLocale) : null;

    // Get title with fallback
    $title = '';
    if ($isTranslatable) {
        $title =
            $currentTranslation && isset($currentTranslation->title) && $currentTranslation->title
                ? $currentTranslation->title
                : ($enTranslation && isset($enTranslation->title) && $enTranslation->title
                    ? $enTranslation->title
                    : '');
    }

    // Fallback to object property if not translatable
    if (empty($title) && isset($section->title)) {
        $title = is_string($section->title)
            ? $section->title
            : (is_array($section->title)
                ? $section->title[$currentLocale] ?? ($section->title['en'] ?? '')
                : '');
    }

    // Default title if still empty
    if (empty($title)) {
        $title = __('Best event & online ticket platform.');
    }

    // Get subtitle with fallback
    $subtitle = '';
    if ($isTranslatable) {
        $subtitle =
            $currentTranslation && isset($currentTranslation->subtitle) && $currentTranslation->subtitle
                ? $currentTranslation->subtitle
                : ($enTranslation && isset($enTranslation->subtitle) && $enTranslation->subtitle
                    ? $enTranslation->subtitle
                    : '');
    }

    if (empty($subtitle) && isset($section->subtitle)) {
        $subtitle = is_string($section->subtitle)
            ? $section->subtitle
            : (is_array($section->subtitle)
                ? $section->subtitle[$currentLocale] ?? ($section->subtitle['en'] ?? '')
                : '');
    }

    // Get content with fallback - split into paragraphs
    $content = '';
    if ($isTranslatable) {
        $content =
            $currentTranslation && isset($currentTranslation->content) && $currentTranslation->content
                ? $currentTranslation->content
                : ($enTranslation && isset($enTranslation->content) && $enTranslation->content
                    ? $enTranslation->content
                    : '');
    }

    if (empty($content) && isset($section->content)) {
        $content = is_string($section->content)
            ? $section->content
            : (is_array($section->content)
                ? $section->content[$currentLocale] ?? ($section->content['en'] ?? '')
                : '');
    }

    // Split content into paragraphs if it contains newlines
    $paragraphs = !empty($content) ? array_filter(explode("\n", $content)) : [];
    if (empty($paragraphs) && !empty($content)) {
        $paragraphs = [$content];
    }

    // Get image from section
    $imagePath = isset($section->image_path) ? $section->image_path : null;
    $images = isset($section->images) && is_array($section->images) ? $section->images : [];
    $mainImage = !empty($images) ? $images[0] : $imagePath ?? null;

    // Get section data
    $sectionData = isset($section->data) && is_array($section->data) ? $section->data : [];

    // Get highlighted words from data or default
    $highlightWords = $sectionData['highlight_words'] ?? ['event', 'online'];

    // Process title to highlight words
    $processedTitle = $title;
    foreach ($highlightWords as $word) {
        $processedTitle = preg_replace(
            '/\b' . preg_quote($word, '/') . '\b/i',
            '<span class="highlight-text">' . $word . '</span>',
            $processedTitle,
        );
    }
@endphp

<!-- About Section - Modern Design -->
@php
    // Check if section is in a grid layout (columns_per_row > 1)
    $isInGrid = isset($section->columns_per_row) && $section->columns_per_row > 1;
    $sectionClass = $isInGrid ? 'about-section-grid' : 'about-section-full';
@endphp
<section class="about-section-modern {{ $sectionClass }} py-4 h-100" style="background: #fff; height: 100%;">
    <div class="container">
        <div class="h-100 d-flex flex-column">
            @if ($isInGrid)
                <!-- Grid Layout (Compact) -->
                <div class="about-content-modern h-100 d-flex flex-column">
                    @if ($mainImage)
                        <div class="about-image-wrapper mb-3" style="flex-shrink: 0;">
                            <img src="{{ asset('storage/' . $mainImage) }}" alt="{{ $title }}"
                                class="img-fluid rounded-3 shadow-sm"
                                style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                    @endif

                    @if ($title)
                        <h3 class="about-title-modern mb-3"
                            style="font-size: 1.5rem; font-weight: 700; line-height: 1.3; color: #1a1a1a; flex-shrink: 0;">
                            {!! $processedTitle !!}
                        </h3>
                    @endif

                    @if (!empty($subtitle))
                        <p class="about-subtitle-modern mb-2 text-muted"
                            style="font-size: 0.9rem; font-weight: 600; flex-shrink: 0;">
                            {{ $subtitle }}
                        </p>
                    @endif

                    @if (!empty($paragraphs))
                        <div class="about-text-wrapper flex-grow-1">
                            @foreach ($paragraphs as $index => $paragraph)
                                <p class="about-text-modern mb-2"
                                    style="font-size: 0.95rem; line-height: 1.6; color: #4a4a4a;">
                                    {{ trim($paragraph) }}
                                </p>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <!-- Full Width Layout -->
                <div class="row align-items-center g-4 g-lg-5 h-100">
                    <!-- Image Column -->
                    <div class="col-lg-4 order-2 order-lg-1">
                        <div class="about-image-wrapper position-relative">
                            @if ($mainImage)
                                <img src="{{ asset('storage/' . $mainImage) }}" alt="{{ $title }}"
                                    class="img-fluid rounded-4 shadow-lg"
                                    style="width: 100%; height: auto; object-fit: cover;">
                            @else
                                <!-- Placeholder with decorative elements -->
                                <div class="about-image-placeholder position-relative"
                                    style="min-height: 500px; background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%); border-radius: 1.5rem; display: flex; align-items: center; justify-content: center;">
                                    <div class="position-absolute"
                                        style="top: 10%; right: 10%; width: 200px; height: 200px; background: rgba(255, 183, 77, 0.3); border-radius: 50%;">
                                    </div>
                                    <div class="position-absolute"
                                        style="bottom: 15%; left: 5%; width: 80px; height: 80px; background: rgba(0, 0, 0, 0.1); border-radius: 50%;">
                                    </div>
                                    <div class="position-absolute"
                                        style="top: 50%; left: 50%; transform: translate(-50%, -50%); width: 150px; height: 150px; background: rgba(255, 255, 255, 0.5); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user-circle"
                                            style="font-size: 80px; color: rgba(0, 0, 0, 0.2);"></i>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Content Column -->
                    <div class="col-lg-8 order-1 order-lg-2">
                        <div class="about-content-modern ps-lg-4">
                            @if ($title)
                                <h2 class="about-title-modern mb-4"
                                    style="font-size: 2.5rem; font-weight: 700; line-height: 1.2; color: var(--brand-primary);">
                                    {!! $processedTitle !!}
                                </h2>
                            @endif

                            @if (!empty($paragraphs))
                                @foreach ($paragraphs as $index => $paragraph)
                                    <p class="about-text-modern mb-3"
                                        style="font-size: 1.1rem; line-height: 1.8; color: var(--brand-secondary);">
                                        {{ trim($paragraph) }}
                                    </p>
                                @endforeach
                            @else
                                <!-- Default content if no content provided -->
                                <p class="about-text-modern mb-3"
                                    style="font-size: 1.1rem; line-height: 1.8; color: var(--brand-secondary);">
                                    {{ __('Things go wrong. You\'ll have questions. We understand. So we have people, not bots, on hand to help.') }}
                                </p>
                                <p class="about-text-modern mb-3"
                                    style="font-size: 1.1rem; line-height: 1.8; color: var(--brand-secondary);">
                                    {{ __('We aim to answer any query in less than 10 minutes.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<style>
    .about-section-modern {
        padding: 40px 20px;
        border-radius: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .about-section-grid {
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .about-section-grid:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .about-section-full {
        padding: 80px 0;
    }

    .highlight-text {
        background: linear-gradient(180deg, transparent 60%, #ffd700 60%);
        font-weight: 600;
        padding: 0 2px;
    }

    .about-image-wrapper img {
        transition: transform 0.3s ease;
    }

    .about-image-wrapper:hover img {
        transform: scale(1.02);
    }

    @media (max-width: 991px) {
        .about-section-full .about-title-modern {
            font-size: 2rem !important;
        }

        .about-section-full .about-content-modern {
            padding-left: 0 !important;
            margin-top: 2rem;
        }

        .about-section-modern {
            padding: 30px 15px;
        }
    }
</style>
