<?php

namespace App\Http\Requests\Availability;

use App\Http\Requests\FormRequest;

class ListAvailabilityRequest extends FormRequest
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
            'day_of_week' => 'nullable|integer',
            'is_active' => 'nullable|integer',
            'date' => 'nullable|date_format:Y-m-d',
        ];
    }
}
