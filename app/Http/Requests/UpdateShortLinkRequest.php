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
            'user_id' => 'required|exists:users,id',
            'original_url' => 'required|url',
            'short_code' => 'nullable|unique:short_links,short_code|min:6|max:8',
            'access_count' => 'integer|min:0',
            'expiration_date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'original_url.required' => 'The URL is required.',
            'original_url.url' => 'The link must be a valid URL.',
            'short_code.unique' => 'The Short Code has already been taken.',
            'short_code.min' => 'The Short Code must be at least 6 characters.',
            'short_code.max' => 'The Short Code may not be greater than 8 characters.',
            'access_count.integer' => 'The access count must be an integer.',
            'access_count.min' => 'The access count must be at least 0.',
            'expiration_date.required' => 'The expiration date is required.',
            'expiration_date.date' => 'The expiration date must be a valid date.',
        ];
    }
}
