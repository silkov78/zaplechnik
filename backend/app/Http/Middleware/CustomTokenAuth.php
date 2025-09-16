<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CustomTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Проверяем наличие заголовка Authorization
        $authToken = $request->bearerToken();

        if (!$authToken) {
            return response()->json([
                'error' => 'Authentication Required',
                'message' => 'Отсутствует токен авторизации. Необходимо передать заголовок Authorization: Bearer {token}',
                'code' => 'TOKEN_MISSING'
            ], 401);
        }

        // 2. Ищем токен в БД
        $tokenRecord = PersonalAccessToken::findToken($authToken);

        if (!$tokenRecord) {
            return response()->json([
                'error' => 'Invalid Token',
                'message' => 'Предоставленный токен недействителен или не существует',
                'code' => 'TOKEN_INVALID'
            ], 401);
        }

        // 3. Проверяем срок действия токена
        if ($tokenRecord->expires_at && Carbon::parse($tokenRecord->expires_at)->isPast()) {
            return response()->json([
                'error' => 'Token Expired',
                'message' => 'Срок действия токена истёк. Необходимо получить новый токен',
                'code' => 'TOKEN_EXPIRED',
                'expired_at' => $tokenRecord->expires_at
            ], 401);
        }

        // 4. Получаем пользователя из токена
        $user = $tokenRecord->tokenable;

        if (!$user) {
            return response()->json([
                'error' => 'Invalid Token',
                'message' => 'Токен не связан с действующим пользователем',
                'code' => 'TOKEN_NO_USER'
            ], 401);
        }

        // 5. Устанавливаем пользователя как аутентифицированного
        auth()->setUser($user);

        // 6. Устанавливаем resolver для request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // 7. Добавляем токен к пользователю (как это делает Sanctum)
        $user->withAccessToken($tokenRecord);


        // Токен валидный, продолжаем выполнение
        return $next($request);
    }
}
