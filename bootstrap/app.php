<?php

// Ustawienia PHP dla importu plików
set_time_limit(180);
ini_set('max_execution_time', 180);
ini_set('memory_limit', '256M');
ini_set('max_input_time', 180);
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
