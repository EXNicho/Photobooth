<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\PhotoAdminController;

Route::middleware(['web', \App\Http\Middleware\AgentsPolicyMiddleware::class])->group(function () {
    Route::get('/', [GalleryController::class, 'home'])->name('home');
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
    Route::get('/p/{token}', [GalleryController::class, 'showByToken'])->name('photos.token');
    Route::get('/download/{photo}', [GalleryController::class, 'signedDownload'])->name('photos.download');
    Route::get('/download/stream/{photo}', [GalleryController::class, 'streamDownload'])->name('photos.download.stream')->middleware('signed');

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/photos', [PhotoAdminController::class, 'index'])->name('photos.index');
        Route::post('/photos/{photo}/retry', [PhotoAdminController::class, 'retry'])->name('photos.retry');
        Route::post('/photos/{photo}/regenerate-qr', [PhotoAdminController::class, 'regenerateQr'])->name('photos.regenerate');
    });
});

