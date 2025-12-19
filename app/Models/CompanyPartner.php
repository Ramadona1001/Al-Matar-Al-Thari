<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class CompanyPartner extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['name'];

    protected $fillable = [
        'logo_path',
        'website_url',
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
        return $query->orderBy('order');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure Translatable uses the current locale
        static::retrieved(function ($partner) {
            if (method_exists($partner, 'setLocale')) {
                $partner->setLocale(app()->getLocale());
            }
        });
    }
}
