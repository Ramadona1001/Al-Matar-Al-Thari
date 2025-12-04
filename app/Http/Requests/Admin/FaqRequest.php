<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);

        $rules = [
            'category' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["question_{$locale}"] = 'required|string|max:500';
            $rules["answer_{$locale}"] = 'required|string';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $messages = [];

        foreach ($locales as $locale) {
            $localeUpper = strtoupper($locale);
            $messages["question_{$locale}.required"] = __("The question field ({$localeUpper}) is required.");
            $messages["question_{$locale}.max"] = __("The question field ({$localeUpper}) may not be greater than :max characters.");
            $messages["answer_{$locale}.required"] = __("The answer field ({$localeUpper}) is required.");
        }

        return $messages;
    }
}
