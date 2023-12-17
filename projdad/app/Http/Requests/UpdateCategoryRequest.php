<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            
            'type' => 'required',
            'name' => [
                'required',
               
            ],
        ];
        return [
            'vcard' => 'required',
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'type'=> 'required|in:C,D',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than :max characters.',
            'name.unique' => 'The name has already been taken.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type field must be C or D.',
        ];
    }
}
