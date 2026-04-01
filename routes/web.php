<?php

use App\Http\Controllers\Auth\ShowLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ImageGeneration\IndexImageGenerationController;
use App\Http\Controllers\ImageGeneration\StatusImageGenerationController;
use App\Http\Controllers\ImageGeneration\StoreImageGenerationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportErrorController;
use App\Http\Controllers\VideoGeneration\StoreVideoGenerationController;
use Illuminate\Support\Facades\Route;

// ── Authentication ────────────────────────────────────────────────────────────
Route::get('/login', ShowLoginController::class)->name('login');
Route::post('/login', LoginController::class)
    ->middleware('guest')
    ->name('login.store');
Route::post('/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');
Route::redirect('/', '/image-generator');

Route::get('/image-generator', IndexImageGenerationController::class)
    ->name('image-generator.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', ProfileController::class)
        ->name('profile');

    Route::post('/image-generator', StoreImageGenerationController::class)
        ->name('image-generator.store');

    Route::get('/image-generator/{id}/status', StatusImageGenerationController::class)
        ->name('image-generator.status')
        ->whereNumber('id');

    Route::post('/video-generator', StoreVideoGenerationController::class)
        ->name('video-generator.store');

    Route::get('/video-generator/{id}/status', StatusVideoGenerationController::class)
        ->name('video-generator.status')
        ->whereNumber('id');
});
Route::post('report-errors', ReportErrorController::class)->name('report-errors');
