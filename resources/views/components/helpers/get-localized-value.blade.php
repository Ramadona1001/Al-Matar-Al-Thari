@php
/**
 * Helper function to get localized value from JSON string or array
 * Usage: @php $title = getLocalizedValue($section->title ?? '') @endphp
 */
if (!function_exists('getLocalizedValue')) {
    function getLocalizedValue($value, $locale = null) {
        if (empty($value)) {
            return '';
        }

        $locale = $locale ?? app()->getLocale();

        // If it's already an array, use it directly
        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? ($value[array_key_first($value)] ?? '');
        }

        // If it's a JSON string, decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded[$locale] ?? $decoded['en'] ?? ($decoded[array_key_first($decoded)] ?? '');
            }
            // If not valid JSON, return as is
            return $value;
        }

        return '';
    }
}
@endphp

