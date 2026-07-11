<?php

use App\Http\Controllers\Api\JournalApiController;
use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Middleware\OjsApiMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([OjsApiMiddleware::class])->prefix('api/v1')->group(function () {
    Route::get('/journals', [JournalApiController::class, 'index'])->name('api.v1.journals.index');
    Route::get('/journals/{id}', [JournalApiController::class, 'show'])->name('api.v1.journals.show');
    
    // Submissions kompatibel dengan schema OJS
    Route::get('/submissions', [ArticleApiController::class, 'index'])->name('api.v1.submissions.index');
    Route::get('/submissions/{id}', [ArticleApiController::class, 'show'])->name('api.v1.submissions.show');
});
