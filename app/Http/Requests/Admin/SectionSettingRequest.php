<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SectionSettingRequest extends FormRequest
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
        $sectionSettingId = $this->route('sectionSetting')?->id;

        $rules = [
            'section_key' => [
                'required',
                'string',
                'max:255',
                'unique:section_settings,section_key,' . $sectionSettingId,
            ],
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'options' => 'nullable|array',
        ];

        foreach ($locales as $locale) {
            $rules["title.{$locale}"] = 'nullable|string|max:255';
            $rules["subtitle.{$locale}"] = 'nullable|string';
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
        return [
            'section_key.required' => __('The section key field is required.'),
            'section_key.unique' => __('This section key already exists.'),
            'section_key.max' => __('The section key may not be greater than :max characters.'),
        ];
    }
}
