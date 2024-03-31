<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|min:4|max:255',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:6|required_with:password_confirmation',
            'password_confirmation' => 'same:password',
            'level' => 'required|string|in:student,instructor',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:512', // 512kb only
        ];
    }
}
