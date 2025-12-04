<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class TranslationFileService
{
    /**
     * Get supported locale codes.
     *
     * @return array
     */
    public function getSupportedLocales(): array
    {
        return config('localization.supported_locales', ['en']);
    }

    /**
     * Load translations from JSON file for a locale.
     *
     * @param string $locale
     * @return array
     */
    public function loadLocale(string $locale): array
    {
        $path = resource_path("lang/{$locale}.json");
        if (!File::exists($path)) {
            return [];
        }
        $content = File::get($path);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Save translations to JSON file for a locale.
     *
     * @param string $locale
     * @param array $data
     * @return void
     */
    public function saveLocale(string $locale, array $data): void
    {
        ksort($data);
        $path = resource_path("lang/{$locale}.json");
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        File::put($path, $json, true);
    }

    /**
     * Set or update a translation key across a locale.
     *
     * @param string $locale
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setKey(string $locale, string $key, string $value): void
    {
        $data = $this->loadLocale($locale);
        $data[$key] = $value;
        $this->saveLocale($locale, $data);
    }

    /**
     * Rename a translation key across a locale.
     *
     * @param string $locale
     * @param string $oldKey
     * @param string $newKey
     * @return void
     */
    public function renameKey(string $locale, string $oldKey, string $newKey): void
    {
        if ($oldKey === $newKey) {
            return;
        }
        $data = $this->loadLocale($locale);
        if (array_key_exists($oldKey, $data)) {
            $data[$newKey] = $data[$oldKey];
            unset($data[$oldKey]);
            $this->saveLocale($locale, $data);
        }
    }

    /**
     * Delete a translation key for a locale.
     *
     * @param string $locale
     * @param string $key
     * @return void
     */
    public function deleteKey(string $locale, string $key): void
    {
        $data = $this->loadLocale($locale);
        if (array_key_exists($key, $data)) {
            unset($data[$key]);
            $this->saveLocale($locale, $data);
        }
    }
}