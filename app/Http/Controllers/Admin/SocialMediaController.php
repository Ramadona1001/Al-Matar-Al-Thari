<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialMediaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index()
    {
        $settings = SiteSetting::getSettings();
        
        // Get social links from JSON or individual fields
        $socialLinks = [];
        if (isset($settings->social_links) && is_array($settings->social_links) && !empty($settings->social_links)) {
            $socialLinks = $settings->social_links;
        } else {
            // Fallback to individual URL fields
            if ($settings->facebook_url) $socialLinks['facebook'] = $settings->facebook_url;
            if ($settings->twitter_url) $socialLinks['twitter'] = $settings->twitter_url;
            if ($settings->instagram_url) $socialLinks['instagram'] = $settings->instagram_url;
            if ($settings->linkedin_url) $socialLinks['linkedin'] = $settings->linkedin_url;
            if ($settings->youtube_url) $socialLinks['youtube'] = $settings->youtube_url;
            if ($settings->tiktok_url) $socialLinks['tiktok'] = $settings->tiktok_url;
        }
        
        // Define available platforms
        $availablePlatforms = [
            'facebook' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'color' => '#1877F2'],
            'twitter' => ['name' => 'Twitter', 'icon' => 'fab fa-twitter', 'color' => '#1DA1F2'],
            'instagram' => ['name' => 'Instagram', 'icon' => 'fab fa-instagram', 'color' => '#E4405F'],
            'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'color' => '#0077B5'],
            'youtube' => ['name' => 'YouTube', 'icon' => 'fab fa-youtube', 'color' => '#FF0000'],
            'tiktok' => ['name' => 'TikTok', 'icon' => 'fab fa-tiktok', 'color' => '#000000'],
            'pinterest' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest', 'color' => '#BD081C'],
            'snapchat' => ['name' => 'Snapchat', 'icon' => 'fab fa-snapchat', 'color' => '#FFFC00'],
            'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#25D366'],
            'telegram' => ['name' => 'Telegram', 'icon' => 'fab fa-telegram', 'color' => '#0088CC'],
        ];
        
        return view('admin.social-media.index', compact('socialLinks', 'availablePlatforms', 'settings'));
    }

    public function create()
    {
        $settings = SiteSetting::getSettings();
        
        // Get existing social links to show which platforms are already added
        $socialLinks = [];
        if (isset($settings->social_links) && is_array($settings->social_links) && !empty($settings->social_links)) {
            $socialLinks = $settings->social_links;
        } else {
            if ($settings->facebook_url) $socialLinks['facebook'] = $settings->facebook_url;
            if ($settings->twitter_url) $socialLinks['twitter'] = $settings->twitter_url;
            if ($settings->instagram_url) $socialLinks['instagram'] = $settings->instagram_url;
            if ($settings->linkedin_url) $socialLinks['linkedin'] = $settings->linkedin_url;
            if ($settings->youtube_url) $socialLinks['youtube'] = $settings->youtube_url;
            if ($settings->tiktok_url) $socialLinks['tiktok'] = $settings->tiktok_url;
        }
        
        // Define available platforms
        $availablePlatforms = [
            'facebook' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'color' => '#1877F2'],
            'twitter' => ['name' => 'Twitter', 'icon' => 'fab fa-twitter', 'color' => '#1DA1F2'],
            'instagram' => ['name' => 'Instagram', 'icon' => 'fab fa-instagram', 'color' => '#E4405F'],
            'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'color' => '#0077B5'],
            'youtube' => ['name' => 'YouTube', 'icon' => 'fab fa-youtube', 'color' => '#FF0000'],
            'tiktok' => ['name' => 'TikTok', 'icon' => 'fab fa-tiktok', 'color' => '#000000'],
            'pinterest' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest', 'color' => '#BD081C'],
            'snapchat' => ['name' => 'Snapchat', 'icon' => 'fab fa-snapchat', 'color' => '#FFFC00'],
            'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#25D366'],
            'telegram' => ['name' => 'Telegram', 'icon' => 'fab fa-telegram', 'color' => '#0088CC'],
        ];
        
        return view('admin.social-media.create', compact('socialLinks', 'availablePlatforms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
        ]);

        $settings = SiteSetting::firstOrCreate([], []);
        
        // Get existing social links
        $socialLinks = [];
        if (isset($settings->social_links) && is_array($settings->social_links)) {
            $socialLinks = $settings->social_links;
        }
        
        // Add or update the platform URL
        $socialLinks[$validated['platform']] = $validated['url'];
        
        // Also update individual field if it exists
        $fieldName = $validated['platform'] . '_url';
        if (in_array($fieldName, $settings->getFillable())) {
            $settings->$fieldName = $validated['url'];
        }
        
        $settings->social_links = $socialLinks;
        $settings->updated_by = Auth::id();
        $settings->save();

        return redirect()->route('admin.social-media.index')
            ->with('success', __('Social media link added successfully.'));
    }

    public function update(Request $request, $platform)
    {
        $validated = $request->validate([
            'url' => 'nullable|url|max:255',
        ]);

        $settings = SiteSetting::getSettings();
        
        // Get existing social links
        $socialLinks = [];
        if (isset($settings->social_links) && is_array($settings->social_links)) {
            $socialLinks = $settings->social_links;
        }
        
        // Update or remove the platform URL
        if (!empty($validated['url'])) {
            $socialLinks[$platform] = $validated['url'];
        } else {
            unset($socialLinks[$platform]);
        }
        
        // Also update individual field if it exists
        $fieldName = $platform . '_url';
        if (in_array($fieldName, $settings->getFillable())) {
            $settings->$fieldName = $validated['url'] ?? null;
        }
        
        $settings->social_links = $socialLinks;
        $settings->updated_by = Auth::id();
        $settings->save();

        return redirect()->route('admin.social-media.index')
            ->with('success', __('Social media link updated successfully.'));
    }

    public function updateAll(Request $request)
    {
        $validated = $request->validate([
            'urls' => 'nullable|array',
            'urls.*' => 'nullable|url|max:255',
        ]);

        $settings = SiteSetting::getSettings();
        
        // Get existing social links
        $socialLinks = [];
        if (isset($settings->social_links) && is_array($settings->social_links)) {
            $socialLinks = $settings->social_links;
        }
        
        // Update all provided URLs
        if (isset($validated['urls']) && is_array($validated['urls'])) {
            foreach ($validated['urls'] as $platform => $url) {
                if (!empty($url)) {
                    $socialLinks[$platform] = $url;
                    
                    // Also update individual field if it exists
                    $fieldName = $platform . '_url';
                    if (in_array($fieldName, $settings->getFillable())) {
                        $settings->$fieldName = $url;
                    }
                } else {
                    // Remove if URL is empty
                    unset($socialLinks[$platform]);
                    
                    $fieldName = $platform . '_url';
                    if (in_array($fieldName, $settings->getFillable())) {
                        $settings->$fieldName = null;
                    }
                }
            }
        }
        
        $settings->social_links = $socialLinks;
        $settings->updated_by = Auth::id();
        $settings->save();

        return redirect()->route('admin.social-media.index')
            ->with('success', __('Social media links updated successfully.'));
    }

    public function destroy($platform)
    {
        $settings = SiteSetting::getSettings();
        
        // Get existing social links
        $socialLinks = [];
        if (isset($settings->social_links) && is_array($settings->social_links)) {
            $socialLinks = $settings->social_links;
        }
        
        // Remove the platform
        unset($socialLinks[$platform]);
        
        // Also clear individual field if it exists
        $fieldName = $platform . '_url';
        if (in_array($fieldName, $settings->getFillable())) {
            $settings->$fieldName = null;
        }
        
        $settings->social_links = $socialLinks;
        $settings->updated_by = Auth::id();
        $settings->save();

        return redirect()->route('admin.social-media.index')
            ->with('success', __('Social media link deleted successfully.'));
    }
}
