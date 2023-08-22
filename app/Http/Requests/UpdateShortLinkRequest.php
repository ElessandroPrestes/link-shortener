<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShortLinkRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'original_url' => 'url',
            'identifier' => 'nullable|unique:short_links|min:6|max:8',
        ];
    }

    public function messages()
    {
        return [
            'original_url.url' => 'The link must be a valid URL.',
            'identifier.unique' => 'The identifier has already been taken.',
            'identifier.min' => 'The identifier must be at least 6 characters.',
            'identifier.max' => 'The identifier may not be greater than 8 characters.',
        ];
    }
}
