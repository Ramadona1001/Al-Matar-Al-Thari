<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class HowItWorksStep extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['title', 'description'];

    protected $fillable = [
        'icon',
        'image_path',
        'step_number',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'step_number' => 'integer',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    public function scopeOrdered($query)
    {
        return $query->orderBy('step_number')->orderBy('order');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure Translatable uses the current locale
        static::retrieved(function ($step) {
            if (method_exists($step, 'setLocale')) {
                $step->setLocale(app()->getLocale());
            }
        });
    }
}
