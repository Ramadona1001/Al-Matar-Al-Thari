<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Affiliate;
use App\Models\AffiliateSale;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AffiliateRewardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Transaction $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if transaction is completed (required)
        if ($this->transaction->status !== 'completed') {
            return;
        }

        // Check if transaction is refunded (skip if refunded)
        if ($this->transaction->status === 'refunded') {
            return;
        }

        // Check if user was referred
        $user = $this->transaction->user;
        $referrer = $this->getReferrer($user);

        if (!$referrer) {
            return;
        }

        // Prevent self-referral
        if ($referrer->id === $user->id) {
            return;
        }

        // Double-check: prevent self-referral using referred_by_user_id
        if ($user->referred_by_user_id && $user->referred_by_user_id === $user->id) {
            return;
        }

        // Check if referrer has active affiliate account (general affiliate, not company-specific)
        $affiliate = Affiliate::where('user_id', $referrer->id)
            ->where('status', 'active')
            ->first();

        if (!$affiliate) {
            return;
        }

        // Skip if referrer is frozen
        if ($referrer->is_frozen || 
            ($referrer->digitalCard && $referrer->digitalCard->is_frozen)) {
            return;
        }

        DB::transaction(function () use ($affiliate, $referrer, $user) {
            // Get admin settings for affiliate points
            $pointsSettings = \App\Models\PointsSetting::current();
            
            // Calculate points based on admin settings
            // Use referral_bonus_points as base, or calculate from transaction amount
            $originalPrice = $this->transaction->original_price ?? $this->transaction->amount;
            
            // Calculate points based on earn_rate (currency amount required to earn 1 point)
            // For affiliate: give points based on referred transaction
            $pointsEarned = 0;
            
            // Option 1: Fixed bonus points per referral transaction
            if ($pointsSettings->referral_bonus_points > 0) {
                $pointsEarned = $pointsSettings->referral_bonus_points;
            }
            
            // Option 2: Percentage-based points from transaction amount
            // Calculate based on earn_rate: if earn_rate is 10, then 10 SAR = 1 point
            if ($pointsSettings->earn_rate > 0) {
                $calculatedPoints = floor($originalPrice / $pointsSettings->earn_rate);
                // Use the higher of fixed bonus or calculated points
                $pointsEarned = max($pointsEarned, $calculatedPoints);
            }

            if ($pointsEarned <= 0) {
                return;
            }

            // Get or create wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $referrer->id],
                [
                    'loyalty_points_balance' => 0,
                    'affiliate_points_balance' => 0,
                    'loyalty_points_pending' => 0,
                    'affiliate_points_pending' => 0,
                ]
            );

            // Add affiliate points as pending
            $wallet->addAffiliatePoints($pointsEarned);

            // Create wallet transaction
            $walletTransaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'affiliate',
                'transaction_type' => 'earned',
                'points' => $pointsEarned,
                'status' => 'pending',
                'source_type' => Transaction::class,
                'source_id' => $this->transaction->id,
                'description' => 'Affiliate points earned from referral transaction ' . $this->transaction->transaction_id,
            ]);

            // Record affiliate sale (without company_id dependency)
            $affiliateSale = AffiliateSale::create([
                'affiliate_id' => $affiliate->id,
                'transaction_id' => $this->transaction->id, // Link to transaction
                'sale_amount' => $originalPrice,
                'commission_amount' => $pointsEarned, // Store points as commission
                'commission_rate' => 0, // Not used for general affiliate
                'status' => 'approved', // Auto-approve for general affiliate
                'approved_at' => now(), // Set approval timestamp
                'user_id' => $user->id,
                'company_id' => $this->transaction->company_id, // Store transaction company for reference
                'offer_id' => null, // General affiliate
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Update affiliate statistics
            $affiliate->increment('total_referrals');
            $affiliate->increment('total_earned', $pointsEarned);

            // Log audit
            AuditLog::log(
                'affiliate_points_earned',
                $this->transaction,
                "Affiliate points earned: {$pointsEarned} points for referrer {$referrer->id} from transaction {$this->transaction->transaction_id}",
                null,
                ['points' => $pointsEarned, 'transaction_amount' => $originalPrice, 'referrer_id' => $referrer->id]
            );
        });
    }

    /**
     * Get the referrer for a user.
     * Priority: referred_by_user_id (locked on registration) > session/cookie > referral record
     */
    private function getReferrer(User $user): ?User
    {
        // Priority 1: Check referred_by_user_id (locked at registration, cannot be changed)
        if ($user->referred_by_user_id) {
            $referrer = User::find($user->referred_by_user_id);
            if ($referrer && $referrer->id !== $user->id) {
                return $referrer;
            }
        }

        // Priority 2: Check session/cookie for referral code (fallback for existing users)
        $referralCode = session('referral_code') ?? request()->cookie('referral_code');
        
        if ($referralCode) {
            $affiliate = Affiliate::where('referral_code', $referralCode)
                ->where('status', 'active')
                ->first();
            
            if ($affiliate && $affiliate->user_id !== $user->id) {
                return $affiliate->user;
            }
        }

        // Priority 3: Check referral record (legacy)
        $referral = \App\Models\Referral::where('referee_id', $user->id)->first();
        
        if ($referral && $referral->referrer_id !== $user->id) {
            return $referral->referrer;
        }

        return null;
    }
}
