<?php

namespace App\Http\Requests;

use App\Models\Types\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends StoreUserRequest
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
        $rules = array_merge(parent::rules(), [
            'phone' => 'required|phone|unique:customers,phone',
            'nif' => 'required|nif',
            'default_payment_type' => 'required|in:' . PaymentType::toRule(),
            "default_payment_reference" => "required|reference:default_payment_type"
        ]);
        unset($rules['type']);

        return $rules;
    }

    public function messages()
    {
        $messages = array_merge(parent::messages(), [
            'nif.nif' => 'The :attribute is not valid.',
            'phone.phone' => "The :attribute is not valid.",
            'default_payment_type.in' => 'The selected :attribute must be either ' . PaymentType::toString(),
            "default_payment_reference.reference" => "The :attribute reference is not valid."
        ]);

        return $messages;
    }
}
