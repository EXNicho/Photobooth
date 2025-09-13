<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

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
        // Register alias for admin-only middleware
        $this->app['router']->aliasMiddleware('admin', AdminOnly::class);
        // Force Intervention Image to use GD (avoid Imagick requirement)
        Config::set('image.driver', env('IMAGE_DRIVER', 'gd'));
        // Share storage link existence to views (for admin warning)
        $storageLinked = is_link(public_path('storage')) || File::exists(public_path('storage'));
        view()->share('storageLinked', $storageLinked);
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
