<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('Authorization');
        
        Log::info('Authorization Header: ' . $authorizationHeader); // Debugging line

        if (!$authorizationHeader || !preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            return response()->json([
                'errors' => [
                    'message' => ['unauthorized']
                ]
            ], 401);
        }

        $token = $matches[1];
        Log::info('Extracted Token: ' . $token); // Debugging line

        $user = User::where('token', $token)->first();
        Log::info('User: ' . $user); // Debugging line

        if (!$user) {
            return response()->json([
                'errors' => [
                    'message' => ['unauthorized']
                ]
            ], 401);
        }

        Auth::login($user);

        return $next($request);
    }
}

