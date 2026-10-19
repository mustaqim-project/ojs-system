<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role  = $request->get('role', '');
        $query = User::withTrashed()->with('roles')->latest();

        if ($role) {
            $query->where(function ($q) use ($role) {
                $q->where('role', $role)
                  ->orWhereHas('roles', fn ($r) => $r->where('name', $role));
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = $this->getAvailableRoles();

        $roleCounts = User::select('role', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->whereNotNull('role')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        return view('admin.users.index', compact('users', 'roles', 'role', 'roleCounts'));
    }

    public function create(): View
    {
        $roles = $this->getAvailableRoles();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $roles = $this->getAvailableRoles($request->input('role'));
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'password'    => ['required', Password::min(8)],
            'role'        => ['required', 'string', \Illuminate\Validation\Rule::in($roles)],
            'roles'       => ['nullable', 'array'],
            'roles.*'     => ['string', \Illuminate\Validation\Rule::in($roles)],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'phone'       => ['nullable', 'string', 'max:20'],
        ]);

        $institutionId = $request->institution_id;
        if (!$institutionId && $request->filled('affiliation')) {
            $institution = \App\Models\Institution::firstOrCreate(
                ['name' => $request->affiliation],
                [
                    'acronym' => $this->generateAcronym($request->affiliation),
                    'country_code' => 'ID',
                ]
            );
            $institutionId = $institution->id;
        }

        $roleToSave = $request->role;
        try {
            $user = User::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'password'       => Hash::make($request->password),
                'role'           => $roleToSave,
                'affiliation'    => $request->affiliation,
                'institution_id' => $institutionId,
                'phone'          => $request->phone,
                'is_active'      => true,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), '1265') || str_contains($e->getMessage(), '22001') || str_contains($e->getMessage(), 'Data truncated')) {
                $user = User::create([
                    'name'           => $request->name,
                    'email'          => $request->email,
                    'password'       => Hash::make($request->password),
                    'role'           => $this->mapToCoreEnumRole($request->role),
                    'affiliation'    => $request->affiliation,
                    'institution_id' => $institutionId,
                    'phone'          => $request->phone,
                    'is_active'      => true,
                ]);
            } else {
                throw $e;
            }
        }

        $allRoles = array_unique(array_filter(array_merge([$request->role], (array) $request->input('roles', []))));
        foreach ($allRoles as $roleName) {
            \Spatie\Permission\Models\Role::findOrCreate($roleName, 'web');
        }
        $user->syncRoles($allRoles);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user): View
    {
        $roles = $this->getAvailableRoles($user->role);
        $userRoles = $user->roles->pluck('name')->toArray();
        if (empty($userRoles) && $user->role) {
            $userRoles = [$user->role];
        }
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $roles = $this->getAvailableRoles($user->role, $request->input('role'));
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', "unique:users,email,{$user->id}"],
            'role'        => ['required', 'string', \Illuminate\Validation\Rule::in($roles)],
            'roles'       => ['nullable', 'array'],
            'roles.*'     => ['string', \Illuminate\Validation\Rule::in($roles)],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'is_active'   => ['boolean'],
        ]);

        $institutionId = $request->institution_id;
        if (!$institutionId && $request->filled('affiliation')) {
            $institution = \App\Models\Institution::firstOrCreate(
                ['name' => $request->affiliation],
                [
                    'acronym' => $this->generateAcronym($request->affiliation),
                    'country_code' => 'ID',
                ]
            );
            $institutionId = $institution->id;
        }

        $roleToSave = $request->role;
        try {
            $user->update([
                'name'           => $request->name,
                'email'          => $request->email,
                'role'           => $roleToSave,
                'affiliation'    => $request->affiliation,
                'institution_id' => $institutionId,
                'phone'          => $request->phone,
                'is_active'      => $request->is_active,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), '1265') || str_contains($e->getMessage(), '22001') || str_contains($e->getMessage(), 'Data truncated')) {
                $user->update([
                    'name'           => $request->name,
                    'email'          => $request->email,
                    'role'           => $this->mapToCoreEnumRole($request->role),
                    'affiliation'    => $request->affiliation,
                    'institution_id' => $institutionId,
                    'phone'          => $request->phone,
                    'is_active'      => $request->is_active,
                ]);
            } else {
                throw $e;
            }
        }

        $allRoles = array_unique(array_filter(array_merge([$request->role], (array) $request->input('roles', []))));
        foreach ($allRoles as $roleName) {
            \Spatie\Permission\Models\Role::findOrCreate($roleName, 'web');
        }
        $user->syncRoles($allRoles);

        if ($request->filled('password')) {
            $request->validate(['password' => [Password::min(8)]]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate!');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        $user->delete(); // Soft delete
        return back()->with('success', 'User berhasil dihapus!');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri!');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$status}!");
    }

    private function generateAcronym(string $name): ?string
    {
        $words = explode(' ', preg_replace('/[^a-zA-Z\s]/', '', $name));
        $acronym = '';
        foreach ($words as $word) {
            if (!empty($word) && !in_array(strtolower($word), ['of', 'in', 'and', 'dan', 'di', 'ke', 'the'])) {
                $acronym .= strtoupper($word[0]);
            }
        }
        return !empty($acronym) ? $acronym : null;
    }

    private function getAvailableRoles(...$extraRoles): array
    {
        $dbRoles = \Spatie\Permission\Models\Role::pluck('name')->toArray();
        $merged = array_merge(
            ['admin', 'editor', 'reviewer', 'author', 'reader'],
            $dbRoles,
            array_filter($extraRoles)
        );
        return array_values(array_unique($merged));
    }

    private function mapToCoreEnumRole(string $role): string
    {
        $coreEnum = ['admin', 'editor', 'reviewer', 'author', 'reader'];
        if (in_array($role, $coreEnum)) {
            return $role;
        }

        return match ($role) {
            'super-admin', 'system-admin' => 'admin',
            'journal-manager', 'managing-editor', 'section-editor', 'layout-editor', 'copy-editor', 'proofreader', 'publisher' => 'editor',
            'reviewer' => 'reviewer',
            'author' => 'author',
            default => 'reader',
        };
    }
}
