<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'slug',
        'image',
        'type',
        'discount_percentage',
        'discount_amount',
        'minimum_purchase',
        'usage_limit_per_user',
        'total_usage_limit',
        'start_date',
        'end_date',
        'status',
        'is_featured',
        'company_id',
        'category_id',
        'branch_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the company that owns the offer.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the category that owns the offer.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the branch that owns the offer.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the coupons for the offer.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Get the affiliate accounts for the offer.
     */
    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class);
    }

    /**
     * Get the affiliate sales for the offer.
     */
    public function affiliateSales(): HasMany
    {
        return $this->hasMany(AffiliateSale::class);
    }

    /**
     * Get the transactions for the offer.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Check if offer is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    /**
     * Check if offer is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Check if offer is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->start_date > now();
    }

    /**
     * Get title for current locale.
     */
    public function getLocalizedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->title[$locale] ?? $this->title['en'] ?? '';
    }

    /**
     * Get description for current locale.
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->description[$locale] ?? $this->description['en'] ?? '';
    }

    /**
     * Scope a query to only include active offers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    /**
     * Scope a query to only include featured offers.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include offers by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Set title as JSON.
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = is_array($value) ? json_encode($value) : $value;
        
        if (empty($this->slug) && is_array($value)) {
            $this->attributes['slug'] = \Str::slug($value['en'] ?? array_values($value)[0] ?? '');
        }
    }

    /**
     * Set description as JSON.
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get title as array.
     */
    public function getTitleAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Get description as array.
     */
    public function getDescriptionAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}