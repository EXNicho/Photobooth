<?php

// Tambahan di method configureRateLimiting() dalam App\Providers\RouteServiceProvider
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

RateLimiter::for('photobooth', function (Request $request) {
    $key = optional($request->user())->id ?: $request->ip();
    return [
        Limit::perMinute(60)->by($key),
        Limit::perMinute(20)->by('checksum:'.$request->input('checksum')),
    ];
});

