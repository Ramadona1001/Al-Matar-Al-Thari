<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class Banner extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['title', 'subtitle', 'description', 'button_text'];

    protected $fillable = [
        'image_path',
        'mobile_image_path',
        'button_link',
        'button_style',
        'order',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    /**
     * Get the banner with translations for current locale
     */
    public function scopeWithCurrentLocale($query)
    {
        return $query->with(['translations' => function($query) {
            $query->where('locale', app()->getLocale());
        }]);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure Translatable uses the current locale
        static::retrieved(function ($banner) {
            // This ensures Translatable uses the current app locale
            if (method_exists($banner, 'setLocale')) {
                $banner->setLocale(app()->getLocale());
            }
        });
    }
}
