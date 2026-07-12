<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Navigation;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        $navigations = Navigation::whereNull('parent_id')->orderBy('order')->with('children')->get();
        $parents = Navigation::whereNull('parent_id')->get();
        return view('admin.navigations.index', compact('navigations', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
        ]);

        Navigation::create($request->all());

        return redirect()->route('navigations.index')->with('success', 'Navigation created successfully.');
    }

    public function update(Request $request, Navigation $navigation)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
        ]);

        $navigation->update($request->all());

        return redirect()->route('navigations.index')->with('success', 'Navigation updated successfully.');
    }

    public function destroy(Navigation $navigation)
    {
        $navigation->delete();
        return redirect()->route('navigations.index')->with('success', 'Navigation deleted successfully.');
    }
}
