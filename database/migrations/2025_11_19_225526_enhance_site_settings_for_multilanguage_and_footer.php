<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // New file fields
            $table->string('preloader_icon_path')->nullable()->after('favicon_path');
            $table->string('footer_logo_path')->nullable()->after('logo_path');
            
            // Custom code fields
            $table->text('custom_styles')->nullable()->after('additional_settings');
            $table->text('custom_scripts')->nullable()->after('custom_styles');
        });
        
        // Convert existing string fields to JSON for multi-language support
        $settings = DB::table('site_settings')->first();
        if ($settings) {
            $locale = config('app.locale', 'en');
            $updates = [];
            
            // Convert brand_name
            if ($settings->brand_name && !is_null($settings->brand_name)) {
                try {
                    json_decode($settings->brand_name);
                    // Already JSON, skip
                } catch (\Exception $e) {
                    $updates['brand_name'] = json_encode([$locale => $settings->brand_name]);
                }
            }
            
            // Convert hero_title
            if ($settings->hero_title && !is_null($settings->hero_title)) {
                try {
                    json_decode($settings->hero_title);
                } catch (\Exception $e) {
                    $updates['hero_title'] = json_encode([$locale => $settings->hero_title]);
                }
            }
            
            // Convert hero_subtitle
            if ($settings->hero_subtitle && !is_null($settings->hero_subtitle)) {
                try {
                    json_decode($settings->hero_subtitle);
                } catch (\Exception $e) {
                    $updates['hero_subtitle'] = json_encode([$locale => $settings->hero_subtitle]);
                }
            }
            
            // Convert meta fields
            if ($settings->meta_title && !is_null($settings->meta_title)) {
                try {
                    json_decode($settings->meta_title);
                } catch (\Exception $e) {
                    $updates['meta_title'] = json_encode([$locale => $settings->meta_title]);
                }
            }
            
            if ($settings->meta_description && !is_null($settings->meta_description)) {
                try {
                    json_decode($settings->meta_description);
                } catch (\Exception $e) {
                    $updates['meta_description'] = json_encode([$locale => $settings->meta_description]);
                }
            }
            
            if ($settings->meta_keywords && !is_null($settings->meta_keywords)) {
                try {
                    json_decode($settings->meta_keywords);
                } catch (\Exception $e) {
                    $updates['meta_keywords'] = json_encode([$locale => $settings->meta_keywords]);
                }
            }
            
            // Convert footer fields
            if ($settings->footer_text && !is_null($settings->footer_text)) {
                try {
                    json_decode($settings->footer_text);
                } catch (\Exception $e) {
                    $updates['footer_text'] = json_encode([$locale => $settings->footer_text]);
                }
            }
            
            if ($settings->footer_copyright && !is_null($settings->footer_copyright)) {
                try {
                    json_decode($settings->footer_copyright);
                } catch (\Exception $e) {
                    $updates['footer_copyright'] = json_encode([$locale => $settings->footer_copyright]);
                }
            }
            
            // Convert contact_address
            if ($settings->contact_address && !is_null($settings->contact_address)) {
                try {
                    json_decode($settings->contact_address);
                } catch (\Exception $e) {
                    $updates['contact_address'] = json_encode([$locale => $settings->contact_address]);
                }
            }
            
            if (!empty($updates)) {
                DB::table('site_settings')->where('id', $settings->id)->update($updates);
            }
        }
        
        // Change column types to JSON
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('brand_name')->nullable()->change();
            $table->json('hero_title')->nullable()->change();
            $table->json('hero_subtitle')->nullable()->change();
            $table->json('meta_title')->nullable()->change();
            $table->json('meta_description')->nullable()->change();
            $table->json('meta_keywords')->nullable()->change();
            $table->json('footer_text')->nullable()->change();
            $table->json('footer_copyright')->nullable()->change();
            $table->json('contact_address')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Convert JSON back to string (using first available locale)
        $settings = DB::table('site_settings')->first();
        if ($settings) {
            $updates = [];
            $locale = config('app.locale', 'en');
            
            $fields = ['brand_name', 'hero_title', 'hero_subtitle', 'meta_title', 'meta_description', 
                      'meta_keywords', 'footer_text', 'footer_copyright', 'contact_address'];
            
            foreach ($fields as $field) {
                if ($settings->$field) {
                    $data = json_decode($settings->$field, true);
                    if (is_array($data)) {
                        $updates[$field] = $data[$locale] ?? reset($data) ?? '';
                    }
                }
            }
            
            if (!empty($updates)) {
                DB::table('site_settings')->where('id', $settings->id)->update($updates);
            }
        }
        
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'preloader_icon_path',
                'footer_logo_path',
                'custom_styles',
                'custom_scripts',
            ]);
            
            $table->string('brand_name')->nullable()->change();
            $table->string('hero_title')->nullable()->change();
            $table->string('hero_subtitle')->nullable()->change();
            $table->string('meta_title')->nullable()->change();
            $table->text('meta_description')->nullable()->change();
            $table->string('meta_keywords')->nullable()->change();
            $table->text('footer_text')->nullable()->change();
            $table->text('footer_copyright')->nullable()->change();
            $table->string('contact_address')->nullable()->change();
        });
    }
};
