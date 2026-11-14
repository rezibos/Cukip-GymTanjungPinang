<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestForgery(except: [
            'payments/midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (QueryException $exception, Request $request) {
            $sqlState = (string) ($exception->errorInfo[0] ?? '');
            $driverErrorCode = (int) ($exception->errorInfo[1] ?? $exception->getCode());
            $connectionErrorCodes = [1042, 1043, 1044, 1045, 2002, 2003, 2005, 2006, 2013];

            $isConnectionError = str_starts_with($sqlState, '08')
                || in_array($driverErrorCode, $connectionErrorCodes, true);

            if (! $isConnectionError) {
                return null;
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Layanan data sedang tidak tersedia. Silakan coba lagi beberapa saat lagi.',
                    'code' => 'DATABASE_UNAVAILABLE',
                ], 503, ['Retry-After' => '30']);
            }

            return response()->view('errors.database-unavailable', status: 503, headers: [
                'Retry-After' => '30',
            ]);
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
