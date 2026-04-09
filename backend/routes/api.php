<?php

use App\Http\Controllers\Api\V1\AgencyPortalController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BoqRequestController;
use App\Http\Controllers\Api\V1\DeveloperPortalController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ReferenceController;
use App\Http\Controllers\Api\V1\SubcontractorPortalController;
use App\Http\Controllers\Api\V1\WizardController;
use App\Http\Controllers\Api\V1\WorkerDirectoryController;
use App\Http\Controllers\Api\V1\WorkerPortalController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::prefix('auth')->group(function () {
        Route::post('send-otp', [AuthController::class, 'sendOtp'])
            ->middleware('throttle:otp');
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    });

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth (authenticated)
        Route::prefix('auth')->group(function () {
            Route::post('select-role', [AuthController::class, 'selectRole']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('2fa/verify', [AuthController::class, 'verifyTwoFactor'])
                ->middleware('role:admin,coordinator');
        });

        // Profile
        Route::get('me', [AuthController::class, 'me']);

        // Worker Directory (all authenticated users)
        Route::get('workers/directory', [WorkerDirectoryController::class, 'index']);

        // Developer routes
        Route::prefix('developer')->middleware('role:developer')->group(function () {
            Route::get('profile', [DeveloperPortalController::class, 'profile']);
            Route::put('profile', [DeveloperPortalController::class, 'updateProfile']);
            Route::post('projects', [DeveloperPortalController::class, 'submitProject']);
            Route::get('projects', [DeveloperPortalController::class, 'projects']);
            Route::get('projects/{id}', [DeveloperPortalController::class, 'projectDetail']);
            Route::get('boq-requests', [DeveloperPortalController::class, 'boqRequests']);
        });

        // Reference data (all authenticated)
        Route::get('reference/{type}', [ReferenceController::class, 'index']);

        // Worker routes
        Route::prefix('worker')->middleware('role:worker')->group(function () {
            Route::get('profile', [WorkerPortalController::class, 'profile']);
            Route::put('profile', [WorkerPortalController::class, 'updateProfile']);
            Route::get('assignments', [WorkerPortalController::class, 'assignments']);
            Route::get('team', [WorkerPortalController::class, 'team']);
            Route::get('training', [WorkerPortalController::class, 'training']);
            Route::get('training/{resultId}/certificate', [WorkerPortalController::class, 'downloadCertificate']);
            Route::get('documents', [WorkerPortalController::class, 'documents']);
            Route::post('documents', [WorkerPortalController::class, 'uploadDocument']);
            Route::get('payments', [WorkerPortalController::class, 'payments']);
            Route::put('availability', [WorkerPortalController::class, 'updateAvailability']);
        });

        // Subcontractor routes
        Route::prefix('subcontractor')->middleware('role:subcontractor')->group(function () {
            Route::get('profile', [SubcontractorPortalController::class, 'profile']);
            Route::put('profile', [SubcontractorPortalController::class, 'updateProfile']);
            Route::get('assignments', [SubcontractorPortalController::class, 'assignments']);
            Route::get('rating', [SubcontractorPortalController::class, 'rating']);
            Route::get('documents', [SubcontractorPortalController::class, 'documents']);
            Route::put('availability', [SubcontractorPortalController::class, 'updateAvailability']);
            Route::put('capacity', [SubcontractorPortalController::class, 'updateCapacity']);
        });

        // Agency routes
        Route::prefix('agency')->middleware('role:agency')->group(function () {
            Route::get('profile', [AgencyPortalController::class, 'profile']);
            Route::put('profile', [AgencyPortalController::class, 'updateProfile']);
            Route::get('workers', [AgencyPortalController::class, 'workers']);
            Route::get('training', [AgencyPortalController::class, 'trainingResults']);
            Route::get('billing', [AgencyPortalController::class, 'billing']);
            Route::get('quality', [AgencyPortalController::class, 'qualityMetrics']);
        });

        // Wizard routes (role-specific wizard endpoints)
        Route::prefix('wizards')->group(function () {
            Route::post('worker', [WizardController::class, 'submitWorker']);
            Route::post('developer', [WizardController::class, 'submitDeveloper']);
            Route::post('subcontractor', [WizardController::class, 'submitSubcontractor']);
            Route::post('agency', [WizardController::class, 'submitAgency']);
            Route::put('{type}/draft', [WizardController::class, 'saveDraft']);
            Route::get('{type}/draft', [WizardController::class, 'loadDraft']);
        });

        // BOQ routes (all roles can request)
        Route::prefix('boq-requests')->group(function () {
            Route::post('/', [BoqRequestController::class, 'store']);
            Route::get('/', [BoqRequestController::class, 'index']);
            Route::get('{id}', [BoqRequestController::class, 'show']);
            Route::get('{id}/items', [BoqRequestController::class, 'items']);
            Route::get('{id}/export', [BoqRequestController::class, 'export']);
        });

        // Notification routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('mark-all-read', [NotificationController::class, 'markAllRead']);
        });
    });
});
