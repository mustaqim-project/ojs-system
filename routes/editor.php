<?php

use App\Http\Controllers\Editor\ArticleController;
use App\Http\Controllers\Editor\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Editor Routes — izin berbasis permission (`submission.*`, dsb.)
|--------------------------------------------------------------------------
| Middleware `role:editor,admin` (backward-compat) + `permission:` per aksi.
| Admin legacy tetap lolos via Gate::before.
*/

Route::middleware(['auth', 'role:editor,admin'])
    ->prefix('editor')
    ->name('editor.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('permission:decision.view')
            ->name('dashboard');

        // Articles management
        Route::middleware('permission:submission.view')->group(function () {
            Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
            Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
        });

        Route::middleware('permission:reviewer.assign')->group(function () {
            Route::post('/articles/{article}/assign-reviewer', [ArticleController::class, 'assignReviewer'])->name('articles.assign-reviewer');
        });

        Route::middleware('permission:decision.make')->group(function () {
            Route::post('/articles/{article}/decision', [ArticleController::class, 'makeDecision'])->name('articles.decision');
        });
    });
