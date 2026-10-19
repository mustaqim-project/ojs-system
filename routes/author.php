<?php

use App\Http\Controllers\Author\ArticleController;
use App\Http\Controllers\Author\DashboardController;
use App\Http\Controllers\Author\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Author Routes — izin berbasis permission (`submission.*`)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:author'])
    ->prefix('author')
    ->name('author.')
    ->group(function () {

        // Submission — DAFTAR SEBELUM /articles/{article} agar tidak tertimpa
        Route::middleware('permission:submission.create')->group(function () {
            Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
            Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
        });

        Route::middleware('permission:submission.view')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
            Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
            Route::get('/articles/{article}/payment', [PaymentController::class, 'show'])->name('payments.show');
            Route::post('/articles/{article}/payment/upload', [PaymentController::class, 'uploadProof'])->name('payments.upload');
        });

        // Revision
        Route::middleware('permission:submission.update')->group(function () {
            Route::get('/articles/{article}/revision', [ArticleController::class, 'uploadRevision'])->name('articles.revision');
            Route::post('/articles/{article}/revision', [ArticleController::class, 'storeRevision'])->name('articles.revision.store');
        });

        // ORCID Sync
        Route::middleware('permission:submission.view')->group(function () {
            Route::post('/orcid/sync', [\App\Http\Controllers\Author\OrcidProfileController::class, 'sync'])->name('orcid.sync');
        });
    });
