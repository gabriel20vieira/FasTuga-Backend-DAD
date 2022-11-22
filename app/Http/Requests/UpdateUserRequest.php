<?php

namespace App\Http\Requests;

use App\Models\Types\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => 'required',
            'email' => [
                'required', 'email',
                Rule::unique('users')->ignore($this->user),
            ],
            'type' => 'required|in:' . UserType::toRule(),
        ];
    }
}
// 'email' => [
//     'required', 'email',
//     Rule::unique('users')->ignore($this->user),
// ],