<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Locales
    |--------------------------------------------------------------------------
    |
    | Contains an array of all the locales supported by the application.
    |
    */
    'supported_locales' => ['en', 'ar'],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale that should be used by the localization service.
    |
    */
    'default_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale that should be used by the localization service when
    | the requested locale is not supported.
    |
    */
    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Locale Detection
    |--------------------------------------------------------------------------
    |
    | Enable/disable automatic locale detection from browser headers.
    |
    */
    'detect_locale' => true,

    /*
    |--------------------------------------------------------------------------
    | Locale Session Key
    |--------------------------------------------------------------------------
    |
    | The session key used to store the current locale.
    |
    */
    'locale_session_key' => 'locale',

    /*
    |--------------------------------------------------------------------------
    | Route Parameter
    |--------------------------------------------------------------------------
    |
    | The route parameter used to set the locale.
    |
    */
    'locale_route_parameter' => 'locale',
];