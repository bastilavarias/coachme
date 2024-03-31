<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class UtilityService
{
    public function getDayOfWeek()
    {
        $dayOfWeek = Carbon::now()->dayOfWeek;
        if ($dayOfWeek === 0) {
            $dayOfWeek = 7;
        }
        return $dayOfWeek;
    }

    public function getDayOfWeekByDate($date)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        if ($dayOfWeek === 0) {
            $dayOfWeek = 7;
        }
        return $dayOfWeek;
    }
}
