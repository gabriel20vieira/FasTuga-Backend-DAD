<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Types\ProductType;
use App\Http\Requests\StoreImageRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => [
                'sometimes',
                Rule::unique('products', 'name')->ignore($this->name, 'name')
            ],
            'type' => 'sometimes|in:' . ProductType::toRule(),
            'image' => 'sometimes|imageable',
            'price' => 'sometimes|numeric'
        ];

        return $rules;
    }

    /**
     * Default messages
     *
     * @return void
     */
    public function messages()
    {
        $messages = [
            'type.in' => "The selected type is invalid. One is required: " . ProductType::toString()
        ];

        $messages = array_merge($messages, (new StoreImageRequest())->messages());

        return $messages;
    }
}
