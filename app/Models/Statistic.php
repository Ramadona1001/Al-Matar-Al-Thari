<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class Statistic extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['label', 'description'];

    protected $fillable = [
        'value',
        'icon',
        'suffix',
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

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure Translatable uses the current locale
        static::retrieved(function ($statistic) {
            if (method_exists($statistic, 'setLocale')) {
                $statistic->setLocale(app()->getLocale());
            }
        });
    }
}
