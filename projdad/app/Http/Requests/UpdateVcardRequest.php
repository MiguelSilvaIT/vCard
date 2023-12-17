<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Base64Services;
use Illuminate\Validation\Rule;
use App\Models\Vcard;

class UpdateVcardRequest extends FormRequest
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
        if (isset($this->vcard)) {
            return [
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('vcards')->ignore($this->phone_number, 'phone_number')],
                'blocked' => 'nullable|int|in:0,1',
                'base64ImagePhoto' => 'nullable|string',
                'deletePhotoOnServer' => 'nullable|boolean'
            ];
        }
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vcards',
            'blocked' => 'nullable|int|in:0,1',
            'base64ImagePhoto' => 'nullable|string',
            'deletePhotoOnServer' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than :max characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',

            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least :min characters.',

            'confirmation_code.required' => 'The confirmation code field is required.',
            'confirmation_code.confirmed' => 'The confirmation code confirmation does not match.',
            'confirmation_code.digits' => 'The confirmation code must be :digits digits.',

            'blocked.required' => 'The blocked field is required.',
            'blocked.int' => 'The blocked field must be an integer.',
            'blocked.in' => 'The selected blocked is invalid.',

            'base64ImagePhoto.nullable' => 'The base64 image photo must be a string.',
            'deletePhotoOnServer.nullable' => 'The delete photo on server must be a boolean.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $base64ImagePhoto = $this->base64ImagePhoto ?? null;
            if ($base64ImagePhoto) {
                $base64Service = new Base64Services();
                $mimeType = $base64Service->mimeType($base64ImagePhoto);
                if (!in_array($mimeType, ['image/png', 'image/jpg', 'image/jpeg'])) {
                    $validator->errors()->add('base64ImagePhoto', 'File type not supported (only supports "png" and "jpeg" images).');
                }
            }
        });
    }
}
