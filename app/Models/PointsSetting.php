<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsSetting extends Model
{
    protected $fillable = [
        'earn_rate',
        'redeem_rate',
        'referral_bonus_points',
        'auto_approve_redemptions',
        'updated_by',
    ];

    protected $casts = [
        'earn_rate' => 'decimal:2',
        'redeem_rate' => 'decimal:2',
        'referral_bonus_points' => 'integer',
        'auto_approve_redemptions' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->latest('updated_at')->first() ?? new static();
    }
}
