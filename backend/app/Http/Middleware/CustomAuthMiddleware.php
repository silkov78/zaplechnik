<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CustomAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check authorization header
        $authToken = $request->bearerToken();

        if (!$authToken) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => [
                    'token' => 'Authorization token is missing.',
                ],
            ], 401);
        }

        // 2. Token validation
        $tokenRecord = PersonalAccessToken::findToken($authToken);

        if (!$tokenRecord) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => [
                    'token' => 'Authentication token is malformed.',
                ],
            ], 401);
        }

        // 3. Check token expiration
        if ($tokenRecord->expires_at && Carbon::parse($tokenRecord->expires_at)->isPast()) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => [
                    'token' => 'Your session has expired. Please log in again',
                ],
            ], 401);
        }

        // 4. Get user from token
        $user = $tokenRecord->tokenable;

        if (!$user) {
            return response()->json([
                'error' => 'Invalid Token',
                'message' => 'Токен не связан с действующим пользователем',
                'code' => 'TOKEN_NO_USER'
            ], 401);
        }

        // 5. Set user as authenticated
        auth()->setUser($user);

        // 6. Add user in request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // 7. Add token to user
        $user->withAccessToken($tokenRecord);


        // Next middleware
        return $next($request);
    }
}
