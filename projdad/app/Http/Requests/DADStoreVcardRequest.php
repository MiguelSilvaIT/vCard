<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rules\Password;

class DADStoreVcardRequest extends DADUpdateVcardRequest
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
    public function rules():array
    {
        $rules = parent::rules();
        unset($rules['deletePhotoOnServer']);
        unset($rules['blocked']);
        unset($rules['max_debit']);
        return array_merge($rules, [
            'phone_number' => ['required', 'string', 'digits:9', 'regex:/^9/', 'unique:vcards'],
            'password' => 'required|confirmed', Password::min(3),
            'confirmation_code' => 'required|confirmed|digits:4',
            ]);
    }

    public function messages(): array
    {
        return [
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.digits' => 'The phone number must be :digits digits.',
            'phone_number.regex' => 'The phone number must start with 9.',
            'phone_number.unique' => 'The phone number has already been taken.',
        ];
    }
}
