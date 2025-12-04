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
        // Determine card type based on user activity or default to silver
        $cardType = 'silver';
        
        // You can add logic here to determine card type based on user activity
        // For example: if ($user->loyalty_points_balance > 1000) { $cardType = 'gold'; }
        
        $cardNumber = DigitalCard::generateUniqueCardNumber($cardType);
        
        // Generate QR code before creating the card
        $qrCodePath = $this->qrCodeService->generateCardQrCode($cardNumber);
        
        $digitalCard = DigitalCard::create([
            'card_number' => $cardNumber,
            'qr_code' => $qrCodePath,
            'type' => $cardType,
            'discount_percentage' => $this->getDiscountPercentage($cardType),
            'loyalty_points' => 0,
            'expiry_date' => now()->addYears(2), // Card valid for 2 years
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        return $digitalCard;
    }

    /**
     * Get discount percentage based on card type
     */
    private function getDiscountPercentage(string $type): float
    {
        $discounts = [
            'silver' => 5.00,
            'gold' => 10.00,
            'platinum' => 15.00,
        ];

        return $discounts[$type] ?? 5.00;
    }

    /**
     * Upgrade card type
     */
    public function upgrade(Request $request)
    {
        $user = auth()->user();
        $digitalCard = $user->digitalCard;

        if (!$digitalCard) {
            return redirect()->route('customer.digital-card.index')
                ->with('error', __('Digital card not found.'));
        }

        $validated = $request->validate([
            'card_type' => 'required|in:silver,gold,platinum',
        ]);

        // Check if upgrade is allowed (user must have higher loyalty points, etc.)
        // This is a simple implementation - you can add more complex logic

        $digitalCard->update([
            'type' => $validated['card_type'],
            'discount_percentage' => $this->getDiscountPercentage($validated['card_type']),
        ]);

        return redirect()->route('customer.digital-card.index')
            ->with('success', __('Card upgraded successfully.'));
    }
}

