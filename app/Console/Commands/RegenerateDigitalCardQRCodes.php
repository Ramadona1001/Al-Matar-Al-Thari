<?php

namespace App\Console\Commands;

use App\Models\DigitalCard;
use App\Services\QrCodeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateDigitalCardQRCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'digital-card:regenerate-qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR codes for all digital cards that are missing files';

    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        parent::__construct();
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking digital cards for missing QR codes...');

        $digitalCards = DigitalCard::whereNotNull('qr_code')->get();
        $regenerated = 0;
        $skipped = 0;

        foreach ($digitalCards as $card) {
            $filePath = str_replace(Storage::url(''), '', $card->qr_code);
            $filePath = str_replace('/storage/', '', $filePath);
            
            if (!Storage::disk('public')->exists($filePath)) {
                $this->info("Regenerating QR code for card: {$card->card_number}");
                
                try {
                    $qrCodePath = $this->qrCodeService->generateCardQrCode($card->card_number);
                    $card->update(['qr_code' => $qrCodePath]);
                    $regenerated++;
                } catch (\Exception $e) {
                    $this->error("Failed to regenerate QR code for card {$card->card_number}: " . $e->getMessage());
                }
            } else {
                $skipped++;
            }
        }

        $this->info("Done! Regenerated: {$regenerated}, Skipped (already exists): {$skipped}");
        
        return 0;
    }
}

