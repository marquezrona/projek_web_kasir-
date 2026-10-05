<?php

// File: bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Import class middleware Anda
use App\Http\Middleware\Admin;
use App\Http\Middleware\CekRole;
use App\Http\Middleware\LogAktivitas;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Mendaftarkan Alias Middleware (Untuk Route Khusus)
        $middleware->alias([
            'admin'         => Admin::class,
            'role'          => CekRole::class,
            'log.aktivitas' => LogAktivitas::class,
        ]);

        // 2. Jika ingin dijadikan Middleware Global (Berjalan di SELURUH request):
        // $middleware->append(LogAktivitas::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpException $exception, Request $request) {
            if ($exception->getStatusCode() !== 419 || ! ($exception->getPrevious() instanceof TokenMismatchException)) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.',
                ], 419);
            }

            return redirect()
                ->route('login')
                ->with('status', 'Sesi Anda telah kedaluwarsa. Silakan masuk kembali.');
        });
    })->create();