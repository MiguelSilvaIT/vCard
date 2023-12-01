<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVcardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
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
            'phone_number' => 'required|string|min:9|max:9|regex:/^9[0-9]*$/',
            'password' => 'required|string',
            'confirmation_code' => 'required|numeric',
            'balance' => 'required|numeric',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return [
            'phone_number.required' => 'O número de telemóvel é obrigatório',
            'phone_number.string' => 'O número de telemóvel tem de ser uma string',
            'phone_number.min' => 'O número de telemóvel tem de ter 9 caracteres',
            'phone_number.max' => 'O número de telemóvel tem de ter 9 caracteres',
            'phone_number.regex' => 'O número de telemóvel tem de começar por 9',
            'password.required' => 'A password é obrigatória',
            'password.string' => 'A password tem de ser uma string',
            'confirmation_code.required' => 'O código de confirmação é obrigatório',
            'confirmation_code.numeric' => 'O código de confirmação tem de ser numérico',
            'balance.required' => 'O saldo é obrigatório',
            'balance.numeric' => 'O saldo tem de ser numérico',
        ];
    }
}
