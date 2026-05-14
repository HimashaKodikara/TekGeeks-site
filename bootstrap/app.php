<?php

use App\Http\Middleware\EnsureOtpSessionExists;
use App\Http\Middleware\RequireApiKey;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\VerifyHmacSignature;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\CheckLastActivity;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\ValidateFormInputMiddleware;
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 🔐 IMPORTANT FIX
        $middleware->redirectGuestsTo(fn () => route('declarant-management-portal-login'));

        // run SetLocale on every web request
        $middleware->web(append: [
            SetLocale::class,
            SanitizeInput::class,
            ValidateFormInputMiddleware::class,
            SecurityHeaders::class,
        ]);

        // Register aliases used like: 'permission:...', 'role:...'
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'api.key' => RequireApiKey::class,
            'EnsureOtpSessionExists' => EnsureOtpSessionExists::class,
            'verify.hmac' => VerifyHmacSignature::class,
            'check_last_activity' => CheckLastActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
