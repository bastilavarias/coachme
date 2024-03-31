<?php

namespace App\Http\Requests\Appointment;

use App\Http\Requests\FormRequest;

class ListAppointmentRequest extends FormRequest
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
            'instructor_id' => 'nullable|integer',
            'student_id' => 'nullable|integer',
            'date' => 'nullable|date_format:Y-m-d',
            'status' => 'nullable|string',
            'tomorrow' => 'nullable|string',
        ];
    }
}
