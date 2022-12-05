<?php

namespace App\Http\Requests;

use App\Models\Types\PaymentType;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'payment' => 'required',
            'payment.type' => 'required|in:' . PaymentType::toRule(),
            'payment.reference' => 'required|string|reference:payment.type',
        ];
    }


    public function messages()
    {
        return [
            'payment.reference.reference' => "The :attribute reference is not valid."
        ];
    }
}
