<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IssueController;
use App\Http\Controllers\Admin\JournalController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SitePageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ApiIntegrationController;
use App\Http\Controllers\Admin\ExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes - permission-driven (Admin Panel gate)
|--------------------------------------------------------------------------
| Middleware `admin.panel` memastikan user adalah bagian panel admin
| (role admin/super-admin ATAU memiliki minimal satu permission area admin).
| Middleware `permission:` melakukan enforce per resource/action.
*/

Route::middleware(['auth', 'admin.panel'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('permission:dashboard.view')
            ->name('dashboard');

        // Users
        Route::middleware('permission:users.view')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        });
        Route::middleware('permission:users.create')->group(function () {
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
        });
        Route::middleware('permission:users.update')->group(function () {
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        });
        Route::middleware('permission:users.delete')->group(function () {
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Roles (RBAC management via Admin Panel)
        Route::middleware('permission:roles.view')->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        });
        Route::middleware('permission:roles.create')->group(function () {
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        });
        Route::middleware('permission:roles.update')->group(function () {
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        });
        Route::middleware('permission:roles.delete')->group(function () {
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // Permissions (RBAC management via Admin Panel)
        Route::middleware('permission:permissions.view')->group(function () {
            Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
            Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        });
        Route::middleware('permission:permissions.create')->group(function () {
            Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
            Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        });
        Route::middleware('permission:permissions.update')->group(function () {
            Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        });
        Route::middleware('permission:permissions.delete')->group(function () {
            Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        });

        // Navigations (dinamis: header publik + sidebar permission-driven)
        Route::middleware('permission:menus.view')->group(function () {
            Route::get('/navigations', [NavigationController::class, 'index'])->name('navigations.index');
            Route::get('/navigations/{navigation}/edit', [NavigationController::class, 'edit'])->name('navigations.edit');
        });
        Route::middleware('permission:menus.create')->group(function () {
            Route::post('/navigations', [NavigationController::class, 'store'])->name('navigations.store');
        });
        Route::middleware('permission:menus.update')->group(function () {
            Route::put('/navigations/{navigation}', [NavigationController::class, 'update'])->name('navigations.update');
            Route::patch('/navigations/{navigation}/toggle-active', [NavigationController::class, 'toggleActive'])->name('navigations.toggle-active');
        });
        Route::middleware('permission:menus.delete')->group(function () {
            Route::delete('/navigations/{navigation}', [NavigationController::class, 'destroy'])->name('navigations.destroy');
        });

        // Journals
        Route::middleware('permission:journal.view')->group(function () {
            Route::get('/journals', [JournalController::class, 'index'])->name('journals.index');
            Route::get('/journals/{journal}/edit', [JournalController::class, 'edit'])->name('journals.edit');
        });
        Route::middleware('permission:journal.create')->group(function () {
            Route::get('/journals/create', [JournalController::class, 'create'])->name('journals.create');
            Route::post('/journals', [JournalController::class, 'store'])->name('journals.store');
        });
        Route::middleware('permission:journal.update')->group(function () {
            Route::put('/journals/{journal}', [JournalController::class, 'update'])->name('journals.update');
        });
        Route::middleware('permission:journal.delete')->group(function () {
            Route::delete('/journals/{journal}', [JournalController::class, 'destroy'])->name('journals.destroy');
        });

        // Issues
        Route::middleware('permission:issue.manage')->group(function () {
            Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');
            Route::get('/issues/create', [IssueController::class, 'create'])->name('issues.create');
            Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');
            Route::get('/issues/{issue}/edit', [IssueController::class, 'edit'])->name('issues.edit');
            Route::put('/issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
            Route::delete('/issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');
            Route::patch('/issues/{issue}/publish', [IssueController::class, 'publish'])->name('issues.publish');
        });

        // Articles
        Route::middleware('permission:submission.view')->group(function () {
            Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
            Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
        });
        Route::middleware('permission:submission.update')->group(function () {
            Route::post('/articles/{article}/metadata', [ArticleController::class, 'updateMetadata'])->name('articles.update-metadata');
        });
        Route::middleware('permission:publication.approve')->group(function () {
            Route::post('/articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
        });

        // Payments
        Route::middleware('permission:invoice.view')->group(function () {
            Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        });
        Route::middleware('permission:payment.verify')->group(function () {
            Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        });
        Route::middleware('permission:payment.reject')->group(function () {
            Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        });

        // Settings
        Route::middleware('permission:system-settings.view')->group(function () {
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        });
        Route::middleware('permission:system-settings.update')->group(function () {
            Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        });

        // API Integrations Manager
        Route::middleware('permission:api.tokens.manage')->group(function () {
            Route::get('/integrations', [ApiIntegrationController::class, 'index'])->name('integrations.index');
            Route::get('/integrations/{provider}', [ApiIntegrationController::class, 'show'])->name('integrations.show');
            Route::put('/integrations/{provider}', [ApiIntegrationController::class, 'update'])->name('integrations.update');
            Route::post('/integrations/{provider}/test', [ApiIntegrationController::class, 'test'])->name('integrations.test');
        });

        // XML Exports (OJS 3.4 PKP compatible)
        Route::middleware('permission:reports.export')->group(function () {
            Route::get('/export/article/{article}', [ExportController::class, 'exportArticle'])->name('export.article');
            Route::get('/export/issue/{issue}', [ExportController::class, 'exportIssue'])->name('export.issue');
        });

        // Site Pages (Admin-managed public content)
        Route::middleware('permission:cms.view')->group(function () {
            Route::get('/pages', [SitePageController::class, 'index'])->name('pages.index');
            Route::get('/pages/{slug}/edit', [SitePageController::class, 'edit'])->name('pages.edit');
        });
        Route::middleware('permission:cms.update')->group(function () {
            Route::put('/pages/{slug}', [SitePageController::class, 'update'])->name('pages.update');
        });
        Route::middleware('permission:cms.delete')->group(function () {
            Route::delete('/pages/{slug}/reset', [SitePageController::class, 'resetToDefault'])->name('pages.reset');
        });
    });
