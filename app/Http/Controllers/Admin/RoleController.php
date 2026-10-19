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
    public function index(): View
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        $userCounts = DB::table('model_has_roles')
            ->where('model_type', (new User)->getMorphClass())
            ->pluck('role_id')
            ->countBy();

        return view('admin.roles.index', compact('roles', 'userCounts'));
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
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-\_]+$/'],
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
        if (in_array($role->name, ['super-admin', 'system-admin'])) {
            return back()->with('error', 'Role sistem tidak bisa diubah namanya.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-\_]+$/'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:'.config('permission.table_names.permissions').',name'],
        ]);

        $role->update(['name' => $data['name']]);
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