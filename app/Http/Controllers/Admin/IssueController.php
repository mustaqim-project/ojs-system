<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(): View
    {
        $issues = Issue::with('journal')->latest()->paginate(15);
        return view('admin.issues.index', compact('issues'));
    }

    public function create(): View
    {
        $journals = Journal::active()->get();
        return view('admin.issues.create', compact('journals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'journal_id'     => ['required', 'exists:journals,id'],
            'title'          => ['required', 'string', 'max:255'],
            'volume'         => ['required', 'integer', 'min:1'],
            'number'         => ['required', 'integer', 'min:1'],
            'year'           => ['required', 'integer', 'min:2000', 'max:2100'],
            'description'    => ['nullable', 'string'],
            'published_date' => ['nullable', 'date'],
            'status'         => ['required', 'in:draft,published,scheduled'],
        ]);

        Issue::create($validated);
        return redirect()->route('admin.issues.index')->with('success', 'Issue berhasil ditambahkan!');
    }

    public function publish(Issue $issue): RedirectResponse
    {
        $issue->update(['status' => 'published', 'published_date' => now()]);
        return back()->with('success', "Issue {$issue->display_title} berhasil dipublish!");
    }
}
