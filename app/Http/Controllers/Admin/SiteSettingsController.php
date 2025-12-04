<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function edit()
    {
        $settings = SiteSetting::getSettings();
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.site.settings', compact('settings', 'locales'));
    }

    public function update(Request $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'theme_primary_color' => 'nullable|string|max:20',
            'theme_secondary_color' => 'nullable|string|max:20',
            'theme_accent_color' => 'nullable|string|max:20',
            'gradient_start_color' => 'nullable|string|max:20',
            'gradient_end_color' => 'nullable|string|max:20',
            'text_primary_color' => 'nullable|string|max:20',
            'text_secondary_color' => 'nullable|string|max:20',
            'text_on_primary_color' => 'nullable|string|max:20',
            'bg_primary_color' => 'nullable|string|max:20',
            'bg_secondary_color' => 'nullable|string|max:20',
            'bg_dark_color' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:150',
            'contact_phone' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico|max:512',
            'preloader_icon' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg,gif|max:1024',
            'footer_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'footer_bg_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'custom_styles' => 'nullable|string',
            'custom_scripts' => 'nullable|string',
        ]);
        
        // Validate multi-language fields
        foreach ($locales as $locale) {
            $request->validate([
                "brand_name.{$locale}" => 'nullable|string|max:100',
                "hero_title.{$locale}" => 'nullable|string|max:150',
                "hero_subtitle.{$locale}" => 'nullable|string|max:200',
                "meta_title.{$locale}" => 'nullable|string|max:255',
                "meta_description.{$locale}" => 'nullable|string|max:500',
                "meta_keywords.{$locale}" => 'nullable|string|max:255',
                "footer_text.{$locale}" => 'nullable|string',
                "footer_copyright.{$locale}" => 'nullable|string|max:255',
                "contact_address.{$locale}" => 'nullable|string|max:255',
            ]);
        }

        $settings = SiteSetting::firstOrCreate([], []);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('site', 'public');
        }
        
        if ($request->hasFile('favicon')) {
            if ($settings->favicon_path) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $validated['favicon_path'] = $request->file('favicon')->store('site', 'public');
        }
        
        if ($request->hasFile('preloader_icon')) {
            if ($settings->preloader_icon_path) {
                Storage::disk('public')->delete($settings->preloader_icon_path);
            }
            $validated['preloader_icon_path'] = $request->file('preloader_icon')->store('site', 'public');
        }
        
        if ($request->hasFile('footer_logo')) {
            if ($settings->footer_logo_path) {
                Storage::disk('public')->delete($settings->footer_logo_path);
            }
            $validated['footer_logo_path'] = $request->file('footer_logo')->store('site', 'public');
        }
        
        if ($request->hasFile('footer_bg_image')) {
            if ($settings->footer_bg_image_path) {
                Storage::disk('public')->delete($settings->footer_bg_image_path);
            }
            $validated['footer_bg_image_path'] = $request->file('footer_bg_image')->store('site', 'public');
        }

        // Build multi-language arrays
        $validated['brand_name'] = $request->input('brand_name', []);
        $validated['hero_title'] = $request->input('hero_title', []);
        $validated['hero_subtitle'] = $request->input('hero_subtitle', []);
        $validated['meta_title'] = $request->input('meta_title', []);
        $validated['meta_description'] = $request->input('meta_description', []);
        $validated['meta_keywords'] = $request->input('meta_keywords', []);
        $validated['footer_text'] = $request->input('footer_text', []);
        $validated['footer_copyright'] = $request->input('footer_copyright', []);
        $validated['contact_address'] = $request->input('contact_address', []);
        
        $validated['custom_styles'] = $request->input('custom_styles');
        $validated['custom_scripts'] = $request->input('custom_scripts');
        $validated['updated_by'] = Auth::id();

        $settings->fill($validated)->save();

        return back()->with('success', __('Settings updated successfully.'));
    }
}
