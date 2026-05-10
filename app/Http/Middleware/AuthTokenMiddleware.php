<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthTokenMiddleware
{
    public function handle($request, Closure $next)
    {
        
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'No token provided'], 401);
        }

        
        $user = User::where('token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

      
        Auth::setUser($user);

        return $next($request);
    }
}