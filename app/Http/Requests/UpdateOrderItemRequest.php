<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Types\OrderItemStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemRequest extends FormRequest
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
                Rule::in(OrderItemStatusEnum::toArray())
            ]
        ];

        return $rules;
    }
}
