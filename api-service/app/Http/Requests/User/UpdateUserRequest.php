<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:4|string',
            'email' => [
                'required',
                'email:rfc,dns',
                Rule::unique('users')->ignore(auth()->id()),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:512', // 512kb only
            'mobile_number' => ['required', 'regex:/^09\d{9}$/'],
            'bio' => 'nullable|string',
        ];
    }
}
