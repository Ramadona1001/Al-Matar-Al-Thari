<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Astrotomic\Translatable\Translatable;

class Service extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['title', 'short_description', 'description', 'meta_title', 'meta_description', 'meta_keywords'];

    protected $fillable = [
        'slug',
        'icon',
        'image_path',
        'og_image',
        'order',
        'is_active',
        'is_featured',
        'features',
        'pricing',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'features' => 'array',
        'pricing' => 'array',
        'order' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    /**
     * Get localized name (title) for current locale.
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();
        
        // Try to get translation for current locale
        try {
            $translation = $this->translate($locale);
            if ($translation && $translation->title) {
                return $translation->title;
            }
        } catch (\Exception $e) {
            // Translation not found for current locale
        }
        
        // Fallback to English
        try {
            $englishTranslation = $this->translate('en');
            if ($englishTranslation && $englishTranslation->title) {
                return $englishTranslation->title;
            }
        } catch (\Exception $e) {
            // English translation not found
        }
        
        // Fallback: try to get title directly (Translatable may provide accessor)
        if (property_exists($this, 'title') || method_exists($this, 'getTitleAttribute')) {
            $title = $this->title ?? null;
            if ($title) {
                return is_string($title) ? $title : '';
            }
        }
        
        // Last resort: get any available translation
        if (method_exists($this, 'translations')) {
            $translations = $this->translations;
            if ($translations && $translations->isNotEmpty()) {
                $firstTranslation = $translations->first();
                return $firstTranslation->title ?? '';
            }
        }
        
        return '';
    }

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $title = is_array($service->title) ? ($service->title[app()->getLocale()] ?? reset($service->title)) : $service->title;
                $service->slug = Str::slug($title);
            }
        });

        static::updating(function ($service) {
            if ($service->isDirty('title') && empty($service->slug)) {
                $title = is_array($service->title) ? ($service->title[app()->getLocale()] ?? reset($service->title)) : $service->title;
                $service->slug = Str::slug($title);
            }
        });
    }
}
