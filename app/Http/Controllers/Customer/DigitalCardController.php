<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DigitalCard;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

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

        // Generate QR code if doesn't exist
        if (!$digitalCard->qr_code) {
            $qrCodePath = $this->qrCodeService->generateCardQrCode($digitalCard->card_number);
            $digitalCard->update(['qr_code' => $qrCodePath]);
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
        $width = 800;
        $height = 500;
        
        // Create image
        $image = imagecreatetruecolor($width, $height);
        
        // Define colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 128, 128, 128);
        $primary = imagecolorallocate($image, 59, 130, 246); // Blue
        
        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $white);
        
        // Add border
        imagerectangle($image, 10, 10, $width - 10, $height - 10, $gray);
        
        // Add card number
        $fontSize = 5;
        $textX = 50;
        $textY = 150;
        imagestring($image, $fontSize, $textX, $textY, 'Card Number: ' . $digitalCard->card_number, $black);
        
        // Add user name
        $userName = $user->full_name ?? $user->name;
        $textY += 50;
        imagestring($image, $fontSize, $textX, $textY, 'Name: ' . $userName, $black);
        
        // Add expiry date
        $textY += 50;
        imagestring($image, $fontSize, $textX, $textY, 'Expires: ' . $digitalCard->expiry_date->format('Y-m-d'), $black);
        
        // Add QR code if exists
        if ($digitalCard->qr_code) {
            $qrPath = storage_path('app/public/' . str_replace('/storage/', '', $digitalCard->qr_code));
            if (file_exists($qrPath)) {
                $qrImage = imagecreatefrompng($qrPath);
                if ($qrImage) {
                    $qrSize = 200;
                    imagecopyresampled($image, $qrImage, $width - $qrSize - 50, $height - $qrSize - 50, 0, 0, $qrSize, $qrSize, imagesx($qrImage), imagesy($qrImage));
                    imagedestroy($qrImage);
                }
            }
        }
        
        // Output as PNG
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        imagedestroy($image);
        
        return $imageData;
    }
}

