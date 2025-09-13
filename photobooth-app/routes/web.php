<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\PhotoAdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as CsrfMiddleware;

Route::middleware(['web', \App\Http\Middleware\AgentsPolicyMiddleware::class])->group(function () {
    Route::get('/', [GalleryController::class, 'home'])->name('home');
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
    Route::get('/p/{token}', [GalleryController::class, 'showByToken'])->name('photos.token');
    Route::get('/download/{photo}', [GalleryController::class, 'signedDownload'])->name('photos.download');
    Route::get('/download/stream/{photo}', [GalleryController::class, 'streamDownload'])->name('photos.download.stream')->middleware('signed');
    Route::get('/media/{photo}/{variant?}', [GalleryController::class, 'media'])->where('variant','thumb|full')->name('photos.media');

    // Static pages
    Route::view('/about', 'static.about')->name('about');
    Route::get('/contact', function () {
        return view('static.contact');
    })->name('contact');
    Route::post('/contact', function (Request $request) {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:160'],
            'message' => ['required','string','max:2000'],
        ]);
        return back()->with('status', 'Terima kasih, pesan Anda sudah kami terima.');
    })->name('contact.send');

    // Auth routes (basic email/password)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform')->withoutMiddleware([CsrfMiddleware::class]);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
    Route::get('/forgot', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot', [AuthController::class, 'sendForgotPassword'])->name('password.email');

    Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/photos', [PhotoAdminController::class, 'index'])->name('photos.index');
        Route::get('/photos/create', [PhotoAdminController::class, 'create'])->name('photos.create');
        Route::post('/photos/upload', [PhotoAdminController::class, 'upload'])->name('photos.upload');
        Route::post('/photos/import-url', [PhotoAdminController::class, 'importUrl'])->name('photos.import');
        Route::post('/photos/{photo}/retry', [PhotoAdminController::class, 'retry'])->name('photos.retry');
        Route::post('/photos/{photo}/regenerate-qr', [PhotoAdminController::class, 'regenerateQr'])->name('photos.regenerate');
        Route::post('/photos/{photo}/approve', [PhotoAdminController::class, 'approve'])->name('photos.approve');
        Route::post('/photos/{photo}/reject', [PhotoAdminController::class, 'reject'])->name('photos.reject');
        Route::delete('/photos/{photo}', [PhotoAdminController::class, 'destroy'])->name('photos.destroy');
        Route::post('/photos/{photo}/featured', [PhotoAdminController::class, 'markFeatured'])->name('photos.featured');

        // Events & QR
        Route::get('/events', [PhotoAdminController::class, 'events'])->name('events');
        Route::post('/events/qr', [PhotoAdminController::class, 'generateEventQr'])->name('events.qr');
        Route::post('/events/delete', [PhotoAdminController::class, 'deleteEvent'])->name('events.delete');

        // Settings - Google Drive
        \App\Http\Controllers\Admin\SettingsController::class;
        Route::get('/settings/drive', [\App\Http\Controllers\Admin\SettingsController::class, 'drive'])->name('settings.drive');
        Route::post('/settings/drive', [\App\Http\Controllers\Admin\SettingsController::class, 'saveDrive'])->name('settings.drive.save');
        Route::post('/settings/drive/sync', [\App\Http\Controllers\Admin\SettingsController::class, 'syncNow'])->name('settings.drive.sync');
        Route::post('/settings/drive/credentials', [\App\Http\Controllers\Admin\SettingsController::class, 'uploadCredentials'])->name('settings.drive.credentials');
    });
});
