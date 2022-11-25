<?php

namespace App\Http\Requests;

use App\Models\Types\OrderItemStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderItemRequest extends FormRequest
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
            'order_id' => 'required|integer',
            'order_local_number' => 'required|integer',
            'product_id' => 'required|integer',
            'status' => 'required|in:' . OrderItemStatusEnum::toRule(),
            'price' => 'required|number',
            'preparation_by' => 'integer',
            'notes' => 'string'
        ];

        return $rules;
    }
}
