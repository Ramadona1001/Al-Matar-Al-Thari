# Laravel Localization Refactoring Guide

## Overview
This document outlines the refactoring process to replace custom localization with `mcamara/laravel-localization` and `astrotomic/laravel-translatable`.

## Steps Completed
1. ✅ Installed packages: `mcamara/laravel-localization` and `astrotomic/laravel-translatable`
2. ✅ Published configuration files

## Steps Remaining

### 1. Update Configuration Files
- Update `config/laravellocalization.php` with supported locales (en, ar)
- Update `config/translatable.php` with locale settings
- Remove custom `config/localization.php` (or keep for backward compatibility)

### 2. Update Routes
- Replace custom `{locale}` prefix with `LaravelLocalization::setLocale()` routes
- Wrap all routes in `LaravelLocalization::routes()` group
- Update route names to work with locale prefix

### 3. Update Models
- Add `Translatable` trait to: Section, Page, Banner, Blog, Service, Testimonial, Statistic
- Define `$translatedAttributes` array
- Create translation tables (sections_translations, pages_translations, etc.)
- Update migrations to remove JSON fields and use translation tables

### 4. Update Controllers
- Remove custom locale handling
- Use `$model->translate()` or `$model->translateOrDefault()` for accessing translations
- Update store/update methods to save translations

### 5. Update Views
- Replace custom localization helpers with translatable methods
- Use `$section->title` instead of `$section->getTitleForLocale()`
- Update forms to handle multiple languages

### 6. Remove Custom Code
- Remove `app/Http/Middleware/Localization.php`
- Remove `app/Providers/LocalizationServiceProvider.php`
- Remove `app/Services/LocalizationService.php`
- Update `app/Http/Kernel.php` to remove custom middleware

### 7. Update RouteServiceProvider
- Remove custom locale handling
- Keep route model bindings

## Migration Strategy
1. Create new translation tables
2. Migrate existing JSON data to translation tables
3. Test all routes and views
4. Remove old code

