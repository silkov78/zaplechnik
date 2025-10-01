<?php

use App\Http\Middleware\ForceJsonRequestHeader;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(ForceJsonRequestHeader::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'errors' => [
                        'token' => [
                            'code' => 'missing',
                            'message' => 'Token is missing.',
                        ],
                    ],
                ], 401);
            }

            $accessToken = PersonalAccessToken::findToken($token);
            if (!$accessToken) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'errors' => [
                        'token' => [
                            'code' => 'invalid',
                            'message' => 'Authentication token is malformed.',
                        ],
                    ],
                ], 401);
            }

            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'errors' => [
                        'token' => [
                            'code' => 'expired',
                            'message' => 'Your access token has expired. Please log in again.',
                        ],
                    ],
                ], 401);
            }
        });
    })->create();
