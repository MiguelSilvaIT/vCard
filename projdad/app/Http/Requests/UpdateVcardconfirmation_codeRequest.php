<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVcardconfirmation_codeRequest extends FormRequest
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
        return [
            'confirmation_code' => 'required|digits:4',
            'oldconfirmation_code' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'confirmation_code.required' => 'Confirmation code is required',
            'confirmation_code.digits' => 'Confirmation code must be 4 digits',
            'oldconfirmation_code.required' => 'Old confirmation code is required',
        ];
    }
}
