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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
   ->withExceptions(function (Exceptions $exceptions): void {

    // Validation errors
    $exceptions->render(function (
        \Illuminate\Validation\ValidationException $e,
        $request
    ) {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    });

    // Authentication errors
    $exceptions->render(function (
        \Illuminate\Auth\AuthenticationException $e,
        $request
    ) {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }
    });

    // Authorization errors (RBAC)
    $exceptions->render(function (
        \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e,
        $request
    ) {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied',
            ], 403);
        }
    });

    // Fallback (any other error)
    $exceptions->render(function (
        \Throwable $e,
        $request
    ) {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => app()->environment('production')
                    ? 'Something went wrong'
                    : $e->getMessage(),
            ], 500);
        }
    });
})

    ->create();
