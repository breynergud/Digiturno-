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
        $middleware->alias([
            'coordinador.inactividad' => \App\Http\Middleware\InactividadCoordinador::class,
            'asesor.inactividad'      => \App\Http\Middleware\InactividadAsesor::class,
            'digiturno.guest'         => \App\Http\Middleware\RedirectIfAuthenticatedDigiturno::class,
            'asesor.session'          => \App\Http\Middleware\StartAsesorSession::class,
            'coordinador.session'     => \App\Http\Middleware\StartCoordinadorSession::class,
        ]);

        // Prioridad: nuestros middlewares de cookie de sesión deben ejecutarse
        // ANTES del StartSession de Laravel para que el nombre de cookie
        // ya esté configurado cuando la sesión se inicia.
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \App\Http\Middleware\StartAsesorSession::class,
            \App\Http\Middleware\StartCoordinadorSession::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
