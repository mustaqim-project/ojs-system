<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware gerbang panel admin.
 *
 * Mengizinkan akses bila user memenuhi SALAH SATU dari:
 *   1. Memiliki role Spatie: admin / super-admin / system-admin, ATAU kolom legacy role = 'admin'
 *   2. Memiliki minimal satu permission yang termasuk area panel admin
 *      (permission-driven: role custom yang diberi permission admin bisa masuk).
 */
class AdminPanelMiddleware
{
    /**
     * Permissions yang menandakan akses ke area panel admin.
     * Role apapun yang memegang salah satunya dianggap anggota panel admin.
     */
    public const PANEL_PERMISSIONS = [
        'dashboard.view',
        'system-settings.view',
        'journal.view',
        'users.view',
        'roles.view',
        'permissions.view',
        'menus.view',
        'invoice.view',
        'cms.view',
        'reports.view',
        'audit.view',
        'api.tokens.manage',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }

        $hasAdminRole = $user->hasRole(RoleMiddleware::ADMIN_ROLES)
            || ($user->role ?? null) === 'admin';

        $hasPanelPermission = $user->hasAnyPermission(self::PANEL_PERMISSIONS);

        if (!$hasAdminRole && !$hasPanelPermission) {
            \Log::warning("Admin panel access denied", [
                'user_id' => $user->id,
                'role'    => $user->role,
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'url'     => $request->url(),
            ]);

            abort(403, 'Anda tidak memiliki akses ke panel administrasi.');
        }

        return $next($request);
    }
}