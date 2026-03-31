<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (config('app.env') !== 'production') {
            return $next($request);
        }

        if (empty($bearerToken) || $bearerToken !== config('auth.auth_token')) {
            return response()->json([
                'code' => 401,
                'message' => __('auth.unauthorized'),
            ], 401);
        }

        return $next($request);
    }
}
