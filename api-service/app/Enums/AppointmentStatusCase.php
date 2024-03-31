<?php

namespace App\Enums;

enum AppointmentStatusCase: string
{
    case Pending = 'pending';
    case Upcoming = 'upcoming';
    case ForReview = 'for_review';
    case Past = 'past';
    case Cancelled = 'cancelled';
}
