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
            'original_url' => 'required|url',
            'short_code' => 'nullable|unique:short_links|min:6|max:8',
        ];
    }

    public function messages()
    {
        return [
            'original_url.required' => 'The URL is required.',
            'original_url.url' => 'The link must be a valid URL.',
            'short_code.unique' => 'The identifier has already been taken.',
            'short_code.min' => 'The identifier must be at least 6 characters.',
            'short_code.max' => 'The identifier may not be greater than 8 characters.',
        ];
    }
}
