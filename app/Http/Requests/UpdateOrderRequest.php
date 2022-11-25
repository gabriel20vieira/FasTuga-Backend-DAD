<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Types\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'status' => [
                Rule::in(OrderStatusEnum::toArray())
            ]
        ];

        return $rules;
    }
}
