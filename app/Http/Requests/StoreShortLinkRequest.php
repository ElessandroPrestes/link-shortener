<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShortLinkRequest extends FormRequest
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
            'identifier' => 'nullable|unique:short_links',
        ];
    }

    public function messages()
    {
        return [
            'original_url.url' => 'The link must be a valid URL.',
            'original_url.unique' => 'The link has already been taken.',
            'identifier.unique' => 'The identifier has already been taken.'
        ];
    }
}
