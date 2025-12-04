<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FooterMenuGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $rules = [
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["name_{$locale}"] = 'required|string|max:255';
        }

        return $rules;
    }

    public function messages(): array
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $messages = [];

        foreach ($locales as $locale) {
            $messages["name_{$locale}.required"] = __('The name field is required for :locale language.', ['locale' => strtoupper($locale)]);
        }

        return $messages;
    }
}
