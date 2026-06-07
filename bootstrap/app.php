<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ── Role middleware alias ──────────────────────────────
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\ActiveSecurityScanner::class);

        // ── Exclude EMR receiver API from CSRF protection ─────
        // These routes receive machine-to-machine HTTP requests
        // from the middleware pipeline, not browser form submissions
        $middleware->validateCsrfTokens(except: [
            'api/emr/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();