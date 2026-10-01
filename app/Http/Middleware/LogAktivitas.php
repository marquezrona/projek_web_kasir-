<?php

// File: app/Http/Middleware/LogAktivitas.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogAktivitas
{
    /**
     * Berjalan SEBELUM respon dikirim ke klien
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Method Terminate: Berjalan SETELAH respon dikirim ke klien
     */
    public function terminate($request, $response)
    {
        Log::info('Request selesai diproses', [
            'url'    => $request->fullUrl(),
            'status' => $response->getStatusCode(),
        ]);
    }
}
