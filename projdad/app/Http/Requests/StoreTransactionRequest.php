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

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor tem de ser numérico',
            'vcard.required' => 'O vcard é obrigatório',
            'vcard.string' => 'O vcard tem de ser uma string',
            'vcard.min' => 'O vcard tem de ter 9 caracteres',
            'vcard.max' => 'O vcard tem de ter 9 caracteres',
            'pair_vcard.required' => 'O vcard é obrigatório',
            'pair_vcard.string' => 'O vcard tem de ser uma string',
            'pair_vcard.min' => 'O vcard tem de ter 9 caracteres',
            'pair_vcard.max' => 'O vcard tem de ter 9 caracteres',
            'description.string' => 'A descrição tem de ser uma string',
            'spare_change.boolean' => 'O spare_change tem de ser um booleano',
        ];
    }
}
