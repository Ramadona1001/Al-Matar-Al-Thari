<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class SectionItem extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['title', 'subtitle', 'content', 'link_text'];

    protected $fillable = [
        'section_id',
        'icon',
        'image_path',
        'link',
        'metadata',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'order' => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'asc');
    }
}
