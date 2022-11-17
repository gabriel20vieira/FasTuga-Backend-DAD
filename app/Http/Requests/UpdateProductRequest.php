<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Types\ProductType;
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
        // TODO autorização
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
                Rule::unique('App\Models\Product', 'name')->ignore($this->id)
            ],
            'type' => [
                Rule::in(ProductType::toRule())
            ],
            'photo_url' => 'file|image',
            'price' => 'numeric'
        ];

        return $rules;
    }
}
