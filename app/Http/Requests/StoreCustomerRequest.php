<?php

namespace App\Http\Requests;

use App\Models\Types\PaymentType;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'phone' => 'required|unique:customers,phone|string',
            'points' => 'integer',
            'nif' => 'required|unique:customers,nif|nif',
            'default_payment_type' => 'required|in:' . PaymentType::toRule(),
            'default_payment_reference' => 'required|string',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'nif.nif' => 'The :attribute is not valid.'
        ];
    }
}
