<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Types\PaymentType;

class UpdateCustomerRequest extends FormRequest
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
            'phone' => ['string', Rule::unique('App\Models\Customer', 'phone')->ignore($this->phone)],
            'nif' => ['required,integer', Rule::unique('App\Models\Customer', 'nif')->ignore($this->nif)],
            'points' => 'integer',
            'default_payment_type' => 'in:' . PaymentType::toRule(),
            'default_payment_reference' => 'required|string',
        ];

        return $rules;
    }
}
