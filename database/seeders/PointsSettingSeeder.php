<?php

namespace Database\Seeders;

use App\Models\PointsSetting;
use Illuminate\Database\Seeder;

class PointsSettingSeeder extends Seeder
{
    public function run(): void
    {
        PointsSetting::firstOrCreate([], [
            'earn_rate' => 10,
            'redeem_rate' => 0.1,
            'referral_bonus_points' => 50,
            'auto_approve_redemptions' => false,
        ]);
    }
}
