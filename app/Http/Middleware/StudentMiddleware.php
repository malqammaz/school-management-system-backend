<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'student') {
            return response()->json(['message' => 'Forbidden. Only students can access.'], 403);
        }

        return $next($request);
    }
}
