<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class ListUserRequest extends FormRequest
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
            'user_id' => 'nullable|integer',
            'level' => 'nullable|string',
            'sort_by' => 'nullable|string',
            'search' => 'nullable|string',
        ];
    }
}
