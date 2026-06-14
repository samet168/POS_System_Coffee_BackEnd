<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if (!in_array(strtolower($user->role), $roles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }


}