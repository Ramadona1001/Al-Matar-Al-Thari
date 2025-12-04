<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class Section extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['title', 'subtitle', 'content'];

    protected $fillable = [
        'name',
        'type',
        'image_path',
        'images',
        'data',
        'builder_data',
        'order',
        'columns_per_row',
        'is_visible',
        'page',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'images' => 'array',
        'data' => 'array',
        'builder_data' => 'array',
        'order' => 'integer',
        'columns_per_row' => 'integer',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeForPage($query, $page)
    {
        if (empty($page)) {
            return $query;
        }
        return $query->where('page', $page);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    public function items()
    {
        return $this->hasMany(SectionItem::class)->ordered();
    }

    public function activeItems()
    {
        return $this->hasMany(SectionItem::class)->active()->ordered();
    }
}
