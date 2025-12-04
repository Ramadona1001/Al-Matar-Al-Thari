<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TranslationFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TranslationController extends Controller
{
    protected TranslationFileService $service;

    public function __construct(TranslationFileService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $locales = $this->service->getSupportedLocales();
        $translations = [];
        foreach ($locales as $locale) {
            $translations[$locale] = $this->service->loadLocale($locale);
        }

        // Union of keys across all locales
        $keys = collect($translations)
            ->flatMap(fn ($items) => array_keys($items))
            ->unique()
            ->sort()
            ->values();

        return view('admin.translations.index', [
            'locales' => $locales,
            'translations' => $translations,
            'keys' => $keys,
        ]);
    }

    public function store(Request $request)
    {
        $locales = $this->service->getSupportedLocales();
        $validated = $request->validate([
            'key' => 'required|string',
        ]);
        $key = $validated['key'];

        foreach ($locales as $locale) {
            $value = (string)($request->input("values.$locale") ?? '');
            $this->service->setKey($locale, $key, $value);
        }

        return redirect()->route('admin.translations.index')->with('status', __('Translation added.'));
    }

    public function update(Request $request)
    {
        $locales = $this->service->getSupportedLocales();
        $validated = $request->validate([
            'key' => 'required|string',
            'new_key' => 'nullable|string',
        ]);
        $key = $validated['key'];
        $newKey = $validated['new_key'] ?? $key;

        foreach ($locales as $locale) {
            // Rename first so values map to the new key
            if ($newKey !== $key) {
                $this->service->renameKey($locale, $key, $newKey);
            }
            $value = (string)($request->input("values.$locale") ?? '');
            $this->service->setKey($locale, $newKey, $value);
        }

        return redirect()->route('admin.translations.index')->with('status', __('Translation updated.'));
    }

    public function destroy(Request $request)
    {
        $locales = $this->service->getSupportedLocales();
        $validated = $request->validate([
            'key' => 'required|string',
        ]);
        $key = $validated['key'];

        foreach ($locales as $locale) {
            $this->service->deleteKey($locale, $key);
        }

        return redirect()->route('admin.translations.index')->with('status', __('Translation deleted.'));
    }
}