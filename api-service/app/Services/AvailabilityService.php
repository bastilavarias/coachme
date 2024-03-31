<?php

namespace App\Services;

use App\Models\Availability;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class AvailabilityService
{
    public function create($payload)
    {
        if (
            !empty(
                Availability::where('user_id', $payload['user_id'])
                    ->where('day_of_week', $payload['day_of_week'])
                    ->whereTime(
                        'time_from',
                        Carbon::parse($payload['time_from'])->toTimeString()
                    )
                    ->whereTime(
                        'time_to',
                        Carbon::parse($payload['time_to'])->toTimeString()
                    )
                    ->first()
            )
        ) {
            throw new Exception('Availability already created.');
        }
        $timeFromCI = Carbon::parse($payload['time_from'])
            ->addSecond()
            ->toTimeString();
        $timeToCI = Carbon::parse($payload['time_to'])
            ->addSecond()
            ->toTimeString();
        if (
            !empty(
                Availability::where('user_id', $payload['user_id'])
                    ->where('day_of_week', $payload['day_of_week'])
                    ->whereBetween('time_from', [$timeFromCI, $timeToCI])
                    ->whereBetween('time_to', [$timeFromCI, $timeToCI])
                    ->first()
            )
        ) {
            throw new Exception('Overlapping of time is not allowed.');
        }
        if (
            !empty(
                Availability::where('user_id', $payload['user_id'])
                    ->where('day_of_week', $payload['day_of_week'])
                    ->whereTime('time_from', '<', $timeFromCI)
                    ->whereTime('time_to', '>', $timeToCI)
                    ->first()
            )
        ) {
            throw new Exception('Inside of the timeslot is not allowed.');
        }

        return Availability::create([
            'user_id' => $payload['user_id'],
            'day_of_week' => $payload['day_of_week'],
            'time_from' => $payload['time_from'],
            'time_to' => $payload['time_to'],
        ]);
    }

    public function list($payload)
    {
        $utils = app()->make(UtilityService::class);
        $query = Availability::query();

        if (isset($payload['user_id'])) {
            $query->where('user_id', $payload['user_id']);
        }
        if (isset($payload['date'])) {
            $desiredDate = Carbon::parse($payload['date']);
            $query
                ->where(
                    'day_of_week',
                    $utils->getDayOfWeekByDate($payload['date'])
                )
                ->when($desiredDate->isToday(), function ($q) {
                    $q->where('time_from', '>=', Carbon::now()->toTimeString());
                })
                ->doesntHave('appointments', 'and', function ($q) {
                    $q->where('status', '!=', 'cancelled');
                });
        } elseif (isset($payload['day_of_week'])) {
            $query->where('day_of_week', $payload['day_of_week']);
        }
        if (isset($payload['is_active'])) {
            $query->where('is_active', $payload['is_active']);
        }

        return $query->orderByDesc('id')->get();
    }

    public function update($payload)
    {
        if (
            !empty(
                Availability::where('user_id', $payload['user_id'])
                    ->where('day_of_week', $payload['day_of_week'])
                    ->whereNotIn('id', [$payload['availability_id']])
                    ->whereTime(
                        'time_from',
                        Carbon::parse($payload['time_from'])->toTimeString()
                    )
                    ->whereTime(
                        'time_to',
                        Carbon::parse($payload['time_to'])->toTimeString()
                    )
                    ->first()
            )
        ) {
            throw new Exception('Availability already created.');
        }
        $timeFromCI = Carbon::parse($payload['time_from'])
            ->addSecond()
            ->toTimeString();
        $timeToCI = Carbon::parse($payload['time_to'])
            ->addSecond()
            ->toTimeString();
        if (
            !empty(
                Availability::where('user_id', $payload['user_id'])
                    ->where('day_of_week', $payload['day_of_week'])
                    ->whereNotIn('id', [$payload['availability_id']])
                    ->whereBetween('time_from', [$timeFromCI, $timeToCI])
                    ->whereBetween('time_to', [$timeFromCI, $timeToCI])
                    ->first()
            )
        ) {
            throw new Exception('Overlapping of time is not allowed.');
        }
        if (
            !empty(
                Availability::where('user_id', $payload['user_id'])
                    ->where('day_of_week', $payload['day_of_week'])
                    ->whereNotIn('id', [$payload['availability_id']])
                    ->whereTime('time_from', '<', $timeFromCI)
                    ->whereTime('time_to', '>', $timeToCI)
                    ->first()
            )
        ) {
            throw new Exception('Inside of the timeslot is not allowed.');
        }
        $availability = Availability::findOrFail($payload['availability_id']);
        $availability->update([
            'day_of_week' => $payload['day_of_week'],
            'time_from' => $payload['time_from'],
            'time_to' => $payload['time_to'],
        ]);

        return $availability;
    }

    public function delete($payload)
    {
        $availability = Availability::findOrFail($payload['availability_id']);
        $availability->delete();

        return $availability;
    }

    public function changeStatus($payload)
    {
        $availability = Availability::findOrFail($payload['availability_id']);
        $availability->update([
            'is_active' => $payload['is_active'],
        ]);

        return $availability;
    }
}
