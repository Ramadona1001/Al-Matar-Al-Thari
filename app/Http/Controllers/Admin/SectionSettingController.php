<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SectionSettingRequest;
use App\Models\SectionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SectionSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = SectionSetting::query();

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('section_key', 'like', "%{$search}%")
                  ->orWhereHas('translations', function($tq) use ($search) {
                      $tq->where('title', 'like', "%{$search}%")
                         ->orWhere('subtitle', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $settings = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.section-settings.index', compact('settings', 'locales'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        // Predefined section keys
        $sectionKeys = [
            'banner_section',
            'services_section',
            'faq_section',
            'testimonials_section',
            'blogs_section',
            'statistics_section',
            'how_it_works_section',
            'companies_section',
            'offers_section',
            'newsletter_section',
            'contact_section',
            'about_section',
            'features_section',
        ];

        return view('admin.cms.section-settings.create', compact('locales', 'sectionKeys'));
    }

    public function store(SectionSettingRequest $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $validated = $request->validated();

        $setting = SectionSetting::create([
            'section_key' => $validated['section_key'],
            'is_active' => $request->has('is_active'),
            'order' => $validated['order'] ?? 0,
            'options' => $validated['options'] ?? [],
        ]);

        foreach ($locales as $locale) {
            $translation = $setting->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}");
            $translation->subtitle = $request->input("subtitle.{$locale}");
            $translation->save();
        }
        
        // Refresh the setting to ensure translations are loaded
        $setting->refresh();

        return redirect()->route('admin.section-settings.index')
            ->with('success', __('Section setting created successfully.'));
    }

    public function edit(SectionSetting $sectionSetting)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $sectionKeys = [
            'banner_section',
            'services_section',
            'faq_section',
            'testimonials_section',
            'blogs_section',
            'statistics_section',
            'how_it_works_section',
            'companies_section',
            'offers_section',
            'newsletter_section',
            'contact_section',
            'about_section',
            'features_section',
        ];

        return view('admin.cms.section-settings.edit', compact('sectionSetting', 'locales', 'sectionKeys'));
    }

    public function update(SectionSettingRequest $request, SectionSetting $sectionSetting)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $validated = $request->validated();

        $sectionSetting->update([
            'section_key' => $validated['section_key'],
            'is_active' => $request->has('is_active'),
            'order' => $validated['order'] ?? 0,
            'options' => $validated['options'] ?? [],
        ]);

        foreach ($locales as $locale) {
            $translation = $sectionSetting->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}");
            $translation->subtitle = $request->input("subtitle.{$locale}");
            $translation->save();
        }
        
        // Refresh the setting to ensure translations are loaded
        $sectionSetting->refresh();

        return redirect()->route('admin.section-settings.index')
            ->with('success', __('Section setting updated successfully.'));
    }

    public function destroy(SectionSetting $sectionSetting)
    {
        $sectionSetting->delete();

        return redirect()->route('admin.section-settings.index')
            ->with('success', __('Section setting deleted successfully.'));
    }
}
