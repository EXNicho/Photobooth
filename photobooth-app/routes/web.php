<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\PhotoAdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

Route::middleware(['web', \App\Http\Middleware\AgentsPolicyMiddleware::class])->group(function () {
    Route::get('/', [GalleryController::class, 'home'])->name('home');
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
    Route::get('/p/{token}', [GalleryController::class, 'showByToken'])->name('photos.token');
    Route::get('/download/{photo}', [GalleryController::class, 'signedDownload'])->name('photos.download');
    Route::get('/download/stream/{photo}', [GalleryController::class, 'streamDownload'])->name('photos.download.stream')->middleware('signed');

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
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
    Route::get('/forgot', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot', [AuthController::class, 'sendForgotPassword'])->name('password.email');

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/photos', [PhotoAdminController::class, 'index'])->name('photos.index');
        Route::post('/photos/{photo}/retry', [PhotoAdminController::class, 'retry'])->name('photos.retry');
        Route::post('/photos/{photo}/regenerate-qr', [PhotoAdminController::class, 'regenerateQr'])->name('photos.regenerate');
    });
});
