<?php

namespace App\Http\Requests;

use App\Models\Types\OrderStatusEnum;
use App\Models\Types\PaymentType;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'ticket_number' => 'required|integer|between:1,99',
            'total_price' => 'required|number',
            'total_paid' => 'required|number',
            'total_paid_with_points' => 'required|number',
            'points_gained' => 'integer',
            'points_used_to_pay' => 'integer',
            'payment_type' => 'in:' . PaymentType::toRule(),
            'payment_reference' => 'required|string|max:255'
        ];

        return $rules;
    }
}
