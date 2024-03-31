<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function create($payload)
    {
        $availabilityService = app()->make(AvailabilityService::class);
        $serviceService = app()->make(ServiceService::class);
        $user = User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => bcrypt($payload['password']),
            'level' => $payload['level'],
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'ip' => $payload['ip'] ?? null,
            'user_agent' => $payload['user_agent'] ?? null,
        ]);
        $user->profile()->create([
            'bio' => 'This a default bio.',
            'occupation' => 'Default Work',
        ]);
        $payload['user_id'] = $user->id;
        $imageStorageService = new ImageStorageService();
        $imageStorageService->create($payload);
        if ($user->level === 'instructor') {
            $daysOfWeek = [1, 2, 3, 4, 5, 6, 7];
            foreach ($daysOfWeek as $day) {
                $availabilityService->create([
                    'user_id' => $user->id,
                    'day_of_week' => $day,
                    'time_from' => '9:00:00',
                    'time_to' => '10:00:00',
                ]);
            }
            $serviceService->create([
                'user_id' => $user->id,
                'title' => 'Coaching',
            ]);
        }

        return $user;
    }
    public function update($payload)
    {
        $user = User::findOrFail($payload['user_id']);
        $user->update([
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);
        $imageStorageService = new ImageStorageService();
        $imageStorageService->replace($payload);
        if (!empty($user->profile)) {
            $user->profile()->update([
                'mobile_number' => $payload['mobile_number'] ?? null,
                'bio' => $payload['bio'] ?? null,
                'occupation' => $payload['occupation'] ?? null,
            ]);
        } else {
            $user->profile()->create([
                'mobile_number' => $payload['mobile_number'] ?? null,
                'bio' => $payload['bio'] ?? null,
                'occupation' => $payload['occupation'] ?? null,
            ]);
        }
        $user->load(['image', 'profile']);
        return $user;
    }

    public function getOne($payload)
    {
        $utils = app()->make(UtilityService::class);
        $dayOfWeek = $utils->getDayOfWeek();
        return User::with([
            'profile',
            'image',
            'services',
            'reviews.reviewer.image',
            'reviews.reviewer.image',
        ])
            ->when(isset($payload['with_availabilities']), function ($q) use (
                $dayOfWeek
            ) {
                $q->with([
                    'availabilities' => function ($availabilityQuery) use (
                        $dayOfWeek
                    ) {
                        $availabilityQuery->where('day_of_week', $dayOfWeek);
                    },
                ]);
            })
            ->when(isset($payload['with_services']), function ($q) {
                $q->with(['services']);
            })
            ->findOrFail($payload['user_id']);
    }

    public function list($payload)
    {
        $query = User::query();
        if (isset($payload['user_id'])) {
            $query->where('id', $payload['user_id']);
        }
        if (isset($payload['level'])) {
            $query->where('level', $payload['level']);
        }
        if (isset($payload['search'])) {
            $query->where(
                DB::raw('LOWER(CONCAT(name," ",email))'),
                'like',
                '%' . strtolower($payload['search']) . '%'
            );
        }

        return $query
            ->with(['profile', 'services', 'image'])
            ->orderBy('id', $payload['order_by'] ?? 'desc')
            ->get();
    }
}
