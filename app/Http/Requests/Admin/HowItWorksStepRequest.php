<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HowItWorksStepRequest extends FormRequest
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
            'icon' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'step_number' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["title_{$locale}"] = 'required|string|max:255';
            $rules["description_{$locale}"] = 'required|string';
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
        $messages = [
            'step_number.required' => __('The step number field is required.'),
            'step_number.integer' => __('The step number must be an integer.'),
            'step_number.min' => __('The step number must be at least :min.'),
            'image_path.image' => __('The image must be an image file.'),
            'image_path.mimes' => __('The image must be a file of type: :values.'),
            'image_path.max' => __('The image may not be greater than :max kilobytes.'),
        ];

        foreach ($locales as $locale) {
            $localeUpper = strtoupper($locale);
            $messages["title_{$locale}.required"] = __("The title field ({$localeUpper}) is required.");
            $messages["title_{$locale}.max"] = __("The title field ({$localeUpper}) may not be greater than :max characters.");
            $messages["description_{$locale}.required"] = __("The description field ({$localeUpper}) is required.");
        }

        return $messages;
    }
}
