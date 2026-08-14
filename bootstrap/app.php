<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withEvents(discover: false)
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'userakses' => App\Http\Middleware\UserAkses::class,
            'fiturakses' => App\Http\Middleware\FiturMiddleware::class,
            'apikey' => \App\Http\Middleware\CheckApiKey::class, 
        ]);

        // $middleware->validateCsrfTokens(except: [
        //     'api/data-absen'
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
