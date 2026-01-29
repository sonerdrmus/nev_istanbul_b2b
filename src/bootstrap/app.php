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
        $middleware->trustProxies(at: '*');
        // Mağaza (sepet, ödeme vb.) için giriş yapmamış kullanıcılar müşteri paneli girişine yönlendirilir
        $middleware->redirectGuestsTo(fn () => url('/panel/login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
