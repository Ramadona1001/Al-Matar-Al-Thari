<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class SectionSetting extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['title', 'subtitle'];

    protected $fillable = [
        'section_key',
        'is_active',
        'options',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'options' => 'array',
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

    public static function getByKey($key)
    {
        return static::where('section_key', $key)->active()->first();
    }
}
