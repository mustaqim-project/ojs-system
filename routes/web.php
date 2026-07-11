<?php

use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\JournalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (tidak perlu login)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/search', [HomeController::class, 'search'])->name('public.search');

// Journals
Route::get('/journals', [JournalController::class, 'index'])->name('public.journals.index');
Route::get('/journals/{slug}', [JournalController::class, 'show'])->name('public.journals.show');

// Articles
Route::get('/articles', [ArticleController::class, 'index'])->name('public.articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('public.articles.show');

// OAI-PMH Endpoint
Route::get('/oai', \App\Http\Controllers\Public\OaiController::class)->name('public.oai');

// Load route files per module
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/author.php';
require __DIR__ . '/editor.php';
require __DIR__ . '/reviewer.php';
require __DIR__ . '/api.php';
