<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $level = $request->user()->level;
        $levels = array_slice(func_get_args(), 2);
        if (in_array($level, $levels) === false) {
            return customResponse()
                ->data([])
                ->message('You do not have access to this resource.')
                ->failed(403)
                ->generate();
        }

        return $next($request);
    }
}
