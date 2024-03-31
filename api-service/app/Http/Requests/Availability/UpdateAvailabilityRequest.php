<?php

namespace App\Http\Requests\Availability;

use App\Http\Requests\FormRequest;

class UpdateAvailabilityRequest extends FormRequest
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
            'time_from' => 'required|date_format:H:i:s',
            'time_to' => 'required|date_format:H:i:s|after:time_from',
            'day_of_week' => 'required|in:1,2,3,4,5,6,7',
        ];
    }
}
