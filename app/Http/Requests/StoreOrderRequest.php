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
            'points_used_to_pay' => 'integer|points|user_points',
            'items' => 'required|array',
            'items.*' => 'required|exists:products,id'
        ];

        return $rules;
    }


    public function messages()
    {
        return [
            'points_used_to_pay.points' => 'The amount of points given must be a multiple of 10',
            'points_used_to_pay.user_points' => 'The user does not have that many points.'
        ];
    }
}
