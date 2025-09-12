<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PhotoIngestController;

Route::middleware(['auth:sanctum','throttle:photobooth'])->group(function () {
    Route::post('/photos', [PhotoIngestController::class, 'store']);
});

