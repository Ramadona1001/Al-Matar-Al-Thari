<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Astrotomic\Translatable\Translatable;

class Blog extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['title', 'excerpt', 'content', 'meta_title', 'meta_description', 'meta_keywords'];

    protected $fillable = [
        'slug',
        'featured_image',
        'og_image',
        'author_name',
        'author_id',
        'published_at',
        'is_published',
        'is_featured',
        'views',
        'tags',
        'categories',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'date',
        'tags' => 'array',
        'categories' => 'array',
        'views' => 'integer',
    ];

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class)->whereNull('parent_id')->orderBy('created_at', 'desc');
    }

    public function approvedComments()
    {
        return $this->hasMany(BlogComment::class)
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');
    }

    public function allComments()
    {
        return $this->hasMany(BlogComment::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();

        // Ensure Translatable uses the current locale
        static::retrieved(function ($blog) {
            if (method_exists($blog, 'setLocale')) {
                $blog->setLocale(app()->getLocale());
            }
        });

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $title = is_array($blog->title) ? ($blog->title[app()->getLocale()] ?? reset($blog->title)) : $blog->title;
                $blog->slug = Str::slug($title);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && empty($blog->slug)) {
                $title = is_array($blog->title) ? ($blog->title[app()->getLocale()] ?? reset($blog->title)) : $blog->title;
                $blog->slug = Str::slug($title);
            }
        });
    }
}
