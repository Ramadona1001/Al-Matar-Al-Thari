<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
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
        'icon',
        'image',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the offers for the category.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get name for current locale.
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['en'] ?? '';
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
     * Generate slug from name if not provided.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = is_array($value) ? json_encode($value) : $value;
        
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
}