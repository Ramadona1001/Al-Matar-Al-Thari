<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Get or create wallet for user.
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'loyalty_points_balance' => 0,
                'affiliate_points_balance' => 0,
                'loyalty_points_pending' => 0,
                'affiliate_points_pending' => 0,
            ]
        );
    }

    /**
     * Add loyalty points to wallet (pending).
     */
    public function addLoyaltyPoints(User $user, int $points, string $sourceType, int $sourceId, string $description = null): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($user);
        
        $wallet->addLoyaltyPoints($points);

        return WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'loyalty',
            'transaction_type' => 'earned',
            'points' => $points,
            'status' => 'pending',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'description' => $description ?? 'Loyalty points earned',
        ]);
    }

    /**
     * Add affiliate points to wallet (pending).
     */
    public function addAffiliatePoints(User $user, int $points, string $sourceType, int $sourceId, string $description = null): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($user);
        
        $wallet->addAffiliatePoints($points);

        return WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'affiliate',
            'transaction_type' => 'earned',
            'points' => $points,
            'status' => 'pending',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'description' => $description ?? 'Affiliate points earned',
        ]);
    }

    /**
     * Approve pending points.
     */
    public function approvePoints(WalletTransaction $transaction, User $admin): bool
    {
        return $transaction->approve($admin);
    }

    /**
     * Redeem loyalty points.
     */
    public function redeemLoyaltyPoints(User $user, int $points, string $description = null): bool
    {
        $wallet = $this->getOrCreateWallet($user);
        
        if (!$wallet->redeemLoyaltyPoints($points)) {
            return false;
        }

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'loyalty',
            'transaction_type' => 'redeemed',
            'points' => -$points,
            'status' => 'approved',
            'description' => $description ?? 'Loyalty points redeemed',
        ]);

        return true;
    }

    /**
     * Redeem affiliate points.
     */
    public function redeemAffiliatePoints(User $user, int $points, string $description = null): bool
    {
        $wallet = $this->getOrCreateWallet($user);
        
        if (!$wallet->redeemAffiliatePoints($points)) {
            return false;
        }

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'affiliate',
            'transaction_type' => 'redeemed',
            'points' => -$points,
            'status' => 'approved',
            'description' => $description ?? 'Affiliate points redeemed',
        ]);

        return true;
    }

    /**
     * Reverse points.
     */
    public function reversePoints(User $user, int $points, string $type, string $reason = null): void
    {
        $wallet = $this->getOrCreateWallet($user);
        
        if ($type === 'loyalty') {
            $wallet->reverseLoyaltyPoints($points);
        } else {
            $wallet->reverseAffiliatePoints($points);
        }

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => $type,
            'transaction_type' => 'reversed',
            'points' => -$points,
            'status' => 'approved',
            'description' => 'Points reversed: ' . ($reason ?? 'Transaction reversed'),
        ]);
    }
}

