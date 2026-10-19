<?php

use App\Http\Controllers\Reviewer\DashboardController;
use App\Http\Controllers\Reviewer\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reviewer Routes — izin berbasis permission (`review.*`)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:reviewer'])
    ->prefix('reviewer')
    ->name('reviewer.')
    ->group(function () {

        Route::middleware('permission:review.view')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
            Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
        });

        Route::middleware('permission:review.accept')->group(function () {
            Route::post('/reviews/{review}/accept', [ReviewController::class, 'accept'])->name('reviews.accept');
        });

        Route::middleware('permission:review.decline')->group(function () {
            Route::post('/reviews/{review}/decline', [ReviewController::class, 'decline'])->name('reviews.decline');
        });

        Route::middleware('permission:review.submit')->group(function () {
            Route::post('/reviews/{review}/submit', [ReviewController::class, 'submit'])->name('reviews.submit');
        });
    });
