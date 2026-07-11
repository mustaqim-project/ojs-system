<?php

use App\Http\Controllers\Reviewer\DashboardController;
use App\Http\Controllers\Reviewer\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reviewer Routes — hanya role: reviewer
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:reviewer'])
    ->prefix('reviewer')
    ->name('reviewer.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::post('/reviews/{review}/accept', [ReviewController::class, 'accept'])->name('reviews.accept');
        Route::post('/reviews/{review}/decline', [ReviewController::class, 'decline'])->name('reviews.decline');
        Route::post('/reviews/{review}/submit', [ReviewController::class, 'submit'])->name('reviews.submit');
    });
