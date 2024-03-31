<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Availability\ChangeAvailabilityStatusRequest;
use App\Http\Requests\Availability\CreateAvailabilityRequest;
use App\Http\Requests\Availability\ListAvailabilityRequest;
use App\Http\Requests\Availability\UpdateAvailabilityRequest;
use App\Services\AvailabilityService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function create(
        CreateAvailabilityRequest $request,
        AvailabilityService $service
    ) {
        try {
            $payload = $request->all();
            $payload['user_id'] = Auth::id();
            $data = $service->create($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function update(
        UpdateAvailabilityRequest $request,
        $availabilityID,
        AvailabilityService $service
    ) {
        try {
            $payload = $request->all();
            $payload['availability_id'] = $availabilityID;
            $payload['user_id'] = Auth::id();
            $data = $service->update($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function list(
        ListAvailabilityRequest $request,
        AvailabilityService $service
    ) {
        try {
            $payload = $request->all();
            $payload['user_id'] = $payload['user_id'] ?? Auth::id();
            $data = $service->list($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function delete($availabilityID, AvailabilityService $service)
    {
        try {
            $payload['availability_id'] = $availabilityID;
            $data = $service->delete($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function changeStatus(
        ChangeAvailabilityStatusRequest $request,
        $availabilityID,
        AvailabilityService $service
    ) {
        try {
            $payload = $request->all();
            $payload['availability_id'] = $availabilityID;
            $data = $service->changeStatus($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }
}
