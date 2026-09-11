<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('catalog', function (Request $request) {
            $token = (string) $request->header('X-Service-Token', $request->ip());

            return Limit::perMinute(120)->by($token);
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            URL::forceRootUrl((string) config('app.url'));
        }
    }
}
