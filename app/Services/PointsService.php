<?php

namespace App\Services;

use App\Models\PointsSetting;

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
}
