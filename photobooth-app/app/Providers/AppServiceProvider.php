<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

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
        // Rate limiter for photobooth ingest
        RateLimiter::for('photobooth', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();
            return [
                Limit::perMinute(60)->by($key),
                Limit::perMinute(20)->by('checksum:'.$request->input('checksum')),
            ];
        });

        // Use Tailwind pagination templates for consistent UI
        Paginator::useTailwind();
    }
}
