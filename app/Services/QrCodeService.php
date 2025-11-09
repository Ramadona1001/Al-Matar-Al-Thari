<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate QR code for a coupon
     *
     * @param string $code The coupon code or identifier
     * @param int $size The size of the QR code (default: 300)
     * @return string The path to the generated QR code image
     */
    public function generateCouponQrCode(string $code, int $size = 300): string
    {
        $qrCodeData = json_encode([
            'type' => 'coupon',
            'code' => $code,
            'timestamp' => now()->timestamp,
        ]);

        $fileName = 'coupons/qr_' . Str::slug($code) . '_' . time() . '.png';
        $path = Storage::disk('public')->path($fileName);

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('coupons');

        // Generate QR code
        QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->generate($qrCodeData, $path);

        return Storage::url($fileName);
    }

    /**
     * Generate QR code for a digital card
     *
     * @param string $cardNumber The digital card number
     * @param int $size The size of the QR code (default: 300)
     * @return string The path to the generated QR code image
     */
    public function generateCardQrCode(string $cardNumber, int $size = 300): string
    {
        $qrCodeData = json_encode([
            'type' => 'card',
            'card_number' => $cardNumber,
            'timestamp' => now()->timestamp,
        ]);

        $fileName = 'cards/qr_' . Str::slug($cardNumber) . '_' . time() . '.png';
        $path = Storage::disk('public')->path($fileName);

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('cards');

        // Generate QR code
        QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->generate($qrCodeData, $path);

        return Storage::url($fileName);
    }

    /**
     * Generate QR code for a referral link
     *
     * @param string $referralCode The referral code
     * @param string $url The referral URL
     * @param int $size The size of the QR code (default: 300)
     * @return string The path to the generated QR code image
     */
    public function generateReferralQrCode(string $referralCode, string $url, int $size = 300): string
    {
        $qrCodeData = json_encode([
            'type' => 'referral',
            'code' => $referralCode,
            'url' => $url,
            'timestamp' => now()->timestamp,
        ]);

        $fileName = 'referrals/qr_' . Str::slug($referralCode) . '_' . time() . '.png';
        $path = Storage::disk('public')->path($fileName);

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('referrals');

        // Generate QR code
        QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->generate($url, $path); // Generate QR code with the URL directly

        return Storage::url($fileName);
    }

    /**
     * Generate QR code as SVG string (for inline display)
     *
     * @param string $data The data to encode
     * @param int $size The size of the QR code (default: 300)
     * @return string The SVG string
     */
    public function generateSvg(string $data, int $size = 300): string
    {
        return QrCode::format('svg')
            ->size($size)
            ->margin(1)
            ->generate($data);
    }

    /**
     * Generate QR code as base64 encoded image
     *
     * @param string $data The data to encode
     * @param int $size The size of the QR code (default: 300)
     * @return string The base64 encoded image
     */
    public function generateBase64(string $data, int $size = 300): string
    {
        $qrCode = QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->generate($data);

        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

    /**
     * Delete QR code file
     *
     * @param string $path The path to the QR code file
     * @return bool True if deleted successfully
     */
    public function deleteQrCode(string $path): bool
    {
        // Extract the file path from the URL
        $filePath = str_replace(Storage::url(''), '', $path);
        
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }

        return false;
    }

    /**
     * Validate QR code data
     *
     * @param string $qrData The QR code data (JSON string)
     * @return array|null Decoded data or null if invalid
     */
    public function validateQrCode(string $qrData): ?array
    {
        try {
            $data = json_decode($qrData, true);
            
            if (!$data || !isset($data['type'])) {
                return null;
            }

            // Validate timestamp (QR codes expire after 24 hours for security)
            if (isset($data['timestamp'])) {
                $timestamp = $data['timestamp'];
                $expiryTime = $timestamp + (24 * 60 * 60); // 24 hours
                
                if (now()->timestamp > $expiryTime) {
                    return null; // QR code expired
                }
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
}

