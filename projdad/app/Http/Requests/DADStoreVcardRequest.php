<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DADStoreVcardRequest extends FormRequest
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
    public function rules()
    {
        return [
            'phone_number' => ['required', 'string', 'digits:9', 'regex:/^9/', 'unique:vcards'],
            'password' => 'required|string',
            'confirmation_code' => ['required', 'string', 'digits:4'],
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'base64ImagePhoto' => 'nullable|string',
        ];
    }
}
