<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::orderBy('name')->get()->loadCount('roles');
        $grouped = $permissions->groupBy(fn (Permission $p) => strtok($p->name, '.'));

        return view('admin.permissions.index', compact('permissions', 'grouped'));
    }

    public function create(): View
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.permissions.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255', 'regex:/^[a-z0-9\.\-\_]+$/',
                'unique:'.config('permission.table_names.permissions').',name',
            ],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:'.config('permission.table_names.roles').',name'],
        ]);

        $permission = Permission::create(['name' => $data['name'], 'guard_name' => 'web']);

        foreach ($data['roles'] ?? [] as $roleName) {
            Role::findByName($roleName, 'web')->givePermissionTo($permission);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission {$permission->name} berhasil dibuat.");
    }

    public function edit(Permission $permission): View
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        $grantedRoles = $permission->roles()->pluck('name')->all();

        return view('admin.permissions.edit', compact('permission', 'roles', 'grantedRoles'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255', 'regex:/^[a-z0-9\.\-\_]+$/',
                'unique:'.config('permission.table_names.permissions').',name,'.$permission->id,
            ],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:'.config('permission.table_names.roles').',name'],
        ]);

        $permission->update(['name' => $data['name']]);

        $grantedNames = $data['roles'] ?? [];
        foreach (Role::where('guard_name', 'web')->get() as $role) {
            if (in_array($role->name, $grantedNames)) {
                $role->givePermissionTo($permission);
            } else {
                $role->revokePermissionTo($permission);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission {$permission->name} berhasil diperbarui.");
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if ($permission->roles()->exists()) {
            return back()->with('error', 'Permission masih dipakai oleh role. Lepaskan dulu dari semua role.');
        }

        if (DB::table('model_has_permissions')->where('permission_id', $permission->id)->exists()) {
            return back()->with('error', 'Permission masih dipakai langsung oleh user.');
        }

        $permission->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', 'Permission berhasil dihapus.');
    }
}