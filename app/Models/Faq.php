<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class Faq extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['question', 'answer'];

    protected $fillable = [
        'category',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'asc');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure Translatable uses the current locale
        static::retrieved(function ($faq) {
            if (method_exists($faq, 'setLocale')) {
                $faq->setLocale(app()->getLocale());
            }
        });
    }
}
