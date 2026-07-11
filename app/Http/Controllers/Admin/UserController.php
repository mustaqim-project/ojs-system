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
        $query = User::withTrashed()->latest();

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = ['admin', 'editor', 'reviewer', 'author', 'reader'];

        return view('admin.users.index', compact('users', 'roles', 'role'));
    }

    public function create(): View
    {
        $roles = ['admin', 'editor', 'reviewer', 'author', 'reader'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'password'    => ['required', Password::min(8)],
            'role'        => ['required', 'in:admin,editor,reviewer,author,reader'],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:20'],
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'affiliation' => $request->affiliation,
            'phone'       => $request->phone,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user): View
    {
        $roles = ['admin', 'editor', 'reviewer', 'author', 'reader'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', "unique:users,email,{$user->id}"],
            'role'        => ['required', 'in:admin,editor,reviewer,author,reader'],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
        ]);

        $user->update($request->only('name', 'email', 'role', 'affiliation', 'phone', 'is_active'));

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
}
