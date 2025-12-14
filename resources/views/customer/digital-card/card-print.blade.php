<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('My Digital Card') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .business-card {
            width: 1050px;
            min-height: 600px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 40px;
            margin: 0 auto;
            position: relative;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-section img {
            max-height: 60px;
            max-width: 200px;
            object-fit: contain;
        }
        
        .logo-section h3 {
            color: #3D4F60;
            font-weight: 700;
            font-size: 28px;
            margin: 0;
        }
        
        .card-content {
            display: flex;
            align-items: center;
            gap: 40px;
            min-height: 400px;
        }
        
        .qr-section {
            flex: 0 0 280px;
            text-align: center;
        }
        
        .qr-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .qr-container img {
            width: 240px;
            height: 240px;
            display: block;
        }
        
        .qr-label {
            color: #6c757d;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .info-section {
            flex: 1;
            color: #3D4F60;
        }
        
        .customer-name {
            margin-bottom: 30px;
        }
        
        .customer-name h2 {
            color: #3D4F60;
            font-weight: 700;
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .customer-name p {
            color: #6c757d;
            font-size: 16px;
            margin: 0;
        }
        
        .card-details {
            background: rgba(61, 79, 96, 0.05);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .detail-item {
            margin-bottom: 20px;
        }
        
        .detail-item:last-child {
            margin-bottom: 0;
        }
        
        .detail-label {
            color: #6c757d;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        
        .detail-value {
            color: #3D4F60;
            font-size: 24px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }
        
        .status-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-badge.inactive {
            background: #dc3545;
        }
        
        .detail-row {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }
        
        .detail-row .detail-item {
            flex: 1;
        }
        
        .detail-value-small {
            color: #3D4F60;
            font-size: 18px;
            font-weight: 600;
        }
        
        .contact-info {
            margin-top: 20px;
        }
        
        .contact-info div {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .contact-info i {
            margin-right: 8px;
            width: 20px;
        }
        
        .gradient-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 50%, #1e40af 100%);
            border-radius: 0 0 20px 20px;
        }
    </style>
</head>
<body>
@php
    try {
        $site = \App\Models\SiteSetting::getSettings();
        $siteLogo = !empty($site->logo_path) ? public_path('storage/' . $site->logo_path) : null;
        $brandName = is_array($site->brand_name ?? null)
            ? ($site->brand_name[app()->getLocale()] ?? reset($site->brand_name ?? []))
            : ($site->brand_name ?? config('app.name'));
    } catch (\Exception $e) {
        $siteLogo = null;
        $brandName = config('app.name');
    }
    $user = $digitalCard->user;
    
    // Get QR code path
    $qrPath = null;
    if ($digitalCard->qr_code) {
        $qrFile = str_replace('/storage/', '', $digitalCard->qr_code);
        $qrFile = str_replace(asset('storage/'), '', $qrFile);
        $fullQrPath = storage_path('app/public/' . $qrFile);
        if (file_exists($fullQrPath)) {
            $qrPath = $fullQrPath;
        }
    }
@endphp

<div class="business-card">
    <!-- Logo at Top -->
    <div class="logo-section">
        @if($siteLogo && file_exists($siteLogo))
            <img src="{{ $siteLogo }}" alt="{{ $brandName }}">
        @else
            <h3>{{ $brandName }}</h3>
        @endif
    </div>

    <!-- Card Content -->
    <div class="card-content">
        <!-- QR Code - Left Side -->
        <div class="qr-section">
            @if($qrPath)
                <div class="qr-container">
                    <img src="{{ $qrPath }}" alt="QR Code">
                </div>
                <p class="qr-label">{{ __('Scan this QR code at the store') }}</p>
            @else
                <div class="qr-container" style="width: 240px; height: 240px; display: flex; align-items: center; justify-content: center; background: #f0f0f0;">
                    <span style="color: #ccc; font-size: 18px;">QR Code</span>
                </div>
            @endif
        </div>

        <!-- Customer Information - Right Side -->
        <div class="info-section">
            <div class="customer-name">
                <h2>{{ $user->first_name }} {{ $user->last_name }}</h2>
                <p>{{ __('Loyalty Member') }}</p>
            </div>

            <div class="card-details">
                <div class="detail-item">
                    <div class="detail-label">{{ __('Card Number') }}</div>
                    <div class="detail-value">{{ $digitalCard->card_number }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">{{ __('Status') }}</div>
                        <div>
                            @if($digitalCard->isActive())
                                <span class="status-badge">{{ __('Active') }}</span>
                            @else
                                <span class="status-badge inactive">{{ __('Inactive') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">{{ __('Expiry Date') }}</div>
                        <div class="detail-value-small">{{ $digitalCard->expiry_date->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>

            @if($user->email || $user->phone)
                <div class="contact-info">
                    @if($user->email)
                        <div><i class="fas fa-envelope"></i>{{ $user->email }}</div>
                    @endif
                    @if($user->phone)
                        <div><i class="fas fa-phone"></i>{{ $user->phone }}</div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Decorative Gradient Bar -->
    <div class="gradient-bar"></div>
</div>

</body>
</html>

