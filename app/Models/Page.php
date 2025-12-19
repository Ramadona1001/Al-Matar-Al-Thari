<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class Page extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = [
        'title',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'excerpt',
        'menu_label',
    ];

    protected $fillable = [
        'slug',
        'is_published',
        'featured_image',
        'og_image',
        'template',
        'sections',
        'order',
        'show_in_menu',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_menu' => 'boolean',
        'sections' => 'array',
        'order' => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'asc');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure Translatable uses the current locale
        static::retrieved(function ($page) {
            if (method_exists($page, 'setLocale')) {
                $page->setLocale(app()->getLocale());
            }
        });
    }
}