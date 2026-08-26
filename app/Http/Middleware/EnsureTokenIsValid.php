<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Token is missing, please login'], 401);
        }

        return $next($request);
    }
}

