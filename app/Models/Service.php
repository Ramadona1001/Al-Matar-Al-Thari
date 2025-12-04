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
