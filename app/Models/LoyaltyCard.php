<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'club_id',
        'title',
        'slug',
        'description',
        'image_path',
        'points',
        'balance',
        'visible_on_homepage',
        'status',
        'views_count',
        'points_accumulated',
        'rewards_redeemed_count',
        'staff_actions_count',
    ];

    protected $casts = [
        'visible_on_homepage' => 'boolean',
        'points' => 'integer',
        'balance' => 'decimal:2',
        'views_count' => 'integer',
        'points_accumulated' => 'integer',
        'rewards_redeemed_count' => 'integer',
        'staff_actions_count' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function rewards(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reward::class);
    }

    public function followers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_loyalty_cards', 'card_id', 'customer_id')
            ->withPivot(['points_balance', 'last_transaction_at'])
            ->withTimestamps();
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function pointRequestLinks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PointRequestLink::class);
    }

    public function redeemCodes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RedeemCode::class);
    }
}
