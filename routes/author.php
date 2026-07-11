<?php

use App\Http\Controllers\Author\ArticleController;
use App\Http\Controllers\Author\DashboardController;
use App\Http\Controllers\Author\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Author Routes — hanya role: author
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:author'])
    ->prefix('author')
    ->name('author.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Articles
        Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

        // Revision
        Route::get('/articles/{article}/revision', [ArticleController::class, 'uploadRevision'])->name('articles.revision');
        Route::post('/articles/{article}/revision', [ArticleController::class, 'storeRevision'])->name('articles.revision.store');

        // Payment
        Route::get('/articles/{article}/payment', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/articles/{article}/payment/upload', [PaymentController::class, 'uploadProof'])->name('payments.upload');

        // ORCID Sync
        Route::post('/orcid/sync', [\App\Http\Controllers\Author\OrcidProfileController::class, 'sync'])->name('orcid.sync');
    });
