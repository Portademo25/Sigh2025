<?php


use App\Http\Middleware\UpdateUserLastSeen;
use App\Http\Middleware\CheckSessionId;
use App\Http\Middleware\CheckMaintenanceMode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
)
->withMiddleware(function ($middleware) {
        $middleware->web(append: [
        \App\Http\Middleware\UpdateUserLastSeen::class,
        \App\Http\Middleware\CheckSessionId::class,
        \App\Http\Middleware\CheckMaintenanceMode::class,
         \App\Http\Middleware\DynamicDatabaseConfig::class,// Tu middleware
    ]);

    // 2. Registra los de Spatie como ALIAS para usarlos en las rutas
    $middleware->alias([
        'role' => RoleMiddleware::class,
        'permission' => PermissionMiddleware::class,
        'role_or_permission' => RoleOrPermissionMiddleware::class,
    ]);


    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Aquí configuras excepciones, por ejemplo:
        $exceptions->reportable(function (Throwable $e) {
            // lógica para reportar
        });

        $exceptions->renderable(function (Throwable $e, $request) {
            // lógica para renderizar
        });
    })
    ->create();
