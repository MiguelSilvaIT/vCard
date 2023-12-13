<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Vcard;

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
            'vcard' => 'required|string|min:9|max:9|regex:/^[0-9]{9}$/|different:pair_vcard|exists:vcards,phone_number',
            'payment_type' => 'required|string|in:VCARD,MBWAY,PAYPAL,IBAN,MB,VISA',
            'description' => 'nullable|string',
            'spare_change' => 'nullable|boolean',
            'type' => 'nullable|string|in:D,C',
            'category_id' => 'nullable|exists:categories,id',
            'payment_ref' => ['required', 
            function ($atribute,$value,$fail){
                switch ($this->payment_type) {
                    case 'VCARD':
                        if(!preg_match('/^9[0-9]{8}$/',$value)){
                            $fail('Destination vcard has to be a valid phone number e.g(9xxxxxxxx)');
                        }
                        if($value === $this->vcard){
                            $fail('Destination vcard has to be different from origin card');
                        }
                        if(!Vcard::where('phone_number',$value)->exists()){
                            $fail('Destination vcard does not exist');
                        }
                        break;
                    case 'MBWAY':
                        if(!preg_match('/^9[0-9]{8}$/',$value)){
                            $fail('Payment reference has to be a valid phone number e.g(9xxxxxxxx)');
                        }
                        if($value === $this->vcard){
                            $fail('Payment reference has to be different from origin card');
                        }
                        break;
                    case 'PAYPAL':
                        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                            $fail('Payment reference has to be a valid email');
                        }
                        break;
                    case 'IBAN':
                        if(!preg_match('/^[a-zA-Z]{2}[0-9]{23}$/',$value)){
                            $fail('Payment reference has to be a valid IBAN');
                        }
                        break;
                    case 'MB':
                        if(!preg_match('/^[0-9]{5}-[0-9]{9}$/',$value)){
                            $fail('Payment reference has to be a valid MB reference');
                        }
                        break;
                    case 'VISA':
                        if(!preg_match('/^4[0-9]{15}$/',$value)){
                            $fail('Payment reference has to be a valid VISA card number');
                        }
                        break;
                    default:
                        $fail('Payment type required');
                        break;
                }
            }]
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        //give me the error messages for the rules above in english 



        return [
            'value.required' => 'Value is required',
            'value.numeric' => 'Value has to be a number',
            'value.min' => 'Value has to be at least 0.01',
            'vcard.required' => 'Origin card is required',
            'vcard.string' => 'Origin card has to be a string',
            'vcard.min' => 'Origin card has to have 9 digits',
            'vcard.max' => 'Origin card has to have 9 digits',
            'vcard.regex' => 'Origin card has to be a valid phone number',
            'vcard.different' => 'Origin card has to be different from destination card',
            'vcard.exists' => 'Origin card does not exist',
            'category_id.exists' => 'Category does not exist',
            'type.string' => 'Type has to be a string',
            'type.in' => 'Type has to be Debit or Credit',
            'payment_type.required' => 'Payment type is required',
            'payment_type.string' => 'Payment type has to be a string',
            'payment_type.in' => 'Payment type has to be VCARD, MBWAY, PAYPAL, IBAN, MB or VISA',
            'pair_vcard.required_unless' => 'Destination card is required',
            'pair_vcard.string' => 'Destination card has to be a string',
            'pair_vcard.min' => 'Destination card has to have 9 digits',
            'pair_vcard.max' => 'Destination card has to have 9 digits',
            'pair_vcard.exists' => 'Destination card does not exist',
            'pair_vcard.regex' => 'Destination card has to be a valid phone number',
            'description.string' => 'Description has to be a string',
            'spare_change.boolean' => 'Spare change has to be true or false',
            'payment_ref.required' => 'Payment reference is required',
            'payment_ref.string' => 'Payment reference has to be a string',
            'payment_ref.min' => 'Payment reference has to have 9 digits',
            'payment_ref.max' => 'Payment reference has to have 9 digits',
            'payment_ref.digits' => 'Payment reference has to have 9 digits',
            'payment_ref.different' => 'Payment reference has to be different from origin card',
            'payment_ref.exists' => 'Payment reference does not exist',
            'payment_ref.regex' => 'Payment reference has to be a valid phone number',
            'payment_ref.regex' => 'Payment reference has to be a valid IBAN',
            'payment_ref.regex' => 'Payment reference has to be a valid MBWAY phone number',
            'payment_ref.regex' => 'Payment reference has to be a valid VISA card number',
            'payment_ref.email' => 'Payment reference has to be a valid email',
        ];
    }
}
