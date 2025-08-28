<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'teacher') {
            return response()->json(['message' => 'Forbidden. Only teachers can access.'], 403);
        }

        return $next($request);
    }
}
