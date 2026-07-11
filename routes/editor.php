<?php

use App\Http\Controllers\Editor\ArticleController;
use App\Http\Controllers\Editor\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Editor Routes — hanya role: editor
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:editor,admin'])
    ->prefix('editor')
    ->name('editor.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Articles management
        Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::post('/articles/{article}/assign-reviewer', [ArticleController::class, 'assignReviewer'])->name('articles.assign-reviewer');
        Route::post('/articles/{article}/decision', [ArticleController::class, 'makeDecision'])->name('articles.decision');
    });
