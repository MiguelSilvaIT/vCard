<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'value' => 'required|numeric',
            'vcard' => 'required|string|min:9|max:9',
            'pair_vcard' => 'required|string|min:9|max:9',
            'description' => 'nullable|string',
            'spare_change' => 'nullable|boolean',
        ];
    }
}
