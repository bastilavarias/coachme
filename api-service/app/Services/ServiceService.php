<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Carbon;

class ServiceService
{
    public function create($payload)
    {
        return Service::create([
            'user_id' => $payload['user_id'],
            'title' => $payload['title'],
        ]);
    }

    public function update($payload)
    {
        $service = Service::findOrFail($payload['service_id']);

        $service->update([
            'title' => $payload['title'],
        ]);

        return $service;
    }

    public function delete($payload)
    {
        $service = Service::findOrFail($payload['service_id']);

        $service->delete();

        return true;
    }

    public function list($payload)
    {
        $query = Service::query();
        if (isset($payload['user_id'])) {
            $query->where('user_id', $payload['user_id']);
        }

        return $query->orderByDesc('id')->get();
    }
}
