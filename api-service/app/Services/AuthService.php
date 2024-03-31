<?php

namespace App\Services;

use App\Enums\AuthProviderCase;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthService
{
    public function doOauthAuthentication($payload)
    {
        $provider = $payload['provider'];

        if ($provider == AuthProviderCase::GitHub->value) {
            $user = $this->beginGitHubOAuthorization($payload['code']);
        } else {
            throw new Exception("Provider $provider is not supported.");
        }
        $userDB = User::updateOrCreate(
            ['provider_id' => $user['id']],
            [
                'provider_id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'] ?? null,
                'email_verified_at' => isset($user['email'])
                    ? Carbon::now()
                    : null,
                'ip' => $payload['ip'] ?? null,
                'user_agent' => $payload['user_agent'] ?? null,
            ]
        );

        $userDB->image()->create([
            'url' => $user['avatar'] ?? null,
        ]);

        $level = $payload['level'];
        if (empty($userDB->level)) {
            $userDB->update(['level' => $level]);
        } elseif ($userDB->level !== $level) {
            throw new Exception("Account with a type of $level was not found.");
        }
        $userDB->update(['is_online' => 1]);
        $userDB->load(['profile', 'image']);

        return [
            'access_token' => $userDB->createToken('access_token')->accessToken,
            'user' => $userDB,
        ];
    }

    public function login($payload)
    {
        if (
            !Auth::attempt([
                'email' => $payload['email'],
                'password' => $payload['password'],
            ])
        ) {
            throw new Exception('Invalid credentials.');
        }
        $user = User::with(['image', 'profile'])->findOrFail(Auth::id());
        $user = tap($user)->update([
            'ip' => $payload['ip'] ?? null,
            'user_agent' => $payload['user_agent'] ?? null,
            'is_online' => 1,
        ]);
        $accessToken = $user->createToken('access_token')->accessToken;

        return [
            'access_token' => $accessToken,
            'user' => $user,
        ];
    }

    public function register($payload)
    {
        $userService = app()->make(UserService::class);
        $user = $userService->create($payload);
        $userDB = User::with(['image', 'profile'])->findOrFail($user->id);
        $user = tap($user)->update([
            'is_online' => 1,
        ]);
        $accessToken = $user->createToken('access_token')->accessToken;

        return [
            'access_token' => $accessToken,
            'user' => $userDB,
        ];
    }

    public function refresh($payload)
    {
        $user = User::with(['image', 'profile'])->findOrFail(
            $payload['user_id']
        );
        $user = tap($user)->update([
            'ip' => $payload['ip'] ?? null,
            'user_agent' => $payload['user_agent'] ?? null,
            'is_online' => 1,
        ]);
        $accessToken = $user->createToken('access_token')->accessToken;

        return [
            'access_token' => $accessToken,
            'user' => $user,
        ];
    }

    public function logout($payload)
    {
        $user = User::findOrFail($payload['user_id']);
        $user->update(['is_online' => 0]);
        $user->tokens()->delete();

        return [];
    }

    private function beginGitHubOAuthorization($code)
    {
        try {
            $response = Http::post(
                'https://github.com/login/oauth/access_token',
                [
                    'client_id' => config('services.github.client_id'),
                    'client_secret' => config('services.github.client_secret'),
                    'code' => $code,
                    'accept' => 'json',
                ]
            );
            $token = explode('=', explode('&', $response)[0])[1];
            $user = Socialite::driver(AuthProviderCase::GitHub->value)
                ->stateless()
                ->userFromToken($token);

            return json_decode(json_encode($user), true);
        } catch (Exception $e) {
            throw new Exception('Session expired.');
        }
    }
}
