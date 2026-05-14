<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\HomeProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('/declarant-management-portal-login', [AuthenticatedSessionController::class, 'create'])
        ->name('declarant-management-portal-login');

    Route::post('/declarant-management-portal-login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [ForgotPasswordController::class, 'resetWithOtp'])
        ->name('password.store');

    Route::get('verify-password-otp', [ForgotPasswordController::class, 'showVerifyForm'])
        ->name('password.otp.verify');

    Route::post('reset-password-otp', [ForgotPasswordController::class, 'verifyResetOtp'])
        ->name('password.otp.update');

    Route::post('password/resend-otp', [ForgotPasswordController::class, 'resendOtp'])
        ->name('password.otp.resend');
});

Route::group(['middleware' => ['auth', 'check_last_activity']], function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::group(['middleware' => ['auth']], function () {

    Route::prefix('adminpanel')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Roles routes
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('roles-list', [RoleController::class, 'index'])->name('roles-list');
            Route::post('store', [RoleController::class, 'store'])->name('store');
            Route::get('create', [RoleController::class, 'create'])->name('create');
            Route::get('edit/{id}', [RoleController::class, 'edit'])->name('edit');
            Route::put('update', [RoleController::class, 'update'])->name('update');
        });

        // Home project routes
        Route::group([
            'prefix' => 'home-projects',
            'as' => 'home-project.',
        ], function () {
            Route::get('/', [HomeProjectController::class, 'index'])->name('index');
            Route::post('/store', [HomeProjectController::class, 'store'])->name('store');
            Route::get('/create', [HomeProjectController::class, 'create'])->name('create');
            Route::get('/edit/{id}', [HomeProjectController::class, 'edit'])->name('edit');
            Route::put('/update', [HomeProjectController::class, 'update'])->name('update');
            Route::get('/get-home-projects', [HomeProjectController::class, 'getHomeProject'])->name('get-home-projects');
            Route::delete('/delete/{id}', [HomeProjectController::class, 'destroy'])->name('delete');
        });

    });
});
