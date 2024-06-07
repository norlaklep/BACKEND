<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next) : Response
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader || !preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            return response()->json([
                'errors' => [
                    'message' => ['unauthorized']
                ]
            ], 401);
        }

        $token = $matches[1];
        $user = User::where('token', $token)->first();

        if (!$user) {
            return response()->json([
                'errors' => [
                    'message' => ['unauthorized']
                ]
            ], 401);
        }

        Auth::login($user);
        Log::info('User authenticated: ' . $user->id);

        return $next($request);
    
    }
}

