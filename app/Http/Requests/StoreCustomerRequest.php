<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Types\PaymentType;

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
            'user_id' => 'required|users,id',
            'phone' => 'required|unique|string',
            'points' => 'integer',
            'nif' => 'required|unique,integer',
            'default_payment_type' => 'required|in:' . PaymentType::toRule(),
            'default_payment_reference' => 'required|string',
        ];

        return $rules;
    }
}
