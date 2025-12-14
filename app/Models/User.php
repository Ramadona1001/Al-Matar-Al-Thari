<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasTranslations;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'avatar',
        'locale',
        'user_type',
        'is_active',
        'is_frozen',
        'frozen_reason',
        'frozen_by',
        'frozen_at',
        'referred_by_user_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'is_frozen' => 'boolean',
        'frozen_at' => 'datetime',
    ];

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user's name.
     */
    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    /**
     * Set the user's name.
     */
    public function setNameAttribute(string $value): void
    {
        $names = explode(' ', $value, 2);
        $this->attributes['first_name'] = $names[0] ?? $value;
        $this->attributes['last_name'] = $names[1] ?? '';
    }

    /**
     * Get the company associated with the user.
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Get the digital card for the user.
     */
    public function digitalCard()
    {
        return $this->hasOne(DigitalCard::class);
    }

    /**
     * Get the loyalty points for the user.
     */
    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Get the total loyalty points balance.
     * Uses Wallet model for current system, falls back to LoyaltyPoint for backward compatibility.
     */
    public function getLoyaltyPointsBalanceAttribute(): int
    {
        // Use Wallet if available (new system)
        $wallet = $this->wallet;
        if ($wallet) {
            return $wallet->loyalty_points_balance ?? 0;
        }
        
        // Fallback to old LoyaltyPoint system for backward compatibility
        return $this->loyaltyPoints()
            ->where('type', 'earned')
            ->whereNull('redeemed_at')
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>', now());
            })
            ->sum('points') - 
            $this->loyaltyPoints()
            ->where('type', 'redeemed')
            ->sum('points');
    }

    /**
     * Wallet: loyalty cards followed by the customer with pivot fields.
     */
    public function walletCards()
    {
        return $this->belongsToMany(LoyaltyCard::class, 'customer_loyalty_cards', 'customer_id', 'card_id')
            ->withPivot(['points_balance', 'last_transaction_at'])
            ->withTimestamps();
    }

    /**
     * Loyalty transactions ledger for the user.
     */
    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    /**
     * Point request links created by the customer.
     */
    public function pointRequestLinks()
    {
        return $this->hasMany(PointRequestLink::class, 'customer_id');
    }

    /**
     * Redeem codes used by the user.
     */
    public function redeemCodesUsed()
    {
        return $this->hasMany(RedeemCode::class, 'used_by_user_id');
    }

    /**
     * Get the affiliate account for the user.
     */
    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    /**
     * Get the affiliate sales for the user.
     */
    public function affiliateSales()
    {
        return $this->hasMany(AffiliateSale::class);
    }

    /**
     * Get the wallet for the user.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the admin who froze the user account.
     */
    public function frozenBy()
    {
        return $this->belongsTo(User::class, 'frozen_by');
    }

    /**
     * Get tickets created by the user.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the referrals made by the user.
     */
    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get the referrals received by the user.
     */
    public function referralsReceived()
    {
        return $this->hasMany(Referral::class, 'referee_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin' || $this->hasRole('super-admin');
    }

    /**
     * Check if user is merchant.
     */
    public function isMerchant(): bool
    {
        return $this->user_type === 'merchant' || $this->hasRole('merchant');
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer(): bool
    {
        return $this->user_type === 'customer' || $this->hasRole('customer');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the user's preferred locale.
     */
    public function getPreferredLocale(): string
    {
        return $this->locale ?? config('localization.default_locale', 'en');
    }

    /**
     * Set the user's preferred locale.
     */
    public function setPreferredLocale(string $locale): void
    {
        if (in_array($locale, config('localization.supported_locales', ['en']))) {
            $this->update(['locale' => $locale]);
        }
    }

    /**
     * Scope a query to only include customers.
     */
    public function scopeCustomers($query)
    {
        return $query->where('user_type', 'customer');
    }

    /**
     * Scope a query to only include merchants.
     */
    public function scopeMerchants($query)
    {
        return $query->where('user_type', 'merchant');
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }
        
        // Return default avatar based on gender or initials
        return null;
    }

    /**
     * Get the user's avatar or default placeholder.
     */
    public function getAvatarOrPlaceholderAttribute(): string
    {
        if ($this->avatar) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }
        
        // Return placeholder with initials
        $initials = strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        return "https://ui-avatars.com/api/?name={$initials}&background=random&color=fff&size=128";
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
