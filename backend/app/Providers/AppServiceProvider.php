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
        $this->configureRateLimiters();
    }

    /**
     * Configure rate limiters for the application.
     */
    protected function configureRateLimiters(): void
    {
        $limits = [
            'strict' => 10,
            'medium' => 40,
            'lite' => 100,
        ];

        foreach ($limits as $limitName => $maxAttempts) {
            RateLimiter::for($limitName, function (Request $request) use ($maxAttempts) {
                return Limit::perMinute($maxAttempts)
                    ->by($request->ip())
                    ->response(function () use ($maxAttempts) {
                        return response()->json([
                            'message' => 'Too many requests.',
                            'errors' => [
                                'rate-limit' => [
                                    'code' => 'rate-limit',
                                    'message' => "Rate limit with {$maxAttempts} requests per minute is exceeded.",
                                ],
                            ],
                        ], 429);
                    });
            });
        }
    }
}
