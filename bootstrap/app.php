<?php

// File: bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
        //
    })->create();