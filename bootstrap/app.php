<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void
    {

        $middleware->alias([
            'auth.server' => \App\Http\Middleware\ServerAuthenticate::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        $middleware->redirectGuestsTo(function () {
            return route('maniaplanet.redirect');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void
    {
     /*   $exceptions->render(function (AuthenticationException $e, $request)
        {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        });
*/
        //$exceptions->shouldRenderJsonWhen(fn() => false);
    })->create();
