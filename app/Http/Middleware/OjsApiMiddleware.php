<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OjsApiMiddleware
{
    /**
     * Handle incoming request secure API token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah API Token dikonfigurasi di Settings
        $configuredToken = Setting::get('system_api_token');

        if (!$configuredToken) {
            // Jika token sistem belum diset, ijinkan read-only requests secara publik
            // atau jika butuh keamanan ketat, block. Kita ijinkan demi kesederhanaan interoperabilitas.
            return $next($request);
        }

        $requestToken = $request->header('X-OJS-API-Token') ?? $request->query('api_token');

        if (!$requestToken || $requestToken !== $configuredToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing X-OJS-API-Token header.'
            ], 401);
        }

        return $next($request);
    }
}
