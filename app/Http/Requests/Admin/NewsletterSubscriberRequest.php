<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NewsletterSubscriberRequest extends FormRequest
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
        $subscriberId = $this->route('newsletterSubscriber')?->id;

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:newsletter_subscribers,email,' . $subscriberId,
            ],
            'name' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => __('The email field is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.unique' => __('This email is already subscribed.'),
            'email.max' => __('The email may not be greater than :max characters.'),
            'name.max' => __('The name may not be greater than :max characters.'),
            'source.max' => __('The source may not be greater than :max characters.'),
        ];
    }
}
