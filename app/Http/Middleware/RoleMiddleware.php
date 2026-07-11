<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Middleware RBAC - cek role user sebelum akses route
     *
     * @param string|string[] $roles Satu atau beberapa role yang diizinkan
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        // Cek apakah user aktif
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }

        // Cek role
        if (!empty($roles) && !in_array($user->role, $roles)) {
            // Log akses yang tidak sah (opsional untuk audit)
            \Log::warning("Unauthorized access attempt", [
                'user_id' => $user->id,
                'role'    => $user->role,
                'url'     => $request->url(),
                'roles_required' => $roles,
            ]);

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
