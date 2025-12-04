<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class Menu extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['label'];

    protected $fillable = [
        'name',
        'url',
        'route',
        'icon',
        'parent_id',
        'footer_menu_group_id',
        'order',
        'is_active',
        'open_in_new_tab',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'settings' => 'array',
        'order' => 'integer',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function footerMenuGroup()
    {
        return $this->belongsTo(FooterMenuGroup::class, 'footer_menu_group_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMenu($query, $menuName = null)
    {
        if ($menuName) {
            return $query->where('name', $menuName);
        }
        return $query;
    }


    public function scopeRootItems($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'asc');
    }

    // Helper method to get full URL
    public function getFullUrlAttribute()
    {
        if ($this->route) {
            return route($this->route);
        }
        return $this->url ?? '#';
    }
}
