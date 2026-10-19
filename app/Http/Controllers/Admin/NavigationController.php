<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Navigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NavigationController extends Controller
{
    public function index(): View
    {
        $navigations = Navigation::whereNull('parent_id')
            ->orderBy('order')
            ->with(['children' => fn ($q) => $q->orderBy('order')])
            ->get();

        $parents = Navigation::whereNull('parent_id')->orderBy('order')->get();

        return view('admin.navigations.index', compact('navigations', 'parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateNavigation($request);
        Navigation::create($data);

        return redirect()->route('admin.navigations.index')
            ->with('success', 'Navigasi berhasil ditambahkan.');
    }

    public function edit(Navigation $navigation): View
    {
        $navigation->load('parent');
        $parents = Navigation::whereNull('parent_id')->orderBy('order')->get();

        return view('admin.navigations.edit', compact('navigation', 'parents'));
    }

    public function update(Request $request, Navigation $navigation): RedirectResponse
    {
        $data = $this->validateNavigation($request, $navigation);
        $navigation->update($data);

        return redirect()->route('admin.navigations.index')
            ->with('success', "Navigasi {$navigation->title} berhasil diperbarui.");
    }

    public function destroy(Navigation $navigation): RedirectResponse
    {
        // parent FK cascade akan menghapus children
        $navigation->delete();

        return redirect()->route('admin.navigations.index')
            ->with('success', 'Navigasi berhasil dihapus.');
    }

    public function toggleActive(Navigation $navigation): RedirectResponse
    {
        $navigation->update(['is_active' => ! $navigation->is_active]);
        $state = $navigation->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Navigasi {$navigation->title} berhasil {$state}.");
    }

    private function validateNavigation(Request $request, ?Navigation $navigation = null): array
    {
        return $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'url'        => ['nullable', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'permission' => ['nullable', 'string', 'max:255'],
            'icon'       => ['nullable', 'string', 'max:100'],
            'parent_id'  => ['nullable', 'integer', 'exists:navigations,id'],
            'order'      => ['nullable', 'integer', 'min:0'],
            'location'   => ['required', 'in:header,sidebar'],
            'is_active'  => ['boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'location'  => $request->input('location', 'header'),
            'order'     => $request->input('order', 0),
        ];
    }
}
