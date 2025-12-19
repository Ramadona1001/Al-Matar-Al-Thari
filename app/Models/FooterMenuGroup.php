<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class FooterMenuGroup extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['name'];
    
    public $translationModel = FooterMenuGroupTranslation::class;
    
    public $translationForeignKey = 'footer_menu_group_id';

    protected $fillable = [
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Relationships
    public function menuItems()
    {
        return $this->hasMany(Menu::class, 'footer_menu_group_id')
            ->where('name', 'footer')
            ->whereNull('parent_id')
            ->active()
            ->ordered();
    }

    // Scopes
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
        static::retrieved(function ($group) {
            if (method_exists($group, 'setLocale')) {
                $group->setLocale(app()->getLocale());
            }
        });
    }
}
