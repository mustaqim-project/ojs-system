<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IssueController;
use App\Http\Controllers\Admin\JournalController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SitePageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ApiIntegrationController;
use App\Http\Controllers\Admin\ExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes — hanya role: admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // Journals
        Route::get('/journals', [JournalController::class, 'index'])->name('journals.index');
        Route::get('/journals/create', [JournalController::class, 'create'])->name('journals.create');
        Route::post('/journals', [JournalController::class, 'store'])->name('journals.store');
        Route::get('/journals/{journal}/edit', [JournalController::class, 'edit'])->name('journals.edit');
        Route::put('/journals/{journal}', [JournalController::class, 'update'])->name('journals.update');

        // Issues
        Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');
        Route::get('/issues/create', [IssueController::class, 'create'])->name('issues.create');
        Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');
        Route::patch('/issues/{issue}/publish', [IssueController::class, 'publish'])->name('issues.publish');

        // Articles
        Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::post('/articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
        Route::post('/articles/{article}/metadata', [ArticleController::class, 'updateMetadata'])->name('articles.update-metadata');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // API Integrations Manager
        Route::get('/integrations', [ApiIntegrationController::class, 'index'])->name('integrations.index');
        Route::get('/integrations/{provider}', [ApiIntegrationController::class, 'show'])->name('integrations.show');
        Route::put('/integrations/{provider}', [ApiIntegrationController::class, 'update'])->name('integrations.update');
        Route::post('/integrations/{provider}/test', [ApiIntegrationController::class, 'test'])->name('integrations.test');

        // XML Exports (OJS 3.4 PKP compatible)
        Route::get('/export/article/{article}', [ExportController::class, 'exportArticle'])->name('export.article');
        Route::get('/export/issue/{issue}', [ExportController::class, 'exportIssue'])->name('export.issue');

        // Site Pages (Admin-managed public content)
        Route::get('/pages', [SitePageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{slug}/edit', [SitePageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{slug}', [SitePageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{slug}/reset', [SitePageController::class, 'resetToDefault'])->name('pages.reset');
    });
