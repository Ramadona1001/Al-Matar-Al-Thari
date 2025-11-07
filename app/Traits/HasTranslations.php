<?php

namespace App\Traits;

trait HasTranslations
{
    /**
     * Get translation for a specific locale.
     */
    public function getTranslation(string $attribute, ?string $locale = null): mixed
    {
        $locale = $locale ?? app()->getLocale();
        $translations = $this->getAttribute($attribute);

        if (is_array($translations)) {
            return $translations[$locale] ?? $translations[config('localization.fallback_locale', 'en')] ?? null;
        }

        if (is_string($translations)) {
            $decoded = json_decode($translations, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded[$locale] ?? $decoded[config('localization.fallback_locale', 'en')] ?? null;
            }
        }

        return $translations;
    }

    /**
     * Set translation for a specific locale.
     */
    public function setTranslation(string $attribute, string $locale, mixed $value): void
    {
        $translations = $this->getAttribute($attribute);

        if (is_string($translations)) {
            $decoded = json_decode($translations, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $translations = $decoded;
            } else {
                $translations = [];
            }
        } elseif (!is_array($translations)) {
            $translations = [];
        }

        $translations[$locale] = $value;
        $this->setAttribute($attribute, $translations);
    }

    /**
     * Get localized attribute value.
     */
    public function getLocalizedAttribute(string $attribute, ?string $locale = null): mixed
    {
        return $this->getTranslation($attribute, $locale);
    }

    /**
     * Get all translations for an attribute.
     */
    public function getTranslations(string $attribute): array
    {
        $translations = $this->getAttribute($attribute);

        if (is_array($translations)) {
            return $translations;
        }

        if (is_string($translations)) {
            $decoded = json_decode($translations, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Set multiple translations for an attribute.
     */
    public function setTranslations(string $attribute, array $translations): void
    {
        $this->setAttribute($attribute, $translations);
    }

    /**
     * Check if translation exists for locale.
     */
    public function hasTranslation(string $attribute, ?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        $translations = $this->getTranslations($attribute);
        
        return isset($translations[$locale]) && !empty($translations[$locale]);
    }

    /**
     * Get translation or fallback.
     */
    public function getTranslationOrFallback(string $attribute, ?string $locale = null): mixed
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->getTranslation($attribute, $locale);
        
        if (empty($translation)) {
            $fallbackLocale = config('localization.fallback_locale', 'en');
            $translation = $this->getTranslation($attribute, $fallbackLocale);
        }

        return $translation;
    }

    /**
     * Magic method to get localized attributes.
     */
    public function __get($key)
    {
        if (in_array($key, $this->getTranslatableAttributes())) {
            return $this->getTranslationOrFallback($key);
        }

        return parent::__get($key);
    }

    /**
     * Get translatable attributes.
     */
    public function getTranslatableAttributes(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    /**
     * Check if attribute is translatable.
     */
    public function isTranslatableAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->getTranslatableAttributes());
    }

    /**
     * Get attribute with fallback locale.
     */
    public function getAttributeWithFallback(string $attribute): mixed
    {
        if (!$this->isTranslatableAttribute($attribute)) {
            return $this->getAttribute($attribute);
        }

        return $this->getTranslationOrFallback($attribute);
    }
}