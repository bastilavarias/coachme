<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\GetOneUserRequest;
use App\Http\Requests\User\ListUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(
        UpdateUserRequest $request,
        $userID,
        UserService $service
    ) {
        try {
            $payload = $request->all();
            $payload['user_id'] = $userID;
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

    public function index(ListUserRequest $request, UserService $service)
    {
        try {
            $payload = $request->all();
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

    public function show(
        GetOneUserRequest $request,
        $userID,
        UserService $service
    ) {
        try {
            $payload = $request->all();
            $payload['user_id'] = $userID;
            $data = $service->getOne($payload);
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
