<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DigitalCard;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DigitalCardController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->middleware(['auth', 'role:customer']);
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display the user's digital card
     */
    public function index()
    {
        $user = auth()->user();
        
        // Auto-generate card if doesn't exist
        $digitalCard = $user->digitalCard;
        
        if (!$digitalCard) {
            $digitalCard = $this->createDigitalCard($user);
        }

        // Generate QR code if doesn't exist or file doesn't exist
        if (!$digitalCard->qr_code) {
            $qrCodePath = $this->qrCodeService->generateCardQrCode($digitalCard->card_number);
            $digitalCard->update(['qr_code' => $qrCodePath]);
        } else {
            // Verify QR code file exists, regenerate if missing
            $filePath = str_replace(Storage::url(''), '', $digitalCard->qr_code);
            if (!Storage::disk('public')->exists($filePath)) {
                $qrCodePath = $this->qrCodeService->generateCardQrCode($digitalCard->card_number);
                $digitalCard->update(['qr_code' => $qrCodePath]);
            }
        }

        return view('customer.digital-card.index', compact('digitalCard'));
    }

    /**
     * Show the digital card details
     */
    public function show()
    {
        $user = auth()->user();
        $digitalCard = $user->digitalCard;

        if (!$digitalCard) {
            return redirect()->route('customer.digital-card.index')
                ->with('info', __('Your digital card will be created automatically.'));
        }

        return view('customer.digital-card.show', compact('digitalCard'));
    }

    /**
     * Download QR code for digital card
     */
    public function downloadQrCode()
    {
        $user = auth()->user();
        $digitalCard = $user->digitalCard;

        if (!$digitalCard) {
            return redirect()->route('customer.digital-card.index')
                ->with('error', __('Digital card not found.'));
        }

        // Generate QR code if doesn't exist
        if (!$digitalCard->qr_code) {
            $qrCodePath = $this->qrCodeService->generateCardQrCode($digitalCard->card_number);
            $digitalCard->update(['qr_code' => $qrCodePath]);
        }

        $filePath = str_replace(\Storage::url(''), '', $digitalCard->qr_code);
        $fullPath = \Storage::disk('public')->path($filePath);

        if (!file_exists($fullPath)) {
            // Regenerate if file doesn't exist
            $qrCodePath = $this->qrCodeService->generateCardQrCode($digitalCard->card_number);
            $digitalCard->update(['qr_code' => $qrCodePath]);
            $filePath = str_replace(\Storage::url(''), '', $qrCodePath);
            $fullPath = \Storage::disk('public')->path($filePath);
        }

        return response()->download($fullPath, 'digital-card-qr-' . $digitalCard->card_number . '.png');
    }

    /**
     * Create a digital card for the user
     */
    private function createDigitalCard($user)
    {
        // Default card type - no discount, only points system
        $cardType = 'standard';
        
        $cardNumber = DigitalCard::generateUniqueCardNumber($cardType);
        
        // Generate QR code before creating the card
        $qrCodePath = $this->qrCodeService->generateCardQrCode($cardNumber);
        
        $digitalCard = DigitalCard::create([
            'card_number' => $cardNumber,
            'qr_code' => $qrCodePath,
            'type' => $cardType,
            'loyalty_points' => 0,
            'expiry_date' => now()->addYears(2), // Card valid for 2 years
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        return $digitalCard;
    }

    /**
     * Download digital card as PNG image
     */
    public function downloadCard()
    {
        $user = auth()->user();
        $digitalCard = $user->digitalCard;

        if (!$digitalCard) {
            return redirect()->route('customer.digital-card.index')
                ->with('error', __('Digital card not found.'));
        }

        // Generate card image as PNG
        $image = $this->generateCardImage($digitalCard);
        
        return response($image)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="digital-card-' . $digitalCard->card_number . '.png"');
    }

    /**
     * Generate digital card image as PNG
     */
    private function generateCardImage($digitalCard)
    {
        $user = $digitalCard->user;
        $width = 1050;
        $height = 600;
        
        // Create image with gradient background
        $image = imagecreatetruecolor($width, $height);
        
        // Define colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $lightGray = imagecolorallocate($image, 248, 249, 250);
        $darkGray = imagecolorallocate($image, 61, 79, 96); // #3D4F60
        $mediumGray = imagecolorallocate($image, 108, 117, 125); // #6c757d
        $green = imagecolorallocate($image, 40, 167, 69); // #28a745
        $red = imagecolorallocate($image, 220, 53, 69); // #dc3545
        $blue1 = imagecolorallocate($image, 59, 130, 246); // #3b82f6
        $blue2 = imagecolorallocate($image, 37, 99, 235); // #2563eb
        $blue3 = imagecolorallocate($image, 30, 64, 175); // #1e40af
        
        // Fill background with gradient (simple gradient effect)
        $this->createGradientBackground($image, $width, $height, $white, $lightGray);
        
        // Padding
        $padding = 40;
        $contentWidth = $width - ($padding * 2);
        $contentHeight = $height - ($padding * 2);
        
        // Get site logo
        try {
            $site = \App\Models\SiteSetting::getSettings();
            $siteLogo = !empty($site->logo_path) ? storage_path('app/public/' . $site->logo_path) : null;
            $brandName = is_array($site->brand_name ?? null)
                ? ($site->brand_name[app()->getLocale()] ?? reset($site->brand_name ?? []))
                : ($site->brand_name ?? config('app.name'));
        } catch (\Exception $e) {
            $siteLogo = null;
            $brandName = config('app.name');
        }
        
        $currentY = $padding + 20;
        
        // Logo or Brand Name at top
        $logoHeight = 60;
        if ($siteLogo && file_exists($siteLogo)) {
            $logoImg = $this->loadImage($siteLogo);
            if ($logoImg) {
                $logoWidth = imagesx($logoImg);
                $logoHeight = imagesy($logoImg);
                $maxLogoWidth = 200;
                $maxLogoHeight = 60;
                $ratio = min($maxLogoWidth / $logoWidth, $maxLogoHeight / $logoHeight);
                $newLogoWidth = (int)($logoWidth * $ratio);
                $newLogoHeight = (int)($logoHeight * $ratio);
                $logoX = ($width - $newLogoWidth) / 2;
                imagecopyresampled($image, $logoImg, $logoX, $currentY, 0, 0, $newLogoWidth, $newLogoHeight, $logoWidth, $logoHeight);
                imagedestroy($logoImg);
                $currentY += $newLogoHeight + 40;
            }
        } else {
            // Draw brand name
            $fontPath = $this->getFontPath();
            if ($fontPath) {
                $fontSize = 28;
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $brandName);
                $textWidth = $bbox[4] - $bbox[0];
                $textX = ($width - $textWidth) / 2;
                imagettftext($image, $fontSize, 0, $textX, $currentY + 30, $darkGray, $fontPath, $brandName);
            } else {
                // Fallback to built-in font
                $textX = ($width - strlen($brandName) * 10) / 2;
                imagestring($image, 5, $textX, $currentY, $brandName, $darkGray);
            }
            $currentY += 60;
        }
        
        // Card content area
        $contentY = $currentY;
        $contentAreaHeight = $height - $contentY - $padding - 10;
        
        // Left side - QR Code
        $qrSize = 240;
        $qrX = $padding + 40;
        $qrY = $contentY + 20;
        
        // QR code background (white box)
        $qrPadding = 20;
        imagefilledrectangle($image, $qrX - $qrPadding, $qrY - $qrPadding, 
            $qrX + $qrSize + $qrPadding, $qrY + $qrSize + $qrPadding, $white);
        
        // Add QR code
        if ($digitalCard->qr_code) {
            $qrFile = str_replace('/storage/', '', $digitalCard->qr_code);
            $qrFile = str_replace(asset('storage/'), '', $qrFile);
            $qrPath = storage_path('app/public/' . $qrFile);
            
            if (file_exists($qrPath)) {
                $qrImage = imagecreatefrompng($qrPath);
                if ($qrImage) {
                    imagecopyresampled($image, $qrImage, $qrX, $qrY, 0, 0, $qrSize, $qrSize, imagesx($qrImage), imagesy($qrImage));
                    imagedestroy($qrImage);
                }
            }
        }
        
        // Right side - Customer Information
        $infoX = $qrX + $qrSize + 80;
        $infoY = $contentY + 30;
        
        // Customer Name
        $userName = $user->first_name . ' ' . $user->last_name;
        $fontPath = $this->getFontPath();
        if ($fontPath) {
            $nameFontSize = 36;
            $this->drawText($image, $nameFontSize, $infoX, $infoY + 40, $darkGray, $fontPath, $userName);
        } else {
            imagestring($image, 5, $infoX, $infoY + 10, $userName, $darkGray);
        }
        
        // Loyalty Member
        $memberY = $infoY + 70;
        $memberText = __('Loyalty Member');
        if ($fontPath) {
            $memberFontSize = 16;
            $this->drawText($image, $memberFontSize, $infoX, $memberY, $mediumGray, $fontPath, $memberText);
        } else {
            imagestring($image, 3, $infoX, $memberY - 10, $memberText, $mediumGray);
        }
        
        // Card details box
        $detailsY = $memberY + 50;
        $detailsWidth = $width - $infoX - $padding;
        $detailsHeight = 180;
        $detailsBgR = 248; $detailsBgG = 249; $detailsBgB = 250;
        for ($i = $detailsY; $i < $detailsY + $detailsHeight; $i++) {
            $color = imagecolorallocate($image, $detailsBgR, $detailsBgG, $detailsBgB);
            imageline($image, $infoX, $i, $infoX + $detailsWidth - 40, $i, $color);
        }
        
        // Card Number
        $detailStartY = $detailsY + 30;
        $cardNumberLabel = strtoupper(__('Card Number'));
        if ($fontPath) {
            $labelFontSize = 11;
            $this->drawText($image, $labelFontSize, $infoX + 25, $detailStartY, $mediumGray, $fontPath, $cardNumberLabel);
            $valueFontSize = 24;
            $monoFont = $this->getMonospaceFontPath() ?: $fontPath;
            $this->drawText($image, $valueFontSize, $infoX + 25, $detailStartY + 35, $darkGray, $monoFont, $digitalCard->card_number);
        } else {
            imagestring($image, 2, $infoX + 25, $detailStartY - 10, $cardNumberLabel, $mediumGray);
            imagestring($image, 5, $infoX + 25, $detailStartY + 10, $digitalCard->card_number, $darkGray);
        }
        
        // Status and Expiry Date
        $statusY = $detailStartY + 80;
        
        // Status
        $statusLabel = strtoupper(__('Status'));
        if ($fontPath) {
            $labelFontSize = 11;
            $this->drawText($image, $labelFontSize, $infoX + 25, $statusY, $mediumGray, $fontPath, $statusLabel);
        } else {
            imagestring($image, 2, $infoX + 25, $statusY - 10, $statusLabel, $mediumGray);
        }
        
        $statusColor = $digitalCard->isActive() ? $green : $red;
        $statusText = $digitalCard->isActive() ? __('Active') : __('Inactive');
        $statusBadgeWidth = 120;
        $statusBadgeHeight = 30;
        imagefilledrectangle($image, $infoX + 25, $statusY + 15, $infoX + 25 + $statusBadgeWidth, $statusY + 15 + $statusBadgeHeight, $statusColor);
        
        if ($fontPath) {
            $statusFontSize = 14;
            $statusBbox = imagettfbbox($statusFontSize, 0, $fontPath, $statusText);
            $statusTextWidth = $statusBbox[4] - $statusBbox[0];
            $statusTextX = $infoX + 25 + ($statusBadgeWidth - $statusTextWidth) / 2;
            imagettftext($image, $statusFontSize, 0, $statusTextX, $statusY + 35, $white, $fontPath, $statusText);
        } else {
            $statusTextX = $infoX + 25 + ($statusBadgeWidth - strlen($statusText) * 7) / 2;
            imagestring($image, 3, $statusTextX, $statusY + 20, $statusText, $white);
        }
        
        // Expiry Date
        $expiryX = $infoX + 180;
        $expiryLabel = strtoupper(__('Expiry Date'));
        $expiryDate = $digitalCard->expiry_date->format('Y-m-d');
        if ($fontPath) {
            $this->drawText($image, $labelFontSize, $expiryX, $statusY, $mediumGray, $fontPath, $expiryLabel);
            $expiryFontSize = 18;
            $this->drawText($image, $expiryFontSize, $expiryX, $statusY + 30, $darkGray, $fontPath, $expiryDate);
        } else {
            imagestring($image, 2, $expiryX, $statusY - 10, $expiryLabel, $mediumGray);
            imagestring($image, 4, $expiryX, $statusY + 15, $expiryDate, $darkGray);
        }
        
        // Contact info
        if ($user->email || $user->phone) {
            $contactY = $detailsY + $detailsHeight + 30;
            if ($user->email) {
                if ($fontPath) {
                    $this->drawText($image, 14, $infoX, $contactY, $mediumGray, $fontPath, $user->email);
                } else {
                    imagestring($image, 3, $infoX, $contactY - 10, $user->email, $mediumGray);
                }
                $contactY += 25;
            }
            if ($user->phone) {
                if ($fontPath) {
                    $this->drawText($image, 14, $infoX, $contactY, $mediumGray, $fontPath, $user->phone);
                } else {
                    imagestring($image, 3, $infoX, $contactY - 10, $user->phone, $mediumGray);
                }
            }
        }
        
        // Gradient bar at bottom
        $barHeight = 6;
        $this->createGradientBar($image, 0, $height - $barHeight, $width, $barHeight, $blue1, $blue2, $blue3);
        
        // Output as PNG
        ob_start();
        imagepng($image, null, 9); // High quality
        $imageData = ob_get_contents();
        ob_end_clean();
        
        imagedestroy($image);
        
        return $imageData;
    }
    
    /**
     * Create gradient background
     */
    private function createGradientBackground($image, $width, $height, $color1, $color2)
    {
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = (int)(255 - (255 - 248) * $ratio);
            $g = (int)(255 - (255 - 249) * $ratio);
            $b = (int)(255 - (255 - 250) * $ratio);
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }
    }
    
    /**
     * Create gradient bar
     */
    private function createGradientBar($image, $x, $y, $width, $height, $color1, $color2, $color3)
    {
        $third = $width / 3;
        for ($i = 0; $i < $width; $i++) {
            if ($i < $third) {
                $ratio = $i / $third;
                $r = (int)(59 - (59 - 37) * $ratio);
                $g = (int)(130 - (130 - 99) * $ratio);
                $b = (int)(246 - (246 - 235) * $ratio);
            } elseif ($i < $third * 2) {
                $ratio = ($i - $third) / $third;
                $r = (int)(37 - (37 - 30) * $ratio);
                $g = (int)(99 - (99 - 64) * $ratio);
                $b = (int)(235 - (235 - 175) * $ratio);
            } else {
                $r = 30; $g = 64; $b = 175;
            }
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, $x + $i, $y, $x + $i, $y + $height, $color);
        }
    }
    
    /**
     * Load image file (PNG, JPG, etc.)
     */
    private function loadImage($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'png':
                return imagecreatefrompng($path);
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($path);
            case 'gif':
                return imagecreatefromgif($path);
            default:
                return null;
        }
    }
    
    /**
     * Get font path (using system fonts)
     */
    private function getFontPath()
    {
        // Try common system fonts
        $fonts = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/TTF/arial.ttf',
            '/System/Library/Fonts/Helvetica.ttc',
            'C:/Windows/Fonts/arial.ttf',
        ];
        
        foreach ($fonts as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }
        
        // Fallback: use built-in font (will use imagestring)
        return null;
    }
    
    /**
     * Get monospace font path
     */
    private function getMonospaceFontPath()
    {
        $fonts = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSansMono-Bold.ttf',
            '/usr/share/fonts/TTF/courier.ttf',
            '/System/Library/Fonts/Courier.ttc',
            'C:/Windows/Fonts/courbd.ttf',
        ];
        
        foreach ($fonts as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }
        
        return $this->getFontPath(); // Fallback to regular font
    }
    
    /**
     * Draw text with fallback to built-in font
     */
    private function drawText($image, $size, $x, $y, $color, $fontPath, $text)
    {
        if ($fontPath && function_exists('imagettftext')) {
            imagettftext($image, $size, 0, $x, $y, $color, $fontPath, $text);
        } else {
            // Fallback to built-in font
            $fontMap = [11 => 2, 14 => 3, 16 => 3, 18 => 4, 24 => 4, 28 => 5, 36 => 5];
            $fontSize = $fontMap[$size] ?? 3;
            imagestring($image, $fontSize, $x, $y - 15, $text, $color);
        }
    }
}

