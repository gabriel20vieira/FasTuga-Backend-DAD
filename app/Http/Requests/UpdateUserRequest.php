<?php

namespace App\Http\Requests;

use App\Models\Types\UserType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $rules =  [
            'email' => [
                'sometimes', 'email', Rule::unique('users')->ignore($this->user),
            ],
            'type' => [
                'sometimes', Rule::in(UserType::toArray())
            ],
            'image' => 'sometimes|imageable',
            "default_payment_type" => "sometimes",
            "phone" => "sometimes",
            "nif" => "sometimes",
            "default_payment_type" => "sometimes",
            "name" => "sometimes",
            "blocked" => "sometimes|boolean"
        ];

        $rules = array_merge($rules, (new UpdateUserPasswordRequest())->rules());

        return $rules;
    }



    public function messages()
    {
        $messages = [];

        $messages = array_merge((new StoreImageRequest())->messages(), $messages);

        return $messages;
    }
}
