<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="description" content="Al-Matar Al-Thari - Digital Loyalty, Discount & Affiliate Marketing System">
    <title>@yield('meta_title', $metaTitle ?: __('Welcome')) - {{ $brandName }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', $metaDescription)">
    <meta name="keywords" content="@yield('meta_keywords', $metaKeywords)">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#3D4F60">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- Security Meta Tags -->
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com https://images.unsplash.com; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://images.unsplash.com; connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        crossorigin="anonymous">
    <!-- Main Stylesheet -->
    @if (app()->getLocale() === 'ar')
        <link rel="stylesheet" href="{{ asset('assets/css/styles-rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/styles-ltr.css') }}">
    @endif

    @if (!empty($site->favicon_path))
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $site->favicon_path) }}">
    @else
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
    @endif

    <!-- Dynamic Theme Colors -->
    <style>
        @php
            echo \App\Services\ThemeService::generateThemeStyles();
        @endphp
        
        /* Apply theme colors to website elements */
        :root {
            --brand-primary: var(--theme-primary-color, #1B4332);
            --brand-secondary: var(--theme-secondary-color, #D4AF37);
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--theme-primary-color, #1B4332);
            border-color: var(--theme-primary-color, #1B4332);
            color: var(--text-on-primary-color, #ffffff);
        }
        
        .btn-primary:hover {
            background-color: var(--gradient-end-color, #2D5016);
            border-color: var(--gradient-end-color, #2D5016);
            color: var(--text-on-primary-color, #ffffff);
        }
        
        .btn-outline-primary {
            color: var(--theme-primary-color, #1B4332);
            border-color: var(--theme-primary-color, #1B4332);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--theme-primary-color, #1B4332);
            border-color: var(--theme-primary-color, #1B4332);
            color: var(--text-on-primary-color, #ffffff);
        }
        
        /* Links */
        a:not(.btn) {
            color:var(--bg-secondary-color,#fff);
        }
        
        a:not(.btn):hover {
            color: var(--bg-secondary-color,#fff);
        }
        
        /* Text Colors */
        .text-primary {
            color: var(--theme-primary-color, #1B4332) !important;
        }
        
        .text-theme-primary {
            color: var(--theme-primary-color, #1B4332) !important;
        }
        
        .text-theme-secondary {
            color: var(--theme-secondary-color, #D4AF37) !important;
        }
        
        /* Background Colors */
        .bg-primary {
            background-color: var(--theme-primary-color, #1B4332) !important;
        }
        
        .bg-theme-primary {
            background-color: var(--theme-primary-color, #1B4332) !important;
        }
        
        .bg-theme-secondary {
            background-color: var(--theme-secondary-color, #D4AF37) !important;
        }
        
        /* Footer Styles */
        .footer-cta-section {
            background: linear-gradient(135deg, var(--gradient-start-color, #1B4332) 0%, var(--gradient-end-color, #2D5016) 100%) !important;
        }
        
        .footer-modern {
            background: var(--bg-dark-color, #1B4332) !important;
        }
        
        .footer-cta-primary-btn,
        .footer-cta-primary-btn:hover {
            background: var(--theme-secondary-color, #D4AF37) !important;
            color: var(--text-on-primary-color, #ffffff) !important;
        }
        
        .footer-logo-icon-modern {
            background: var(--theme-secondary-color, #D4AF37) !important;
            color: var(--bg-dark-color, #1B4332) !important;
        }
        
        /* Override inline styles with theme colors */
        section.footer-cta-section[style*="background"] {
            background: linear-gradient(135deg, var(--gradient-start-color, #1B4332) 0%, var(--gradient-end-color, #2D5016) 100%) !important;
        }
        
        footer.footer-modern[style*="background"] {
            background: var(--bg-dark-color, #1B4332) !important;
        }
        
        .footer-cta-primary-btn[style*="background"] {
            background: var(--theme-secondary-color, #D4AF37) !important;
            color: var(--text-on-primary-color, #ffffff) !important;
        }
        
        button[style*="background"][style*="#4BB543"] {
            background: var(--theme-secondary-color, #D4AF37) !important;
            color: var(--text-on-primary-color, #ffffff) !important;
        }
        
        .footer-logo-icon-modern[style*="background"] {
            background: var(--theme-secondary-color, #D4AF37) !important;
            color: var(--bg-dark-color, #1B4332) !important;
        }
    </style>

    @if(!empty($site->custom_styles))
      <style>
         {!! $site->custom_styles !!}
      </style>
   @endif

    @yield('styles')

</head>
