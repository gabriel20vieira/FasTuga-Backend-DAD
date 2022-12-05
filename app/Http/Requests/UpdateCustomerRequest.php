<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Types\PaymentType;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends UpdateUserRequest
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
            'email' => [
                'sometimes', 'email', Rule::unique('users')->ignore($this->customer->user),
            ],
            'phone' => ['sometimes', 'string', Rule::unique('App\Models\Customer', 'phone')->ignore($this->customer)],
            'nif' => ['sometimes', 'nif', Rule::unique('App\Models\Customer', 'nif')->ignore($this->customer)],
            'default_payment_type' => 'sometimes|in:' . PaymentType::toRule(),
            'default_payment_reference' => 'sometimes|reference:default_payment_type',
        ]);

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'nif.nif' => 'The :attribute is not valid.'
        ];

        $messages = array_merge($messages, (new StoreImageRequest())->messages());
        $messages = array_merge($messages, (new UpdateUserRequest())->rules());

        return $messages;
    }
}
