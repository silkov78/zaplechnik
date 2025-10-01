<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Custom api token exception for Sanctum token validation.
 *
 * Provides detailed error codes and messages for token authentication failures:
 * missing, invalid, or expired tokens.
 */
class ApiTokenException extends Exception
{
    public function __construct(
        private readonly string $tokenErrorCode,
        private readonly string $tokenErrorMessage,
    ) {
        parent::__construct('Unauthenticated.');
    }

    /**
     * Validate the authentication token from the request.
     *
     * @throws ApiTokenException If token is missing, invalid, or expired
     */
    public static function checkToken(Request $request): void
    {
        $token = $request->bearerToken();

        if (!$token) {
            throw new self('missing', 'Token is missing.');
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            throw new self(
                'invalid',
                'Authentication token is malformed.'
            );
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            throw new self(
                'expired',
                'Your access token has expired. Please log in again.'
            );
        }
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthenticated.',
            'errors' => [
                'token' => [
                    'code' => $this->tokenErrorCode,
                    'message' => $this->tokenErrorMessage,
                ],
            ],
        ], 401);
    }
}
