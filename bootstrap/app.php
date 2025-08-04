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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // Add Cloudinary configuration middleware early in the stack
        $middleware->prependToGroup('web', \App\Http\Middleware\EnsureCloudinaryConfiguration::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
