<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        $userCounts = DB::table('model_has_roles')
            ->where('model_type', (new User)->getMorphClass())
            ->pluck('role_id')
            ->countBy();

        $legacyCounts = User::select('role', DB::raw('count(*) as count'))
            ->whereNotNull('role')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $totalUsers = User::count();
        $totalPermissions = Permission::count();

        // Granular permissions grouped by clean readable categories
        $permissionsByCategory = Permission::orderBy('name')->get()
            ->groupBy(function (Permission $p) {
                $prefix = strtok($p->name, '.');
                return match($prefix) {
                    'submission', 'screening' => 'Pengajuan & Naskah (Submissions)',
                    'review', 'reviewer' => 'Peninjauan Sejawat (Peer Review)',
                    'decision', 'publication', 'volume', 'issue', 'doi' => 'Keputusan Redaksi & Terbitan',
                    'copyediting', 'layout', 'proofreading' => 'Produksi & Tata Letak',
                    'invoice', 'payment', 'waiver', 'receipt', 'refund' => 'Keuangan & APC',
                    'users', 'roles', 'permissions' => 'Pengguna & Hak Akses',
                    'system-settings', 'cms', 'api', 'audit', 'notifications', 'menus' => 'Pengaturan & Sistem',
                    default => ucfirst($prefix),
                };
            });

        // Members with their roles
        $members = User::latest()->paginate(15, ['*'], 'members_page')->withQueryString();

        return view('admin.roles.index', compact(
            'roles',
            'userCounts',
            'legacyCounts',
            'totalUsers',
            'totalPermissions',
            'permissionsByCategory',
            'members'
        ));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(fn (Permission $p) => strtok($p->name, '.'));

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-\_]+$/', 'unique:'.config('permission.table_names.roles').',name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:'.config('permission.table_names.permissions').',name'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role {$role->name} berhasil dibuat.");
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(fn (Permission $p) => strtok($p->name, '.'));
        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, ['super-admin', 'system-admin']) && $request->input('name') !== $role->name) {
            return back()->with('error', 'Nama role sistem tidak bisa diubah.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-\_]+$/', 'unique:'.config('permission.table_names.roles').',name,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:'.config('permission.table_names.permissions').',name'],
        ]);

        if (!in_array($role->name, ['super-admin', 'system-admin'])) {
            $role->update(['name' => $data['name']]);
        }
        $role->syncPermissions($data['permissions'] ?? []);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role {$role->name} berhasil diperbarui.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['admin', 'super-admin', 'system-admin', 'editor', 'author', 'reviewer'])) {
            return back()->with('error', 'Role inti tidak bisa dihapus.');
        }

        if (DB::table('model_has_roles')->where('role_id', $role->id)->exists()) {
            return back()->with('error', 'Role masih digunakan oleh user. Pindahkan user terlebih dahulu.');
        }

        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', 'Role berhasil dihapus.');
    }
}