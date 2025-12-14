<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'sku',
        'price',
        'compare_price',
        'stock_quantity',
        'track_stock',
        'in_stock',
        'image',
        'images',
        'status',
        'is_featured',
        'sort_order',
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
        'name' => 'array',
        'description' => 'array',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_stock' => 'boolean',
        'in_stock' => 'boolean',
        'images' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the company that owns the product.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the branch that owns the product.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the offers for the product.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Get the coupons for the product.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('in_stock', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Get name for current locale.
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();
        $rawName = $this->getRawOriginal('name');
        
        if (empty($rawName)) {
            return '';
        }
        
        $name = json_decode($rawName, true);
        
        if (!is_array($name) || empty($name)) {
            return '';
        }
        
        return $name[$locale] ?? $name['en'] ?? '';
    }

    /**
     * Get description for current locale.
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        $rawDescription = $this->getRawOriginal('description');
        
        if (empty($rawDescription)) {
            return '';
        }
        
        $description = json_decode($rawDescription, true);
        
        if (!is_array($description) || empty($description)) {
            return '';
        }
        
        return $description[$locale] ?? $description['en'] ?? '';
    }

    /**
     * Set name as JSON.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = is_array($value) ? json_encode($value) : $value;
        
        // Auto-generate slug if not provided
        if (empty($this->attributes['slug']) && is_array($value)) {
            $nameString = $value['en'] ?? array_values($value)[0] ?? '';
            if (is_string($nameString) && !empty($nameString)) {
                $this->attributes['slug'] = Str::slug($nameString);
            }
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
     * Get name as array.
     */
    public function getNameAttribute($value)
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

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        if (!$this->track_stock) {
            return $this->in_stock;
        }
        // If stock_quantity is null, it means unlimited stock
        if ($this->stock_quantity === null) {
            return $this->in_stock;
        }
        return $this->in_stock && $this->stock_quantity > 0;
    }

    /**
     * Check if product is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'active' && $this->isInStock();
    }
}
