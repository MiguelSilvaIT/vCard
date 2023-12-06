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
            'value' => 'required|numeric|min:0.01',
            'vcard' => 'required|string|min:9|max:9|different:pair_vcard|exists:vcards,phone_number',
            'payment_type' => 'required|string|in:VCARD,MBWAY,PAYPAL,IBAN,MB,VISA',
            'pair_vcard' => [
                'required_unless:payment_type,MBWAY,PAYPAL,IBAN,MB,VISA',
                'string',
                'min:9',
                'max:9',
                'exists:vcards,phone_number',
            ],
            'description' => 'nullable|string',
            'spare_change' => 'nullable|boolean',
            'payment_ref' => [
                'required_unless:payment_type,VCARD',
                'string',
                $this->payment_type === 'MBWAY' ? 'regex:/^9[0-9]{8}$/' : 
                    ($this->payment_type === 'PAYPAL' ? 'email' : 
                        ($this->payment_type === 'IBAN' ? 'regex:/^[a-zA-Z]{2}[0-9]{23}$/' : 
                            ($this->payment_type === 'MB' ? 'regex:/^[0-9]{5}-[0-9]{9}$/' : 
                                ($this->payment_type === 'VISA' ? 'regex:/^4[0-9]{15}$/' : '')))),
            ],
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
            'value.min' => 'O valor tem de ser superior a 0.01',
            'vcard.required' => 'O Vcard é obrigatório',
            'vcard.string' => 'O Vcard tem de ser uma string',
            'vcard.min' => 'O Vcard tem de ter 9 caracteres',
            'vcard.max' => 'O Vcard tem de ter 9 caracteres',
            'vcard.different' => 'O Vcard não pode ser igual ao Vcard de destino',
            'vcard.exists' => 'O Vcard não existe',
            'payment_type.required' => 'O tipo de pagamento é obrigatório',
            'payment_type.string' => 'O tipo de pagamento tem de ser uma string',
            'payment_type.in' => 'O tipo de pagamento tem de ser VCARD, MBWAY, PAYPAL, IBAN, MB ou VISA',
            'pair_vcard.required_unless' => 'O Vcard de destino é obrigatório',
            'pair_vcard.string' => 'O Vcard de destino tem de ser uma string',
            'pair_vcard.min' => 'O Vcard de destino tem de ter 9 caracteres',
            'pair_vcard.max' => 'O Vcard de destino tem de ter 9 caracteres',
            'pair_vcard.exists' => 'O Vcard de destino não existe',
            'description.string' => 'A descrição tem de ser uma string',
            'spare_change.boolean' => 'O troco tem de ser um boolean',
            'payment_ref.required_unless' => 'A referência de pagamento é obrigatória',
            'payment_ref.string' => 'A referência de pagamento tem de ser uma string',
            'payment_ref.regex' => 'A referência de pagamento não é válida',
            'payment_ref.email' => 'A referência de pagamento não é válida',

        ];
    }
}
