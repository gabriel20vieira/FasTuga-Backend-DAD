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
        return [
            'email' => [
                'email',
                Rule::unique('users')->ignore($this->user),
            ],
            'type' => 'in:' . UserType::toRule(),
        ];
    }
}
