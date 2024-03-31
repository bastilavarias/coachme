<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TokenBindingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $tokenBindingId = "$user->ip-$user->user_agent";
        $currentBindingId = $this->generateBindingId($request);
        if ($tokenBindingId !== $currentBindingId) {
            return customResponse()
                ->data([])
                ->message(
                    '[Token binding error] You do not have access to this resource.'
                )
                ->failed(403)
                ->generate();
        }
        return $next($request);
    }

    protected function generateBindingId($request)
    {
        return $request->ip() . '-' . $request->header('User-Agent');
    }
}
