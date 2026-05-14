<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\InstitutePortal\AdminInstituteDashboardPortalController;
use App\Http\Controllers\Backend\InstitutePortal\InstituteDashboardPortalController;
use App\Http\Controllers\Backend\InstitutePortal\InstitutePortalController;
use App\Http\Controllers\Backend\SupportModule\CommonLogController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::middleware(['EnsureOtpSessionExists'])->group(function () {
    Route::get('verify-otp', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('verify-otp', [OtpController::class, 'verify'])->middleware('throttle:5,1')->name('otp.submit');
    Route::post('resend-otp', [OtpController::class, 'resend'])->name('otp.resend');
    Route::get('otp-cancel', [OtpController::class, 'cancel'])->name('otp.cancel');
});

Route::group(['middleware' => ['auth', 'check_last_activity']], function () {
    Route::prefix('adminpanel')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::group([
            'prefix' => 'common-log',
            'as' => 'common-log.'
        ], function () {
            Route::get('/', [CommonLogController::class, 'index'])->name('index');
            Route::get('get-common-log', [CommonLogController::class, 'getAjaxSupportModule'])->name('get-common-log');
            Route::get('/get-user-logs/{user_id}', [CommonLogController::class, 'getUserLogs'])->name('get-user-logs');
        });


      
    });
});


require __DIR__ . '/auth.php';
