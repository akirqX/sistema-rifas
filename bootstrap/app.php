<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. Configuração para o CSRF (já estava certa)
        $middleware->validateCsrfTokens(except: [
            'webhook'
        ]);

        // 2. REGISTRO DOS APELIDOS DE MIDDLEWARE (A PARTE QUE FALTAVA)
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            // 'outro_apelido' => \App\Http\Middleware\OutroMiddleware::class, // Exemplo futuro
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
