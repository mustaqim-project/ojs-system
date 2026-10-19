<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Backfill sekali-jalan: migrasikan kolom legacy `users.role`
 * menjadi role Spatie (guard web) sehingga otorisasi bisa berjalan
 * berbasis permission.
 *
 * Aman dijalankan berulang (idempotent):
 *  - Role & permission dibuat hanya jika belum ada.
 *  - assignRole() pada user legacy memakai guard (sync) tanpa duplikasi.
 */
return new class extends Migration
{
    /**
     * Role Spatie yang dijamin ada + permission minimalnya.
     */
    private const ROLE_PERMISSIONS = [
        'admin' => [
            'dashboard.view', 'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.update', 'permissions.delete',
            'journal.view', 'journal.create', 'journal.update', 'journal.delete',
            'issue.manage', 'submission.view', 'submission.update', 'publication.approve',
            'invoice.view', 'payment.verify', 'payment.reject',
            'system-settings.view', 'system-settings.update', 'cms.view', 'cms.update', 'cms.delete',
            'api.tokens.manage', 'reports.export', 'menus.view', 'menus.create', 'menus.update', 'menus.delete',
        ],
        'editor' => [
            'submission.view', 'screening.view', 'screening.decide',
            'reviewer.assign', 'reviewer.view', 'decision.make', 'decision.view',
            'publication.approve', 'volume.manage', 'issue.manage',
            'cms.view', 'cms.update', 'reports.view',
        ],
        'reviewer' => ['review.accept', 'review.decline', 'review.submit', 'review.view'],
        'author' => [
            'submission.create', 'submission.view', 'submission.update',
            'submission.delete', 'submission.submit', 'submission.withdraw',
        ],
        'reader' => [],
    ];

    private const LEGACY_TO_ROLE = [
        'admin' => 'admin',
        'editor' => 'editor',
        'reviewer' => 'reviewer',
        'author' => 'author',
        'reader' => 'reader',
    ];

    public function up(): void
    {
        $this->ensureRolesAndPermissions();

        // Assign role Spatie untuk semua user dengan kolom legacy `role`.
        $rows = DB::table('users')
            ->select('id', 'role')
            ->whereNotNull('role')
            ->get();

        $roleByName = Role::where('guard_name', 'web')->get()->keyBy('name');

        foreach ($rows as $row) {
            $roleName = self::LEGACY_TO_ROLE[$row->role] ?? null;
            if (!$roleName || !$roleByName->has($roleName)) {
                continue;
            }

            $userClass = config('auth.providers.users.model');
            $user = (new $userClass)->newQuery()->find($row->id);
            if ($user) {
                // syncRoles memakai guard default (web) — menghindari duplikasi
                // bila assignRole() dipanggil berulang.
                $user->syncRoles([$roleName]);
            }
        }
    }

    public function down(): void
    {
        // Tidak menghapus role/permission — one-way data migration.
    }

    private function ensureRolesAndPermissions(): void
    {
        // Pastikan permission init dibuat (aman jika RolePermissionSeeder belum jalan).
        $allPermissions = collect(self::ROLE_PERMISSIONS)->flatten()->unique()->values();

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissionNames);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};