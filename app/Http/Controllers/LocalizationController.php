<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocalizationService;

class LocalizationController extends Controller
{
    protected LocalizationService $localization;

    public function __construct(LocalizationService $localization)
    {
        $this->localization = $localization;
    }

    /**
     * Switch application locale.
     */
    public function switchLocale(Request $request, string $locale)
    {
        if (!$this->localization->isValidLocale($locale)) {
            return back()->with('error', __('messages.invalid_locale'));
        }

        $this->localization->setLocale($locale);
        
        return back()->with('success', __('messages.language_changed_successfully'));
    }

    /**
     * Get available locales.
     */
    public function getLocales()
    {
        $locales = [];
        
        foreach ($this->localization->getSupportedLocales() as $locale) {
            $locales[$locale] = [
                'name' => $this->localization->getLocaleName($locale),
                'flag' => $this->localization->getLocaleFlag($locale),
                'direction' => $this->localization->getLocaleDirection($locale),
                'is_current' => $locale === $this->localization->getCurrentLocale(),
            ];
        }

        return response()->json([
            'current' => $this->localization->getCurrentLocale(),
            'locales' => $locales,
        ]);
    }

    /**
     * Get current locale info.
     */
    public function getCurrentLocale()
    {
        $locale = $this->localization->getCurrentLocale();
        
        return response()->json([
            'locale' => $locale,
            'name' => $this->localization->getLocaleName($locale),
            'flag' => $this->localization->getLocaleFlag($locale),
            'direction' => $this->localization->getLocaleDirection($locale),
            'is_rtl' => $this->localization->isRtl(),
        ]);
    }

    /**
     * Get alternative locales for current page.
     */
    public function getAlternativeLocales()
    {
        return response()->json([
            'alternatives' => $this->localization->getAlternativeLocales(),
        ]);
    }
}