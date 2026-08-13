<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\JournalController;
use App\Http\Controllers\Api\V1\SubmissionController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\FinanceController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\InstitutionController;

$registerRoutes = function () {
    // Public endpoints
    Route::get('/institutions', [InstitutionController::class, 'index']);
    Route::get('/journals', [JournalController::class, 'index']);
    Route::get('/journals/{journal}', [JournalController::class, 'show']);
    Route::get('/articles', [SubmissionController::class, 'index']);
    Route::get('/articles/{article}', [SubmissionController::class, 'show']);

    // Auth endpoints (public)
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Submissions
        Route::post('/submissions', [SubmissionController::class, 'store']);
        Route::put('/submissions/{article}', [SubmissionController::class, 'update']);
        Route::post('/submissions/{article}/submit', [SubmissionController::class, 'submit']);
        Route::post('/submissions/{article}/withdraw', [SubmissionController::class, 'withdraw']);

        // Reviews
        Route::get('/review-assignments', [ReviewController::class, 'index']);
        Route::post('/review-assignments/{assignment}/respond', [ReviewController::class, 'respond']);
        Route::post('/review-assignments/{assignment}/review', [ReviewController::class, 'submitReview']);

        // Finance
        Route::get('/invoices', [FinanceController::class, 'invoices']);
        Route::post('/invoices/{invoice}/payments', [FinanceController::class, 'uploadPayment']);
        Route::get('/invoices/{invoice}/receipt', [FinanceController::class, 'receipt']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

        // Reports
        Route::get('/reports/journals/{journal}/stats', [ReportController::class, 'journalStats']);
        Route::get('/reports/journals/{journal}/submissions', [ReportController::class, 'submissionTrend']);
        Route::get('/reports/journals/{journal}/reviews', [ReportController::class, 'reviewStats']);
    });
};

Route::prefix('v1')->group($registerRoutes);
Route::prefix('api/v1')->group($registerRoutes);
