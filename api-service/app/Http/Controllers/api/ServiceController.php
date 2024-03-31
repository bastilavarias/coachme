<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\ListServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Services\ServiceService;
use Exception;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function create(
        CreateServiceRequest $request,
        ServiceService $service
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
        UpdateServiceRequest $request,
        $serviceID,
        ServiceService $service
    ) {
        try {
            $payload = $request->all();
            $payload['service_id'] = $serviceID;
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

    public function list(ListServiceRequest $request, ServiceService $service)
    {
        try {
            $payload = $request->all();
            $payload['user_id'] = Auth::id();
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

    public function delete($serviceID, ServiceService $service)
    {
        try {
            $payload['service_id'] = $serviceID;
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
}
