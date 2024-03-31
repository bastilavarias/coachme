<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DoOauthenticationRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function doOauthAuthentication(
        DoOauthenticationRequest $request,
        AuthService $service
    ) {
        try {
            $payload = $request->all();
            $payload['ip'] = $request->ip() ?? null;
            $payload['user_agent'] = $request->header('User-Agent') ?? null;
            $data = $service->doOauthAuthentication($payload);
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

    public function login(LoginRequest $request, AuthService $service)
    {
        try {
            $payload = $request->all();
            $payload['ip'] = $request->ip() ?? null;
            $payload['user_agent'] = $request->header('User-Agent') ?? null;
            $data = $service->login($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (\Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function register(RegisterRequest $request, AuthService $service)
    {
        try {
            $payload = $request->all();
            $payload['ip'] = $request->ip() ?? null;
            $payload['user_agent'] = $request->header('User-Agent') ?? null;
            $data = $service->register($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (\Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function refresh(Request $request, AuthService $service)
    {
        try {
            $payload = $request->all();
            $payload['user_id'] = Auth::id();
            $payload['ip'] = $request->ip() ?? null;
            $payload['user_agent'] = $request->header('User-Agent') ?? null;
            $data = $service->refresh($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (\Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function logout(AuthService $service)
    {
        try {
            $payload = [
                'user_id' => Auth::id(),
            ];
            $data = $service->logout($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (\Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }
}
