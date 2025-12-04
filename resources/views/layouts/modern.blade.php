@php
    try {
        $site = \App\Models\SiteSetting::getSettings();
    } catch (\Exception $e) {
        $site = new \App\Models\SiteSetting();
    }
    
    try {
        $menus = class_exists(\App\Models\Menu::class) && \Schema::hasTable('menus')
            ? \App\Models\Menu::forMenu('header')->active()->rootItems()->ordered()->get()
            : collect();
    } catch (\Exception $e) {
        $menus = collect();
    }
    
    try {
        $footerMenus = class_exists(\App\Models\Menu::class) && \Schema::hasTable('menus')
            ? \App\Models\Menu::forMenu('footer')->active()->rootItems()->ordered()->get()
            : collect();
    } catch (\Exception $e) {
        $footerMenus = collect();
    }
    
    try {
        $servicesMenu = class_exists(\App\Models\Menu::class) && \Schema::hasTable('menus')
            ? \App\Models\Menu::forMenu('services')->active()->rootItems()->ordered()->get()
            : collect();
    } catch (\Exception $e) {
        $servicesMenu = collect();
    }
    
    // Build social links array
    $socialLinks = [];
    try {
        if (isset($site->social_links) && is_array($site->social_links) && !empty($site->social_links)) {
            $socialLinks = $site->social_links;
        } else {
            if (isset($site->facebook_url) && $site->facebook_url) {
                $socialLinks['facebook'] = $site->facebook_url;
            }
            if (isset($site->twitter_url) && $site->twitter_url) {
                $socialLinks['twitter'] = $site->twitter_url;
            }
            if (isset($site->instagram_url) && $site->instagram_url) {
                $socialLinks['instagram'] = $site->instagram_url;
            }
            if (isset($site->linkedin_url) && $site->linkedin_url) {
                $socialLinks['linkedin'] = $site->linkedin_url;
            }
            if (isset($site->youtube_url) && $site->youtube_url) {
                $socialLinks['youtube'] = $site->youtube_url;
            }
            if (isset($site->tiktok_url) && $site->tiktok_url) {
                $socialLinks['tiktok'] = $site->tiktok_url;
            }
        }
    } catch (\Exception $e) {
        $socialLinks = [];
    }
@endphp
<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    @php
        $currentLocale = app()->getLocale();
        $metaTitle = is_array($site->meta_title) ? ($site->meta_title[$currentLocale] ?? reset($site->meta_title)) : ($site->meta_title ?? '');
        $metaDescription = is_array($site->meta_description) ? ($site->meta_description[$currentLocale] ?? reset($site->meta_description)) : ($site->meta_description ?? '');
        $metaKeywords = is_array($site->meta_keywords) ? ($site->meta_keywords[$currentLocale] ?? reset($site->meta_keywords)) : ($site->meta_keywords ?? '');
        $brandName = is_array($site->brand_name) ? ($site->brand_name[$currentLocale] ?? reset($site->brand_name)) : ($site->brand_name ?? config('app.name'));
    @endphp
    <title>@yield('meta_title', $metaTitle ?: __('Welcome')) - {{ $brandName }}</title>
    <meta name="description" content="@yield('meta_description', $metaDescription)">
    <meta name="keywords" content="@yield('meta_keywords', $metaKeywords)">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Place favicon.ico in the root directory -->
    @if(!empty($site->favicon_path))
        <link rel="icon" href="{{ asset('storage/'.$site->favicon_path) }}" type="image/png">
    @endif
    <!-- CSS here -->
    <link rel="stylesheet" href="{{ asset('assets/css') }}/preloader.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/bootstrap.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/meanmenu.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/animate.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/owl.carousel.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/swiper-bundle.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/backToTop.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/magnific-popup.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/ui-range-slider.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/nice-select.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/fontAwesome5Pro.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/flaticon.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/default.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/style.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @if(!empty($site->custom_styles))
        <style>
            {!! $site->custom_styles !!}
        </style>
    @endif
</head>

<body>

    <div class="mouseCursor cursor-outer"></div>
    <div class="mouseCursor cursor-inner"><span>Drag</span></div>

    <!-- header area start  -->
    <header class="header2">
        <div class="header-top-area d-none d-lg-block">
            <div class="container">
                <div class="header-top-inner">
                    <div class="header-top-left">
                        <div class="header-top-social">
                            <div class="social-links">
                                <ul>
                                    @if(isset($socialLinks) && is_array($socialLinks) && count($socialLinks) > 0)
                                        @foreach($socialLinks as $platform => $url)
                                            @if($url)
                                                <li>
                                                    <a href="{{ $url }}" target="_blank"><i class="fab fa-{{ $platform }}"></i></a>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="header-top-right">
                        <div class="meta-items">
                            @if($site->contact_address)
                                <div class="meta-item">
                                    <div class="meta-item-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="meta-item-text">
                                        <p><a href="#">{{ $site->contact_address }}</a></p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="header-sticky" class="meta-header-area">
            <div class="container">
                <div class="meta-header-inner">
                    <div class="meta-header-left">
                        <div class="header-logo header2-logo">
                            <a href="{{ route('public.home') }}" class="logo-w">
                                @if(!empty($site->logo_path))
                                    <img src="{{ asset('storage/'.$site->logo_path) }}" alt="{{ $site->brand_name ?? config('app.name') }}">
                                @else
                                    <span class="text-white fw-bold">{{ $site->brand_name ?? config('app.name') }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="meta-header-right">
                        <div class="meta-items meta-header-meta-items d-none d-lg-inline-flex">
                            @if($site->contact_email)
                                <div class="meta-item d-none d-xl-inline-flex">
                                    <div class="meta-item-icon">
                                        <i class="fas fa-envelope-open"></i>
                                    </div>
                                    <div class="meta-item-content">
                                        <p><a href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a></p>
                                        <div class="meta-title">{{ __('Email Address') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="main-menu main-menu2">
                            <nav id="mobile-menu-sticky">
                                <ul>
                                    @if($menus->count() > 0)
                                        @foreach($menus as $menu)
                                            @php
                                                $children = $menu->children()->active()->ordered()->get();
                                                $hasChildren = $children->count() > 0;
                                            @endphp
                                            <li class="{{ $hasChildren ? 'menu-item-has-children' : '' }}">
                                                <a href="{{ $menu->full_url }}">{{ $menu->label }}</a>
                                                @if($hasChildren)
                                                    <ul class="sub-menu">
                                                        @foreach($children as $child)
                                                            <li><a href="{{ $child->full_url }}">{{ $child->label }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    @else
                                        <li><a href="{{ route('public.home') }}">{{ __('Home') }}</a></li>
                                        <li><a href="{{ route('public.about') }}">{{ __('About') }}</a></li>
                                        <li><a href="{{ route('public.contact') }}">{{ __('Contact') }}</a></li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                        <a href="javascript:void(0)"
                            class="action-btn cart-btn d-none d-lg-inline-flex action-item-cart"><i
                                class="fas fa-shopping-basket"></i></a>
                        <a href="{{ route('public.contact') }}" class="border-btn-rounded d-none d-lg-inline-flex"><i
                                class="fal fa-farm"></i><span>{{ __('Get a Quote') }}</span></a>
                        <div class="menu-bar d-lg-none">
                            <a class="side-toggle" href="javascript:void(0)">
                                <div class="bar-icon">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-main header-main2 d-none d-lg-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-12 col-lg-12">
                        <div class="header-main-content-wrapper">
                            <div class="header-main-left header-main-left-header2">
                                <div class="main-menu main-menu2 d-none d-lg-inline-block">
                                    <nav id="mobile-menu2">
                                        <ul>
                                            @if($menus->count() > 0)
                                                @foreach($menus as $menu)
                                                    @php
                                                        $children = $menu->children()->active()->ordered()->get();
                                                        $hasChildren = $children->count() > 0;
                                                    @endphp
                                                    <li class="{{ $hasChildren ? 'menu-item-has-children' : '' }}">
                                                        <a href="{{ $menu->full_url }}">{{ $menu->label }}</a>
                                                        @if($hasChildren)
                                                            <ul class="sub-menu">
                                                                @foreach($children as $child)
                                                                    <li><a href="{{ $child->full_url }}">{{ $child->label }}</a></li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            @else
                                                <li><a href="{{ route('public.home') }}">{{ __('Home') }}</a></li>
                                                <li><a href="{{ route('public.about') }}">{{ __('About') }}</a></li>
                                                <li><a href="{{ route('public.contact') }}">{{ __('Contact') }}</a></li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                                <div class="header-search d-none d-xl-inline-block">
                                    <form action="#">
                                        <div class="single-input-field field-search">
                                            <input type="search" placeholder="Keyword here...">
                                            <button type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="header-main-right header-main-right-header2">
                                @if($site->contact_phone)
                                    <div class="meta-item">
                                        <div class="meta-item-icon">
                                            <i class="flaticon-telephone"></i>
                                        </div>
                                        <div class="meta-item-content">
                                            <div class="meta-title">{{ __('Emergency Call') }}</div>
                                            <p><a href="tel:{{ $site->contact_phone }}">{{ $site->contact_phone }}</a></p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header area end -->

    <!-- Add your site or application content here -->
    <main>
        <!-- side toggle start -->
        <div class="fix">
            <div class="side-info">
                <div class="side-info-content">
                    <div class="offset-widget offset-logo mb-40">
                        <div class="row align-items-center">
                            <div class="col-9">
                                <a href="index.html">
                                    <img src="assets/img/logo/logo-bl.png" alt="Logo">
                                </a>
                            </div>
                            <div class="col-3 text-end">
                                <button class="side-info-close">
                                    <i class="fal fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-menu2 d-xl-none fix"></div>
                    <div class="offset-widget offset_searchbar mb-30">
                        <form action="#" class="filter-search-input">
                            <input type="text" placeholder="Search keyword">
                            <button type="submit"><i class="fal fa-search"></i></button>
                        </form>
                    </div>
                    <div class="offset-widget offset-support mb-30">
                        <div class="footer-support">
                            <div class="irc-item support-meta">
                                <div class="irc-item-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="irc-item-content">
                                    <p>Emergency Call</p>
                                    <div class="support-number">
                                        <a href="tel:98965963168">989 659 631 68</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="offset-widget offset-social mb-30">
                        <div class="footer-social">
                            <span>Connect:</span>
                            <div class="social-links">
                                <ul>
                                    <li>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="fab fa-behance"></i></a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="fab fa-youtube"></i></a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="fab fa-linkedin"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-overlay"></div>
        <div class="offcanvas-overlay-white"></div>
        <div class="fix">
            <div class="sidebar-action sidebar-cart">
                <button class="close-sidebar">
                    Close<i class="fal fa-times"></i>
                </button>
                <h4 class="sidebar-action-title">Shopping Cart</h4>
                <div class="sidebar-action-list">
                    <div class="sidebar-list-item">
                        <div class="product-image pos-rel">
                            <a href="shop-details.html" class=""><img src="assets/img/product/product-6.png"
                                    alt="img"></a>
                        </div>
                        <div class="product-desc">
                            <div class="product-name">
                                <a href="shop-details.html">Giardino Tools</a>
                            </div>
                            <div class="product-pricing">
                                <span class="item-number">1 ×</span>
                                <span class="price-now">$24.00</span>
                            </div>
                            <button class="remove-item">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="sidebar-list-item">
                        <div class="product-image pos-rel">
                            <a href="shop-details.html" class=""><img src="assets/img/product/product-8.png"
                                    alt="img"></a>
                        </div>
                        <div class="product-desc">
                            <div class="product-name">
                                <a href="shop-details.html">Bloom Season</a>
                            </div>
                            <div class="product-pricing">
                                <span class="item-number">1 ×</span>
                                <span class="price-now">$12.00</span>
                            </div>
                            <button class="remove-item">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="sidebar-list-item">
                        <div class="product-image pos-rel">
                            <a href="shop-details.html" class=""><img src="assets/img/product/product-10.png"
                                    alt="img"></a>
                        </div>
                        <div class="product-desc">
                            <div class="product-name">
                                <a href="shop-details.html">the best dirt</a>
                            </div>
                            <div class="product-pricing">
                                <span class="item-number">1 ×</span>
                                <span class="price-now">$42.00</span>
                            </div>
                            <button class="remove-item">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-price-total">
                    <span>Subtotal :</span>
                    <span class="subtotal-price">$78.00</span>
                </div>
                <div class="sidebar-action-btn">
                    <a href="cart.html" class="fill-btn">View cart</a>
                    <a href="checkout.html" class="border-btn">Checkout</a>
                </div>
            </div>
        </div>

        <!-- side toggle end -->

        <!-- banner area start  -->
        @php
            $banners = isset($banners) ? $banners : (class_exists(\App\Models\Banner::class) 
                ? \App\Models\Banner::active()->ordered()->get() 
                : collect());
        @endphp
        @if($banners->count() > 0)
            <div class="banner-area banner-area2 pos-rel">
                <div class="swiper-container slider__active">
                    <div class="swiper-wrapper">
                        @foreach($banners as $banner)
                            <div class="swiper-slide">
                                <div class="single-banner single-banner-2 banner-850 d-flex align-items-center pos-rel">
                                    @if($banner->image_path)
                                        <div class="banner-bg banner-bg2 banner-bg2-1"
                                            data-background="{{ asset('storage/'.$banner->image_path) }}"></div>
                                    @endif
                                    <div class="container pos-rel">
                                        <div class="row align-items-center">
                                            <div class="col-lg-8 col-md-12">
                                                <div class="banner-content banner-content2 banner-content2-1">
                                                    @if($banner->subtitle)
                                                        <div class="banner-meta-text" data-animation="fadeInUp" data-delay=".3s">
                                                            <span>{{ $banner->subtitle }}</span>
                                                        </div>
                                                    @endif
                                                    @if($banner->title)
                                                        <h1 class="banner-title" data-animation="fadeInUp" data-delay=".5s">
                                                            {{ $banner->title }}
                                                        </h1>
                                                    @endif
                                                    @if($banner->description)
                                                        <div class="banner-text" data-animation="fadeInUp" data-delay=".7s">
                                                            <p>{{ $banner->description }}</p>
                                                        </div>
                                                    @endif
                                                    <div class="banner-btn" data-animation="fadeInUp" data-delay=".9s">
                                                        @if($banner->button_text && $banner->button_link)
                                                            <a href="{{ $banner->button_link }}" class="fill-btn-rounded">
                                                                {{ $banner->button_text }}<i class="fal fa-long-arrow-right"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="slider-nav d-none">
                        <div class="slider-button-prev">
                            <i class="fal fa-long-arrow-left"></i>
                        </div>
                        <div class="slider-button-next">
                            <i class="fal fa-long-arrow-right"></i>
                        </div>
                    </div>
                </div>
                <div class="slider-pagination slider2-pagination d-none"></div>
            </div>
        @endif
        <!-- banner area end  -->

        <!-- Dynamic Content Area -->
        @yield('content')

        <!-- Static Sections (Fallback if no CMS content) -->
        @if(!isset($sections) || $sections->count() === 0)
        <!-- about area start  -->
        <section class="about-area style-2 pt-120 pb-140">
            <div class="container">
                <div class="row align-items-center wow fadeInUp" data-wow-delay=".3s">
                    <div class="col-lg-6">
                        <div class="about-thumb-group mb-60">
                            <img class="about-group-thumb about-group-thumb1"
                                src="assets/img/about/about-group-thumb1.jpg" alt="img not found">
                            <img class="about-group-thumb about-group-thumb2"
                                src="assets/img/about/about-group-thumb2.jpg" alt="img not found">
                            <img class="about-group-thumb about-group-thumb3"
                                src="assets/img/about/about-group-thumb3.jpg" alt="img not found">
                            <img class="about-group-thumb about-group-thumb4"
                                src="assets/img/about/about-group-thumb4.jpg" alt="img not found">
                            <img class="about-group-thumb about-group-thumb5"
                                src="assets/img/about/about-group-thumb5.jpg" alt="img not found">
                            <img class="about-group-thumb about-group-thumb6"
                                src="assets/img/about/about-group-thumb6.jpg" alt="img not found">
                            <img class="about-group-thumb about-group-thumb7"
                                src="assets/img/about/about-group-thumb7.jpg" alt="img not found">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content mb-60 style-2">
                            <div class="section-title style-2">
                                <span class="section-subtitle">[ since from 2000 ]</span>
                                <h2 class="section-main-title mb-20">
                                    Gardeny offers a full-service
                                </h2>
                            </div>
                            <p class="mb-30">
                                Most gardens consist of a mix of natural and constructed
                                elements, although even very 'natural' gardens are always an
                                inherently artificial creation. Natural elements present in a
                                garden principally.
                            </p>
                            <div class="about-work-progress">
                                <div class="bd-skill--content">
                                    <div class="bd-skill__wrapper mb-35">
                                        <div class="bd-skill--title__wrapper">
                                            <h5 class="bd-skill--title">landscaping ground</h5>
                                            <span>75%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar wow slideInLeft" data-wow-duration="1s"
                                                data-wow-delay="0.5s" role="progressbar" style="width: 75%"
                                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bd-skill__wrapper mb-35">
                                        <div class="bd-skill--title__wrapper">
                                            <h5 class="bd-skill--title">soil re-building</h5>
                                            <span>82%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar wow slideInLeft" data-wow-duration="1s"
                                                data-wow-delay="0.6s" role="progressbar" style="width: 82%"
                                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="about-btn">
                                <a href="contact.html" class="border-btn-rounded">
                                    learn more<i class="fal fa-long-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- about area end  -->

        <!-- clients area start  -->
        <div class="clients-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="clients-wrapper clients-container-wrapped style-2">
                            <div class="swiper-container client-active wow fadeInUp" data-wow-delay=".3s">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="client-single">
                                            <a href="#"><img src="assets/img/client/client-1.png"
                                                    alt="img not found"></a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="client-single">
                                            <a href="#"><img src="assets/img/client/client-2.png"
                                                    alt="img not found"></a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="client-single">
                                            <a href="#"><img src="assets/img/client/client-3.png"
                                                    alt="img not found"></a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="client-single">
                                            <a href="#"><img src="assets/img/client/client-4.png"
                                                    alt="img not found"></a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="client-single">
                                            <a href="#"><img src="assets/img/client/client-5.png"
                                                    alt="img not found"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- clients area end  -->

        <!-- services area start  -->
        <section class="services-area pt-200 pb-60 wow fadeInUp" data-wow-delay=".3s">
            <div class="services-title-bg">
                <img src="assets/img/bg/services-title-bg.jpg" alt="img not found">
            </div>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="section-title style-2 text-center services-title-style-2">
                            <span class="section-subtitle">[ <span>services</span> ]</span>
                            <h2 class="section-main-title mb-45">What we provide</h2>
                        </div>
                    </div>
                </div>
                <div class="services-wrapper pb-10">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="single-service single-service-default mb-30">
                                <div class="service-icon">
                                    <img src="assets/img/icon/s-icon1.png" alt="service icon">
                                </div>
                                <div class="single-service-content">
                                    <span class="service-number"></span>
                                    <h4 class="service-title">
                                        <a href="service-details.html">Garden landscaping</a>
                                    </h4>
                                    <p>
                                        The laying out and care of a plot of ground devoted
                                        partially or wholly.
                                    </p>
                                    <a href="service-details.html" class="text-btn"><i
                                            class="fal fa-long-arrow-right"></i>read more<i
                                            class="fal fa-long-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="single-service single-service-default mb-30">
                                <div class="service-icon">
                                    <img src="assets/img/icon/s-icon2.png" alt="service icon">
                                </div>
                                <div class="single-service-content">
                                    <span class="service-number"></span>
                                    <h4 class="service-title">
                                        <a href="service-details.html">soil making & carbo</a>
                                    </h4>
                                    <p>
                                        The laying out and care of a plot of ground devoted
                                        partially or wholly.
                                    </p>
                                    <a href="service-details.html" class="text-btn"><i
                                            class="fal fa-long-arrow-right"></i>read more<i
                                            class="fal fa-long-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="single-service single-service-default mb-30">
                                <div class="service-icon">
                                    <img src="assets/img/icon/s-icon3.png" alt="service icon">
                                </div>
                                <div class="single-service-content">
                                    <span class="service-number"></span>
                                    <h4 class="service-title">
                                        <a href="service-details.html">planting plants</a>
                                    </h4>
                                    <p>
                                        The laying out and care of a plot of ground devoted
                                        partially or wholly.
                                    </p>
                                    <a href="service-details.html" class="text-btn"><i
                                            class="fal fa-long-arrow-right"></i>read more<i
                                            class="fal fa-long-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="services-action-wrapper style-2 wow fadeInUp" data-wow-delay=".3s">
                    <div class="service-action-bg-shape">
                        <img src="assets/img/shape/pattern-1270.png" alt="img not found">
                    </div>
                    <h3>
                        <i class="flaticon-house"></i>Provides Hassle-Free Backyard
                        Transformations with Artistic Solutions.
                    </h3>
                    <a href="contact.html" class="fill-btn-rounded"><i class="fal fa-farm"></i><span>Get a
                            Quote</span></a>
                </div>
            </div>
        </section>

        <!-- services area end  -->

        <!-- portfolio area start  -->
        <section class="portfolio-area pt-120 pb-90 portfolio-bg portfolio-full-width">
            <div class="container">
                <div class="row justify-content-center wow fadeInUp" data-wow-delay=".3s">
                    <div class="col-lg-8">
                        <div class="section-title style-2 text-center">
                            <span class="section-subtitle">[ showcase ]</span>
                            <h2 class="section-main-title mb-45">watch our gallery</h2>
                        </div>
                    </div>
                </div>
                <div class="portfolio-wrapper portfolio-slide-wrapper slider-drag mb-30 wow fadeInUp"
                    data-wow-delay=".3s">
                    <div class="swiper-container portfolio-active-style-2">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="portfolio-single portfolio-hover-style style-2">
                                    <div class="portfolio-thumb">
                                        <a href="portfolio-details.html"><img
                                                src="assets/img/portfolio/portfolio-14.jpg" alt="img not found"></a>
                                        <div class="portfolio-content">
                                            <a href="portfolio-details.html" class="portfolio-hover-bg"></a>
                                            <div class="portfolio-inner">
                                                <div class="portfolio-inner-text">
                                                    <span class="portfolio-tag">gardening</span>
                                                    <h4 class="portfolio-title">
                                                        <a href="portfolio-details.html">lawn and garden
                                                            maintenance</a>
                                                    </h4>
                                                </div>
                                                <a href="assets/img/portfolio/portfolio-14.jpg"
                                                    class="icon-btn image-popups"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="portfolio-single portfolio-hover-style style-2">
                                    <div class="portfolio-thumb">
                                        <a href="portfolio-details.html"><img
                                                src="assets/img/portfolio/portfolio-15.jpg" alt="img not found"></a>
                                        <div class="portfolio-content">
                                            <a href="portfolio-details.html" class="portfolio-hover-bg"></a>
                                            <div class="portfolio-inner">
                                                <div class="portfolio-inner-text">
                                                    <span class="portfolio-tag">gardening</span>
                                                    <h4 class="portfolio-title">
                                                        <a href="portfolio-details.html">Tree-Trimming & Removal</a>
                                                    </h4>
                                                </div>
                                                <a href="assets/img/portfolio/portfolio-15.jpg"
                                                    class="icon-btn image-popups"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="portfolio-single portfolio-hover-style style-2">
                                    <div class="portfolio-thumb">
                                        <a href="portfolio-details.html"><img
                                                src="assets/img/portfolio/portfolio-16.jpg" alt="img not found"></a>
                                        <div class="portfolio-content">
                                            <a href="portfolio-details.html" class="portfolio-hover-bg"></a>
                                            <div class="portfolio-inner">
                                                <div class="portfolio-inner-text">
                                                    <span class="portfolio-tag">gardening</span>
                                                    <h4 class="portfolio-title">
                                                        <a href="portfolio-details.html">Junk Removal</a>
                                                    </h4>
                                                </div>
                                                <a href="assets/img/portfolio/portfolio-16.jpg"
                                                    class="icon-btn image-popups"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="portfolio-single portfolio-hover-style style-2">
                                    <div class="portfolio-thumb">
                                        <a href="portfolio-details.html"><img
                                                src="assets/img/portfolio/portfolio-17.jpg" alt="img not found"></a>
                                        <div class="portfolio-content">
                                            <a href="portfolio-details.html" class="portfolio-hover-bg"></a>
                                            <div class="portfolio-inner">
                                                <div class="portfolio-inner-text">
                                                    <span class="portfolio-tag">gardening</span>
                                                    <h4 class="portfolio-title">
                                                        <a href="portfolio-details.html">watering plants</a>
                                                    </h4>
                                                </div>
                                                <a href="assets/img/portfolio/portfolio-17.jpg"
                                                    class="icon-btn image-popups"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="portfolio-single portfolio-hover-style style-2">
                                    <div class="portfolio-thumb">
                                        <a href="portfolio-details.html"><img
                                                src="assets/img/portfolio/portfolio-18.jpg" alt="img not found"></a>
                                        <div class="portfolio-content">
                                            <a href="portfolio-details.html" class="portfolio-hover-bg"></a>
                                            <div class="portfolio-inner">
                                                <div class="portfolio-inner-text">
                                                    <span class="portfolio-tag">gardening</span>
                                                    <h4 class="portfolio-title">
                                                        <a href="portfolio-details.html">Mowing the grass</a>
                                                    </h4>
                                                </div>
                                                <a href="assets/img/portfolio/portfolio-18.jpg"
                                                    class="icon-btn image-popups"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="portfolio-style-2-pagination-wrapper mt-50">
                            <div class="portfolio-pagination portfolio-style-2-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- portfolio area end  -->

        <!-- pricing-cta area start  -->
        <section class="pricing-cta pt-120 pb-120 fix">
            <div class="container">
                <div class="row wow fadeInUp" data-wow-delay=".3s">
                    <div class="col-lg-8">
                        <div class="section-title style-2">
                            <span class="section-subtitle">[ call to action ]</span>
                            <h2 class="section-main-title mb-45">estimate price</h2>
                        </div>
                    </div>
                </div>
                <div class="pricing-cta-wrapper wow fadeInUp" data-wow-delay=".3s">
                    <div class="pricing-cta-inner">
                        <div class="row">
                            <div class="col-xl-8">
                                <div class="pricing-cta-form">
                                    <form action="#">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="single-input-field field-name">
                                                    <label for="name">full name</label>
                                                    <input type="text" placeholder="enter here..." name="name"
                                                        id="name">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="single-input-field field-email">
                                                    <label for="email">email address</label>
                                                    <input type="text" placeholder="enter here..." name="email"
                                                        id="email">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="single-input-field field-number">
                                                    <label for="number">phone number</label>
                                                    <input type="text" placeholder="enter here..." name="number"
                                                        id="number">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="single-input-field field-address">
                                                    <label for="address">address</label>
                                                    <input type="text" placeholder="enter here..." name="address"
                                                        id="address">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="select-service-button">
                                                    <div class="single-input-field field-service">
                                                        <label for="select-service">name of service</label>
                                                        <select class="select-service" name="select-service"
                                                            id="select-service">
                                                            <option value="1">gardening</option>
                                                            <option value="2">watering</option>
                                                            <option value="3">triming</option>
                                                            <option value="4">planting</option>
                                                            <option value="5">cleaning</option>
                                                        </select>
                                                    </div>
                                                    <a href="#" class="fill-btn">get a quote<i
                                                            class="fal fa-long-arrow-right"></i></a>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="estimated-price-wrapper">
                                                    <p>estimated price</p>
                                                    <span class="es-price-value">$45,000</span>
                                                    <img class="es-price-tag" src="assets/img/icon/price-tag.png"
                                                        alt="img not found">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="pricing-cta-img">
                                    <img src="assets/img/bg/pricing-cta-img.png" alt="img not found">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- pricing-cta area end  -->

        <!-- testimonial-area start  -->
        <section class="testimonial-area pt-120 pb-110">
            <div class="testimonial-bg-style-2">
                <img src="assets/img/bg/dot-map.png" alt="img not found">
            </div>
            <div class="container">
                <div class="row justify-content-center wow fadeInUp" data-wow-delay=".3s">
                    <div class="col-lg-8">
                        <div class="section-title style-2 text-center">
                            <span class="section-subtitle">[ testimonials ]</span>
                            <h2 class="section-main-title mb-45">people say about us</h2>
                        </div>
                    </div>
                </div>
                <div class="testimonial-slide-wrapper style-2 wow fadeInUp" data-wow-delay=".3s">
                    <div class="testimonial-content-wrapper slider-drag">
                        <div class="swiper-container testimonial-content-style-2">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="testimonial-single style-2">
                                        <div class="testimonial-content">
                                            <div class="item-counter">01</div>
                                            <div class="testimonial-icon">
                                                <i class="fas fa-quote-right"></i>
                                            </div>
                                            <p class="author-quote">
                                                “ started with a simple idea: Deliver quality,
                                                well-designed landscape for home, business and public
                                                spaces. ”
                                            </p>
                                            <div class="author-info">
                                                <span class="author-designation">head of idea</span>
                                                <h4 class="author-name">humble d. dow</h4>
                                            </div>
                                            <div class="author-img">
                                                <img src="assets/img/testimonial/author-2.jpg" alt="img not found">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="testimonial-single style-2">
                                        <div class="testimonial-content">
                                            <div class="item-counter">02</div>
                                            <div class="testimonial-icon">
                                                <i class="fas fa-quote-right"></i>
                                            </div>
                                            <p class="author-quote">
                                                “ One of the best gardening tips you'll ever get is to
                                                plan your new garden near a water source.
                                            </p>
                                            <div class="author-info">
                                                <span class="author-designation">founder</span>
                                                <h4 class="author-name">rosalina d. william</h4>
                                            </div>
                                            <div class="author-img">
                                                <img src="assets/img/testimonial/author-3.jpg" alt="img not found">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="testimonial-single style-2">
                                        <div class="testimonial-content">
                                            <div class="item-counter">03</div>
                                            <div class="testimonial-icon">
                                                <i class="fas fa-quote-right"></i>
                                            </div>
                                            <p class="author-quote">
                                                “ When starting a garden, one of the top pieces of
                                                advice is o invest in soil that is nutrient rich &
                                                well drained. ”
                                            </p>
                                            <div class="author-info">
                                                <span class="author-designation">ceo of halim co.</span>
                                                <h4 class="author-name">miranda h. halim</h4>
                                            </div>
                                            <div class="author-img">
                                                <img src="assets/img/testimonial/author-4.jpg" alt="img not found">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- testimonial-area end  -->

        <!-- faq area start  -->
        <div class="faq-area style-3 pt-120 pb-60">
            <div class="container">
                <div class="row wow fadeInUp" data-wow-delay=".3s">
                    <div class="col-lg-6">
                        <div class="faq-img mb-60">
                            <img src="assets/img/about/faq-thumb.png" alt="img not found">
                            <div class="faq-thumb">
                                <img src="assets/img/logo/gm-thumb.png" alt="img not found">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="section-title style-2">
                            <span class="section-subtitle">[ faq ]</span>
                            <h2 class="section-main-title mb-55">some questions</h2>
                        </div>
                        <div class="faq-wrapper mb-60">
                            <div class="gm-faq">
                                <div class="accordion" id="accordionExample-st-2">
                                    <div class="gm-faq-group">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOne-st-2">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseOne-st-2"
                                                    aria-expanded="false" aria-controls="collapseOne-st-2">
                                                    What does an inch of water mean?
                                                </button>
                                            </h2>
                                            <div id="collapseOne-st-2" class="accordion-collapse collapse"
                                                aria-labelledby="headingOne-st-2"
                                                data-bs-parent="#accordionExample-st-2">
                                                <div class="accordion-body">
                                                    Gardens consist of a mix of natural and constructed
                                                    elements, although even very 'natural' gardens are
                                                    always an inherently artificial creation. Natural
                                                    elements present in a garden principally.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingTwo-st-2">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseTwo-st-2"
                                                    aria-expanded="true" aria-controls="collapseTwo-st-2">
                                                    How does gardening make you feel?
                                                </button>
                                            </h2>
                                            <div id="collapseTwo-st-2" class="accordion-collapse collapse show"
                                                aria-labelledby="headingTwo-st-2"
                                                data-bs-parent="#accordionExample-st-2">
                                                <div class="accordion-body">
                                                    Gardens consist of a mix of natural and constructed
                                                    elements, although even very 'natural' gardens are
                                                    always an inherently artificial creation. Natural
                                                    elements present in a garden principally.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingThree-st-2">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseThree-st-2"
                                                    aria-expanded="false" aria-controls="collapseThree-st-2">
                                                    How often should I fertilize my plants?
                                                </button>
                                            </h2>
                                            <div id="collapseThree-st-2" class="accordion-collapse collapse"
                                                aria-labelledby="headingThree-st-2"
                                                data-bs-parent="#accordionExample-st-2">
                                                <div class="accordion-body">
                                                    Gardens consist of a mix of natural and constructed
                                                    elements, although even very 'natural' gardens are
                                                    always an inherently artificial creation. Natural
                                                    elements present in a garden principally.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingFour-st-2">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseFour-st-2"
                                                    aria-expanded="false" aria-controls="collapseFour-st-2">
                                                    Do gardens help the environment?
                                                </button>
                                            </h2>
                                            <div id="collapseFour-st-2" class="accordion-collapse collapse"
                                                aria-labelledby="headingFour-st-2"
                                                data-bs-parent="#accordionExample-st-2">
                                                <div class="accordion-body">
                                                    Gardens consist of a mix of natural and constructed
                                                    elements, although even very 'natural' gardens are
                                                    always an inherently artificial creation. Natural
                                                    elements present in a garden principally.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- faq area end  -->

        <!-- newsletter area start  -->
        <section class="newsletter-area">
            <div class="container">
                <div class="newsletter-wrapper">
                    <div class="row align-items-center wow fadeInUp" data-wow-delay=".3s">
                        <div class="col-lg-6">
                            <div class="newsletter-text">
                                <p>get weekly newsletter & offers</p>
                                <h2>classy offers too.</h2>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="newsletter-form">
                                <form action="#">
                                    <div class="newsletter-input-wrapper">
                                        <input type="email" placeholder="Email address">
                                        <button type="submit" class="fill-btn-rounded">
                                            subscribe<i class="fal fa-long-arrow-right"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- newsletter area end  -->

        <!-- blog-area start  -->
        <section class="blog-area blog-area-2 pb-75">
            <div class="container">
                <div class="row justify-content-center wow fadeInUp" data-wow-delay=".3s">
                    <div class="col-lg-8">
                        <div class="section-title text-center style-2">
                            <span class="section-subtitle">[ blog ]</span>
                            <h2 class="section-main-title mb-45">blog & insights</h2>
                        </div>
                    </div>
                </div>
                <div class="blog-wrapper wow fadeInUp" data-wow-delay=".3s">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="blog-single style-2 mb-45">
                                <div class="blog-thumb">
                                    <a href="blog-details.html"><img src="assets/img/blog/blog-11.jpg"
                                            alt="img not found"></a>
                                </div>
                                <div class="blog-content">
                                    <div class="blog-meta-list">
                                        <div class="blog-meta-single">
                                            <div class="blog-meta-text">
                                                <div class="blog-date">november 23, 2022</div>
                                            </div>
                                        </div>
                                        <div class="blog-meta-single">
                                            <div class="blog-meta-text"><span>by</span> domson</div>
                                        </div>
                                    </div>
                                    <h2 class="blog-title">
                                        <a href="blog-details.html">Most gardens consist of a mix of natural</a>
                                    </h2>
                                    <div class="blog-btn">
                                        <a href="blog-details.html" class="text-btn"><i
                                                class="fal fa-long-arrow-right"></i>read more<i
                                                class="fal fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="blog-single style-2 mb-45">
                                <div class="blog-thumb">
                                    <a href="blog-details.html"><img src="assets/img/blog/blog-12.jpg"
                                            alt="img not found"></a>
                                </div>
                                <div class="blog-content">
                                    <div class="blog-meta-list">
                                        <div class="blog-meta-single">
                                            <div class="blog-meta-text">
                                                <div class="blog-date">november 21, 2022</div>
                                            </div>
                                        </div>
                                        <div class="blog-meta-single">
                                            <div class="blog-meta-text"><span>by</span> jeson</div>
                                        </div>
                                    </div>
                                    <h2 class="blog-title">
                                        <a href="blog-details.html">Place your garden in a part of your yard</a>
                                    </h2>
                                    <div class="blog-btn">
                                        <a href="blog-details.html" class="text-btn"><i
                                                class="fal fa-long-arrow-right"></i>read more<i
                                                class="fal fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="blog-single style-2 mb-45">
                                <div class="blog-thumb">
                                    <a href="blog-details.html"><img src="assets/img/blog/blog-13.jpg"
                                            alt="img not found"></a>
                                </div>
                                <div class="blog-content">
                                    <div class="blog-meta-list">
                                        <div class="blog-meta-single">
                                            <div class="blog-meta-text">
                                                <div class="blog-date">november 19, 2022</div>
                                            </div>
                                        </div>
                                        <div class="blog-meta-single">
                                            <div class="blog-meta-text"><span>by</span> peter</div>
                                        </div>
                                    </div>
                                    <h2 class="blog-title">
                                        <a href="blog-details.html">you'll see it regularly of sight, out of mind</a>
                                    </h2>
                                    <div class="blog-btn">
                                        <a href="blog-details.html" class="text-btn"><i
                                                class="fal fa-long-arrow-right"></i>read more<i
                                                class="fal fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- blog-area end  -->
        @endif
        <!-- End Static Sections Fallback -->
    </main>

    <!-- footer area start  -->
    <footer data-background="" class="footer2-bg">
        <section class="footer-area footer-area2 footer-area2-bg pt-95 pb-55">
            <div class="container">
                <div class="footer2-inner footer-plant-shape">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="footer-widget footer2-widget footer2-widget1 mb-40">
                                @if(!empty($site->footer_logo_path))
                                    <div class="footer-logo mb-3">
                                        <a href="{{ route('home') }}">
                                            <img src="{{ asset('storage/'.$site->footer_logo_path) }}" alt="{{ $site->brand_name ?? config('app.name') }}" style="max-height: 60px;">
                                        </a>
                                    </div>
                                @elseif(!empty($site->logo_path))
                                    <div class="footer-logo mb-3">
                                        <a href="{{ route('home') }}">
                                            <img src="{{ asset('storage/'.$site->logo_path) }}" alt="{{ $site->brand_name ?? config('app.name') }}" style="max-height: 60px;">
                                        </a>
                                    </div>
                                @endif
                                @php
                                    $footerText = is_array($site->footer_text) ? ($site->footer_text[app()->getLocale()] ?? reset($site->footer_text)) : ($site->footer_text ?? '');
                                @endphp
                                @if($footerText)
                                    <div class="footer-text">
                                        <p>{{ $footerText }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($footerMenus->count() > 0)
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="footer-widget footer2-widget footer2-widget1 mb-40">
                                    <div class="footer-widget-title">
                                        <h4>{{ __('Main Pages') }}</h4>
                                    </div>
                                    <ul>
                                        @foreach($footerMenus as $menu)
                                            <li><a href="{{ $menu->full_url }}">{{ $menu->label }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        @if($servicesMenu->count() > 0)
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="footer-widget footer2-widget footer2-widget2 mb-40">
                                    <div class="footer-widget-title">
                                        <h4>{{ __('Our Services') }}</h4>
                                    </div>
                                    <ul>
                                        @foreach($servicesMenu as $menu)
                                            <li><a href="{{ $menu->full_url }}">{{ $menu->label }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="footer-widget footer2-widget footer2-widget3 mb-40">
                                <div class="footer-widget-title">
                                    <h4>get in touch</h4>
                                </div>
                                <div class="footer-contact">
                                    <ul>
                                        @if($site->contact_phone)
                                            <li>
                                                <div class="single-contact">
                                                    <div class="contact-icon">
                                                        <i class="fas fa-phone"></i>
                                                    </div>
                                                    <p><a href="tel:{{ $site->contact_phone }}">{{ $site->contact_phone }}</a></p>
                                                </div>
                                            </li>
                                        @endif
                                        @if($site->contact_email)
                                            <li>
                                                <div class="single-contact">
                                                    <div class="contact-icon">
                                                        <i class="fas fa-envelope-open"></i>
                                                    </div>
                                                    <p>
                                                        <a href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a>
                                                    </p>
                                                </div>
                                            </li>
                                        @endif
                                        @php
                                            $contactAddress = is_array($site->contact_address) ? ($site->contact_address[app()->getLocale()] ?? reset($site->contact_address)) : ($site->contact_address ?? '');
                                        @endphp
                                        @if($contactAddress)
                                            <li>
                                                <div class="single-contact">
                                                    <div class="contact-icon">
                                                        <i class="fas fa-map-marked-alt"></i>
                                                    </div>
                                                    <p>
                                                        <a href="#">{{ $contactAddress }}</a>
                                                    </p>
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                @if(isset($socialLinks) && is_array($socialLinks) && count($socialLinks) > 0)
                                    <div class="footer-social">
                                        <span>{{ __('Connect') }}:</span>
                                        <div class="social-links">
                                            <ul>
                                                @foreach($socialLinks as $platform => $url)
                                                    @if($url)
                                                        <li>
                                                            <a href="{{ $url }}" target="_blank"><i class="fab fa-{{ $platform }}"></i></a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="footer-widget footer2-widget footer2-widget4 mb-40">
                                <div class="footer-widget-title">
                                    <h4>instagram feeds</h4>
                                </div>
                                <div class="instagam-shots">
                                    <a href="#"><img src="assets/img/istagram/insta-1.jpg"
                                            alt="instagram-img"></a>
                                    <a href="#"><img src="assets/img/istagram/insta-2.jpg"
                                            alt="instagram-img"></a>
                                    <a href="#"><img src="assets/img/istagram/insta-3.jpg"
                                            alt="instagram-img"></a>
                                    <a href="#"><img src="assets/img/istagram/insta-4.jpg"
                                            alt="instagram-img"></a>
                                    <a href="#"><img src="assets/img/istagram/insta-5.jpg"
                                            alt="instagram-img"></a>
                                    <a href="#"><img src="assets/img/istagram/insta-6.jpg"
                                            alt="instagram-img"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="copyright-area copyright2-area">
            <div class="container">
                <div class="copyright2-inner">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-6">
                            <div class="footer-links">
                                <a href="#">environment soluton</a>
                                <a href="#">faq</a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="copyright-text copyright2-text">
                                @php
                                    $footerCopyright = is_array($site->footer_copyright) ? ($site->footer_copyright[app()->getLocale()] ?? reset($site->footer_copyright)) : ($site->footer_copyright ?? __('Copyright'));
                                    $brandName = is_array($site->brand_name) ? ($site->brand_name[app()->getLocale()] ?? reset($site->brand_name)) : ($site->brand_name ?? config('app.name'));
                                @endphp
                                {{ $footerCopyright }} &copy; {{ date('Y') }} {{ $brandName }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer area end  -->

    <!-- pre loader area start -->
    <div id="loading">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <div class="loading-icon text-center d-flex flex-column align-items-center justify-content-center">

                    @if(!empty($site->logo_path))
                        <img src="{{ asset('storage/'.$site->logo_path) }}" alt="{{ $site->brand_name ?? config('app.name') }}" style="max-height: 60px; margin-bottom: 20px;">
                    @else
                        <img src="assets/img/logo/logo-text.png" alt="">
                    @endif
                    @if(!empty($site->preloader_icon_path))
                        <img class="loading-logo" src="{{ asset('storage/'.$site->preloader_icon_path) }}" alt="{{ __('Loading') }}">
                    @else
                        <img class="loading-logo" src="assets/img/logo/preloader.svg" alt="{{ __('Loading') }}">
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- pre loader area end -->

    <!-- back to top start -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- back to top end -->

    <!-- JS here -->
    <script src="{{ asset('assets/js') }}/vendor/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js') }}/vendor/waypoints.min.js"></script>
    <script src="{{ asset('assets/js') }}/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js') }}/meanmenu.js"></script>
    <script src="{{ asset('assets/js') }}/swiper-bundle.min.js"></script>
    <script src="{{ asset('assets/js') }}/owl.carousel.min.js"></script>
    <script src="{{ asset('assets/js') }}/magnific-popup.min.js"></script>
    <script src="{{ asset('assets/js') }}/parallax.min.js"></script>
    <script src="{{ asset('assets/js') }}/backToTop.js"></script>
    <script src="{{ asset('assets/js') }}/jquery-ui-slider-range.js"></script>
    <script src="{{ asset('assets/js') }}/nice-select.min.js"></script>
    <script src="{{ asset('assets/js') }}/counterup.min.js"></script>
    <script src="{{ asset('assets/js') }}/ajax-form.js"></script>
    <script src="{{ asset('assets/js') }}/wow.min.js"></script>
    <script src="{{ asset('assets/js') }}/beforeafter.jquery-1.0.0.js"></script>
    <script src="{{ asset('assets/js') }}/isotope.pkgd.min.js"></script>
    <script src="{{ asset('assets/js') }}/imagesloaded.pkgd.min.js"></script>
    <script src="{{ asset('assets/js') }}/main.js"></script>
    
    @if(!empty($site->custom_scripts))
        <script>
            {!! $site->custom_scripts !!}
        </script>
    @endif
</body>

</html>
