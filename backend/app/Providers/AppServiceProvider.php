<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('strict', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many requests.',
                        'errors' => [
                            'rate-limit' => [
                                'code' => 'rate-limit',
                                'message' => 'Rate limit with 10 requests per minute is exceeded.',
                            ],
                        ],
                    ], 429);
                });
        });

        RateLimiter::for('medium', function (Request $request) {
            return Limit::perMinute(40)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many requests.',
                        'errors' => [
                            'rate-limit' => [
                                'code' => 'rate-limit',
                                'message' => 'Rate limit with 40 requests per minute is exceeded.',
                            ],
                        ],
                    ], 429);
                });
        });

        RateLimiter::for('lite', function (Request $request) {
            return Limit::perMinute(100)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many requests.',
                        'errors' => [
                            'rate-limit' => [
                                'code' => 'rate-limit',
                                'message' => 'Rate limit with 100 requests per minute is exceeded.',
                            ],
                        ],
                    ], 429);
                });
        });
    }
}
