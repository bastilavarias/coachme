<?php

namespace App\Services;

use App\Enums\AppointmentStatusCase;
use App\Models\Appointment;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;

class AppointmentService
{
    public function create($payload)
    {
        return Appointment::create([
            'instructor_id' => $payload['instructor_id'],
            'service_id' => $payload['service_id'],
            'student_id' => $payload['student_id'],
            'availability_id' => $payload['availability_id'],
            'date' => $payload['date'],
            'meeting_url' => $payload['meeting_url'],
            'status' => AppointmentStatusCase::Pending->value,
        ]);
    }

    public function list($payload)
    {
        $query = Appointment::query();
        if (isset($payload['date'])) {
            $query->whereDate('date', Carbon::parse($payload['date']));
        }
        if (isset($payload['user_id'])) {
            $user = User::findOrFail($payload['user_id']);
            $query->where(
                $user->level === 'student' ? 'student_id' : 'instructor_id',
                $payload['user_id']
            );
        }
        if (isset($payload['status'])) {
            $query->where('status', $payload['status']);
            if ($payload['status'] === AppointmentStatusCase::Upcoming->value) {
                if (isset($payload['tomorrow'])) {
                    $query
                        ->where('status', $payload['status'])
                        ->whereDate('date', '>', Carbon::today());
                } else {
                    $query
                        ->where('status', $payload['status'])
                        ->whereDate('date', Carbon::today());
                }
            }
        }
        if (isset($payload['count_only'])) {
            return $query->count();
        }

        return $query
            ->with([
                'student.image',
                'instructor.image',
                'student.reviews.reviewer.image',
                'instructor.reviews.reviewer.image',
                'student.profile',
                'instructor.profile',
                'availability',
                'service',
                'feedbacks.reviewer',
                'feedbacks.reviewee',
            ])
            ->whereHas('availability')
            ->orderBy('id', $payload['order_by'] ?? 'desc')
            ->get();
    }

    public function update($payload)
    {
        $appointment = Appointment::findOrFail($payload['appointment_id']);
        $appointment->update([
            'status' => $payload['status'],
        ]);

        return $appointment;
    }
}
