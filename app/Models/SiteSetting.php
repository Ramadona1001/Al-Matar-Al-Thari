<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslations;

class SiteSetting extends Model
{
    use HasTranslations;

    protected $fillable = [
        'brand_name',
        'primary_color',
        'secondary_color',
        'theme_primary_color',
        'theme_secondary_color',
        'theme_accent_color',
        'gradient_start_color',
        'gradient_end_color',
        'text_primary_color',
        'text_secondary_color',
        'text_on_primary_color',
        'bg_primary_color',
        'bg_secondary_color',
        'bg_dark_color',
        'logo_path',
        'favicon_path',
        'preloader_icon_path',
        'footer_logo_path',
        'footer_bg_image_path',
        'contact_email',
        'contact_phone',
        'contact_address',
        'hero_title',
        'hero_subtitle',
        'updated_by',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'tiktok_url',
        'footer_text',
        'footer_copyright',
        'custom_styles',
        'custom_scripts',
        'social_links',
        'footer_links',
        'additional_settings',
    ];

    public $translatable = [
        'brand_name',
        'hero_title',
        'hero_subtitle',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'footer_text',
        'footer_copyright',
        'contact_address',
    ];

    protected $casts = [
        'brand_name' => 'array',
        'hero_title' => 'array',
        'hero_subtitle' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'meta_keywords' => 'array',
        'footer_text' => 'array',
        'footer_copyright' => 'array',
        'contact_address' => 'array',
        'social_links' => 'array',
        'footer_links' => 'array',
        'additional_settings' => 'array',
    ];

    public static function getSettings()
    {
        return static::first() ?? new static();
    }
}