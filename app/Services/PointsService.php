<?php

namespace App\Services;

use App\Models\PointsSetting;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\LoyaltyCard;
use App\Models\Reward;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class PointsService
{
    protected ?PointsSetting $settings = null;

    public function settings(): PointsSetting
    {
        if (!$this->settings) {
            $this->settings = PointsSetting::current();
        }

        return $this->settings;
    }

    public function earnRate(): float
    {
        return (float) ($this->settings()->earn_rate ?: 10.0);
    }

    public function redeemRate(): float
    {
        return (float) ($this->settings()->redeem_rate ?: 0.1);
    }

    public function referralBonus(): int
    {
        return (int) ($this->settings()->referral_bonus_points ?: 50);
    }

    public function autoApproveRedemptions(): bool
    {
        return (bool) ($this->settings()->auto_approve_redemptions ?? false);
    }

    public function calculateEarnedPoints(float $amount, float $multiplier = 1.0): int
    {
        $rate = max($this->earnRate(), 0.01);
        $basePoints = (int) floor($amount / $rate);
        return (int) round($basePoints * $multiplier);
    }

    public function calculateRedeemAmount(int $points): float
    {
        return (float) round($points * $this->redeemRate(), 2);
    }

    /**
     * Ensure the user has this card in their wallet.
     */
    public function ensureWalletAttached(User $user, LoyaltyCard $card): void
    {
        if (!$user->walletCards()->where('card_id', $card->id)->exists()) {
            $user->walletCards()->attach($card->id, [
                'points_balance' => 0,
                'last_transaction_at' => null,
            ]);
        }
    }

    /**
     * Add points to a user's wallet for a specific card and create ledger.
     */
    public function addPoints(User $user, LoyaltyCard $card, int $points, Staff $staff, ?\DateTimeInterface $expiry = null): void
    {
        DB::transaction(function () use ($user, $card, $points, $staff, $expiry) {
            $this->ensureWalletAttached($user, $card);

            LoyaltyPoint::create([
                'user_id' => $user->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'club_id' => $card->club_id,
                'points' => $points,
                'type' => 'earned',
                'source_type' => Staff::class,
                'source_id' => $staff->id,
                'description' => 'Staff awarded points',
                'expiry_date' => $expiry ? $expiry : now()->addYear(),
            ]);

            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'type' => 'earn',
                'points' => $points,
                'expires_at' => $expiry ? $expiry : now()->addYear(),
                'metadata' => [
                    'awarded_by_staff_id' => $staff->id,
                ],
            ]);

            // Update wallet pivot balance and last transaction
            $user->walletCards()->updateExistingPivot($card->id, [
                'points_balance' => DB::raw('points_balance + ' . (int)$points),
                'last_transaction_at' => now(),
            ]);

            // Increment analytics
            $card->increment('points_accumulated', $points);
            $card->increment('staff_actions_count');
        });
    }

    /**
     * Check if user can redeem given points on this card.
     */
    public function canRedeem(User $user, LoyaltyCard $card, int $requiredPoints): bool
    {
        $earned = LoyaltyPoint::query()
            ->where('user_id', $user->id)
            ->where('company_id', $card->company_id)
            ->where('card_id', $card->id)
            ->where('type', 'earned')
            ->sum('points');

        $redeemed = LoyaltyPoint::query()
            ->where('user_id', $user->id)
            ->where('company_id', $card->company_id)
            ->where('card_id', $card->id)
            ->where('type', 'redeemed')
            ->sum('points');

        return ($earned - $redeemed) >= $requiredPoints;
    }

    /**
     * Redeem points using FIFO across earned lots. Records a ledger with consumption details and updates pivot balance.
     */
    public function redeemFIFO(User $user, LoyaltyCard $card, int $pointsRequired, Reward $reward, Staff $staff): void
    {
        DB::transaction(function () use ($user, $card, $pointsRequired, $reward, $staff) {
            $this->ensureWalletAttached($user, $card);

            // Build consumption plan from oldest earned lots that are not expired and not marked redeemed
            $lots = LoyaltyPoint::query()
                ->where('user_id', $user->id)
                ->where('company_id', $card->company_id)
                ->where('card_id', $card->id)
                ->where('type', 'earned')
                ->where(function ($q) {
                    $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
                })
                ->orderBy('created_at', 'asc')
                ->get(['id', 'points', 'created_at']);

            $remaining = $pointsRequired;
            $consumed = [];
            $available = 0;

            foreach ($lots as $lot) {
                if ($remaining <= 0) break;
                $take = min($lot->points, $remaining);
                $available += $lot->points;
                $consumed[] = [
                    'lot_id' => $lot->id,
                    'take' => $take,
                ];
                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new \RuntimeException('Insufficient points to redeem.');
            }

            // Create a single redeemed record (legacy balance mechanism)
            LoyaltyPoint::create([
                'user_id' => $user->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'club_id' => $card->club_id,
                'points' => $pointsRequired,
                'type' => 'redeemed',
                'source_type' => Reward::class,
                'source_id' => $reward->id,
                'description' => 'Reward redeemed: ' . $reward->title,
            ]);

            // Ledger entry with FIFO consumption details
            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'type' => 'redeem',
                'points' => $pointsRequired,
                'metadata' => [
                    'reward_id' => $reward->id,
                    'reward_title' => $reward->title,
                    'consumed_lots' => $consumed,
                    'redeemed_by_staff_id' => $staff->id,
                ],
            ]);

            // Update wallet pivot balance and last transaction
            $user->walletCards()->updateExistingPivot($card->id, [
                'points_balance' => DB::raw('points_balance - ' . (int)$pointsRequired),
                'last_transaction_at' => now(),
            ]);
        });
    }

    /**
     * Transfer points from one user to another using FIFO across sender's earned lots.
     * Creates ledger entries for both parties, preserves original expiry on receiver lots,
     * and updates wallet pivot balances.
     */
    public function transferFIFO(User $from, User $to, LoyaltyCard $card, int $points, ?User $initiator = null): void
    {
        DB::transaction(function () use ($from, $to, $card, $points, $initiator) {
            // Ensure both users have the card attached
            $this->ensureWalletAttached($from, $card);
            $this->ensureWalletAttached($to, $card);

            // Build consumption plan from sender's oldest earned lots
            $lots = LoyaltyPoint::query()
                ->where('user_id', $from->id)
                ->where('company_id', $card->company_id)
                ->where('card_id', $card->id)
                ->where('type', 'earned')
                ->where(function ($q) {
                    $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
                })
                ->orderBy('created_at', 'asc')
                ->get(['id', 'points', 'created_at', 'expiry_date']);

            $remaining = $points;
            $consumed = [];

            foreach ($lots as $lot) {
                if ($remaining <= 0) break;
                $take = min($lot->points, $remaining);
                $consumed[] = [
                    'lot_id' => $lot->id,
                    'take' => $take,
                    'expiry_date' => $lot->expiry_date,
                ];
                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new \RuntimeException('Insufficient points to transfer.');
            }

            // Record sender side as redeemed (points moved out)
            LoyaltyPoint::create([
                'user_id' => $from->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'club_id' => $card->club_id,
                'points' => $points,
                'type' => 'redeemed',
                'source_type' => User::class,
                'source_id' => $to->id,
                'description' => 'Transferred points to user #' . $to->id,
            ]);

            LoyaltyTransaction::create([
                'user_id' => $from->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'type' => 'transfer',
                'points' => $points,
                'metadata' => [
                    'direction' => 'outgoing',
                    'initiator_user_id' => $initiator ? $initiator->id : null,
                    'to_user_id' => $to->id,
                    'consumed_lots' => $consumed,
                ],
            ]);

            // Create receiver lots preserving expiry
            foreach ($consumed as $c) {
                LoyaltyPoint::create([
                    'user_id' => $to->id,
                    'company_id' => $card->company_id,
                    'card_id' => $card->id,
                    'club_id' => $card->club_id,
                    'points' => $c['take'],
                    'type' => 'earned',
                    'source_type' => User::class,
                    'source_id' => $from->id,
                    'description' => 'Received transferred points from user #' . $from->id,
                    'expiry_date' => $c['expiry_date'] ?: now()->addYear(),
                ]);
            }

            LoyaltyTransaction::create([
                'user_id' => $to->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'type' => 'transfer',
                'points' => $points,
                'metadata' => [
                    'direction' => 'incoming',
                    'initiator_user_id' => $initiator ? $initiator->id : null,
                    'from_user_id' => $from->id,
                    'preserved_expiry' => true,
                ],
            ]);

            // Update wallet pivot balances
            $from->walletCards()->updateExistingPivot($card->id, [
                'points_balance' => DB::raw('points_balance - ' . (int)$points),
                'last_transaction_at' => now(),
            ]);

            $to->walletCards()->updateExistingPivot($card->id, [
                'points_balance' => DB::raw('points_balance + ' . (int)$points),
                'last_transaction_at' => now(),
            ]);
        });
    }

    /**
     * Redeem a single-use code to add points to a user's wallet.
     * Marks the code as used and records ledger.
     */
    public function redeemCode(User $user, RedeemCode $code, LoyaltyCard $card): void
    {
        DB::transaction(function () use ($user, $code, $card) {
            $this->ensureWalletAttached($user, $card);

            // Add points from code with default 1-year expiry
            LoyaltyPoint::create([
                'user_id' => $user->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'club_id' => $card->club_id,
                'points' => $code->points,
                'type' => 'earned',
                'source_type' => RedeemCode::class,
                'source_id' => $code->id,
                'description' => 'Redeemed code ' . $code->code,
                'expiry_date' => now()->addYear(),
            ]);

            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'company_id' => $card->company_id,
                'card_id' => $card->id,
                'type' => 'earn',
                'points' => $code->points,
                'expires_at' => now()->addYear(),
                'metadata' => [
                    'source' => 'redeem_code',
                    'code_id' => $code->id,
                    'code' => $code->code,
                ],
            ]);

            // Update wallet balance
            $user->walletCards()->updateExistingPivot($card->id, [
                'points_balance' => DB::raw('points_balance + ' . (int)$code->points),
                'last_transaction_at' => now(),
            ]);

            // Mark code as used
            $code->markUsed($user->id);
        });
    }
}
