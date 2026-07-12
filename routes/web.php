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
Route::get('/articles', [App\Http\Controllers\Public\ArticleController::class, 'index'])->name('public.articles.index');
Route::get('/articles/{slug}', [App\Http\Controllers\Public\ArticleController::class, 'show'])->name('public.articles.show');
Route::get('/articles/{slug}/download', [App\Http\Controllers\Public\ArticleController::class, 'download'])->name('public.articles.download');
Route::get('/articles/{slug}/citation/{format}', [App\Http\Controllers\Public\ArticleController::class, 'citation'])->name('public.articles.citation');

// Static Informational Pages
Route::get('/about', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.about');
Route::get('/editorial-team', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.editorial-team');
Route::get('/reviewer-board', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.reviewer-board');
Route::get('/author-guidelines', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.author-guidelines');
Route::get('/publication-ethics', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.ethics');
Route::get('/peer-review-process', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.peer-review');
Route::get('/focus-and-scope', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.focus-and-scope');
Route::get('/journal-policies', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.journal-policies');
Route::get('/indexing', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.indexing');
Route::get('/contact', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.contact');
Route::get('/privacy-policy', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.privacy-policy');
Route::get('/terms-conditions', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.terms-conditions');

// Information & Browse
Route::get('/announcements', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.announcements');
Route::get('/call-for-papers', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.call-for-papers');
Route::get('/current-issue', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.current-issue');
Route::get('/archive', [App\Http\Controllers\Public\PublicPageController::class, 'show'])->name('public.archive');

// OAI-PMH Endpoint
Route::get('/oai', \App\Http\Controllers\Public\OaiController::class)->name('public.oai');

// Notifications


Route::middleware([
    'auth',
    config('jetstream.auth_session', 'verified'), // Fallback to verified if config is null
])->group(function () {
    Route::get('/notifications/mark-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAll');
    Route::get('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'password'])->name('profile.password');
});

// Load route files per module
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/author.php';
require __DIR__ . '/editor.php';
require __DIR__ . '/reviewer.php';
require __DIR__ . '/api.php';
