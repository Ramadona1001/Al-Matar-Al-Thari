@extends('layouts.new-design')

@section('meta_title', __('Our Services'))
@section('meta_description', __('Discover our professional services'))

@section('content')
    <x-page-title 
        :title="__('What We Do')" 
        :subtitle="__('Provides hassle-free backyard transformation')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Services'), 'url' => '#']
        ]"
    />
    
    <!-- services area start  -->
    <section class="services-area pt-120 pb-90" style="padding: 50px">
        <div class="container">
            <div class="services-wrapper">
                @if($services->count() > 0)
                    <div class="row">
                        @foreach($services as $service)
                            @php
                                $currentLocale = app()->getLocale();
                                $serviceTitle = '';
                                if (is_object($service) && method_exists($service, 'translate')) {
                                    $serviceCurrent = $service->translate($currentLocale);
                                    $serviceEn = $service->translate('en');
                                    $serviceTitle = ($serviceCurrent && isset($serviceCurrent->title) && $serviceCurrent->title) 
                                        ? $serviceCurrent->title 
                                        : (($serviceEn && isset($serviceEn->title) && $serviceEn->title) ? $serviceEn->title : '');
                                }
                                if (empty($serviceTitle)) {
                                    $serviceTitle = is_array($service->title ?? '') ? ($service->title[$currentLocale] ?? $service->title['en'] ?? '') : ($service->title ?? __('Service'));
                                }
                                
                                $serviceDesc = '';
                                if (is_object($service) && method_exists($service, 'translate')) {
                                    $serviceCurrent = $service->translate($currentLocale);
                                    $serviceEn = $service->translate('en');
                                    $serviceDesc = ($serviceCurrent && isset($serviceCurrent->short_description) && $serviceCurrent->short_description) 
                                        ? $serviceCurrent->short_description 
                                        : (($serviceEn && isset($serviceEn->short_description) && $serviceEn->short_description) ? $serviceEn->short_description : '');
                                }
                                if (empty($serviceDesc)) {
                                    $serviceDesc = is_array($service->short_description ?? '') ? ($service->short_description[$currentLocale] ?? $service->short_description['en'] ?? '') : ($service->short_description ?? $service->description ?? '');
                                }
                                
                                $serviceImage = $service->image_path ?? null;
                            @endphp
                            <div class="col-lg-4 col-md-6 mb-4">
                                <a href="{{ route('public.services.show', $service->slug) }}" class="text-decoration-none">
                                    <div class="service-card-modern" style="background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                                        {{-- Image Section --}}
                                        <div class="service-image-wrapper" style="position: relative; width: 100%; height: 250px; overflow: hidden; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            @if($serviceImage)
                                                <img src="{{ asset('storage/'.$serviceImage) }}" alt="{{ $serviceTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            @elseif($service->icon && !str_contains($service->icon, 'img') && !str_contains($service->icon, 'image'))
                                                <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                                    <i class="{{ $service->icon }}" style="font-size: 4rem; color: rgba(255, 255, 255, 0.9);"></i>
                                                </div>
                                            @else
                                                <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                                    <i class="fas fa-star" style="font-size: 4rem; color: rgba(255, 255, 255, 0.9);"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Content Overlay --}}
                                        <div class="service-content-overlay" style="background: #ffffff; padding: 1.75rem; position: relative; flex-grow: 1; border-radius: 20px 20px 0 0; margin-top: -20px; z-index: 2;">
                                            <h4 class="service-title-modern" style="font-size: 1.35rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.75rem; line-height: 1.3;">
                                                {{ $serviceTitle }}
                                            </h4>
                                            <p class="service-description-modern" style="color: #6b7280; font-size: 0.95rem; line-height: 1.6; margin-bottom: 0;">
                                                {{ Str::limit(strip_tags($serviceDesc), 80) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if($services->hasPages())
                        <div class="pagination-wrapper">
                            {{ $services->links() }}
                        </div>
                    @endif
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center py-5">
                                <h3>{{ __('No services found') }}</h3>
                                <p>{{ __('Check back later for new services') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- services area end  -->
@endsection

@push('styles')
<style>
.service-card-modern {
    cursor: pointer;
}

.service-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.service-card-modern:hover .service-image-wrapper img {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

.service-card-modern:hover .service-arrow-icon {
    background: #f59e0b;
    transform: scale(1.1);
}

.service-arrow-icon {
    transition: all 0.3s ease;
}

.service-image-wrapper img {
    transition: transform 0.3s ease;
}
</style>
@endpush

