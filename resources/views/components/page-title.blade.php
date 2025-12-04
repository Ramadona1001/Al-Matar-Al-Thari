@props(['title' => '', 'subtitle' => '', 'breadcrumbs' => []])

@php
    $defaultBreadcrumbs = [
        ['label' => __('Home'), 'url' => route('public.home')]
    ];
    $breadcrumbs = !empty($breadcrumbs) ? $breadcrumbs : $defaultBreadcrumbs;
@endphp

<!-- page title area start  -->
<section class="page-title-section" style="padding: 100px 0 60px; position: relative; overflow: hidden; background: #ebebeb; background: -webkit-linear-gradient(180deg, rgba(235, 235, 235, 1) 0%, rgba(245, 245, 245, 1) 50%, rgba(235, 235, 235, 1) 100%); background: -moz-linear-gradient(180deg, rgba(235, 235, 235, 1) 0%, rgba(245, 245, 245, 1) 50%, rgba(235, 235, 235, 1) 100%); background: linear-gradient(180deg, rgba(235, 235, 235, 1) 0%, rgba(245, 245, 245, 1) 50%, rgba(235, 235, 235, 1) 100%);">
    <!-- Background Pattern -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; background-size: 50px 50px;"></div>
    
    <div class="container" style="position: relative; z-index: 1;">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="page-title-content">
                    @if($subtitle)
                        <p style="color: var(--brand-primary); font-size: 1.1rem; margin-bottom: 0.5rem; font-weight: 500;">
                            {{ $subtitle }}
                        </p>
                    @endif
                    <h1 style="color: var(--brand-primary); font-size: 3rem; font-weight: 700; margin-bottom: 0; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                        {{ $title }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Breadcrumb -->
    <div class="breadcrumb-wrapper" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 1rem 0; margin-top: 2rem;">
        <div class="container">
            <nav aria-label="breadcrumb" style="margin: 0;">
                <ol class="breadcrumb-modern" style="display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 0.5rem; list-style: none; margin: 0; padding: 0;">
                    @foreach($breadcrumbs as $index => $crumb)
                        <li class="breadcrumb-item-modern" style="display: flex; align-items: center;">
                            @if($index === count($breadcrumbs) - 1)
                                <span style="color: var(--bg-primary-color); font-weight: 600; font-size: 0.95rem;">
                                    {{ $crumb['label'] }}
                                </span>
                            @else
                                <a href="{{ $crumb['url'] ?? '#' }}" 
                                   style="color: var(--brand-primary); text-decoration: none; font-size: 0.95rem; transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem;"
                                   onmouseover="this.style.color='var(--brand-primary)'; this.style.transform='translateX(2px)';"
                                   onmouseout="this.style.color='var(--brand-primary)'; this.style.transform='translateX(0)';">
                                    @if($index === 0)
                                        <i class="bi bi-house-door" style="font-size: 1rem;"></i>
                                    @endif
                                    <span>{{ $crumb['label'] }}</span>
                                </a>
                                <span style="color: var(--brand-primary); margin: 0 0.5rem; font-size: 0.8rem;">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>
</section>
<!-- page title area end  -->

<style>
@media (max-width: 768px) {
    .page-title-section h1 {
        font-size: 2rem !important;
    }
    .page-title-section p {
        font-size: 0.95rem !important;
    }
    .breadcrumb-modern {
        justify-content: flex-start !important;
    }
}
</style>
