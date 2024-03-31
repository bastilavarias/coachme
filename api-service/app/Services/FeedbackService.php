<?php

namespace App\Services;

use App\Models\Feedback;

class FeedbackService
{
    public function create($payload)
    {
        $feedback = Feedback::create([
            'reviewer_id' => $payload['reviewer_id'],
            'reviewee_id' => $payload['reviewee_id'],
            'rating' => $payload['rating'],
            'appointment_id' => $payload['appointment_id'],
            'comment' => $payload['comment'],
        ]);
        $feedback->load(['appointment', 'reviewer.image', 'reviewee.image']);
        $feedbacksCount = Feedback::where(
            'appointment_id',
            $payload['appointment_id']
        )->count();
        if ($feedbacksCount > 1) {
            $feedback->appointment()->update([
                'status' => 'past',
            ]);
        }

        return $feedback;
    }
}
