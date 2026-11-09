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

    public function edit(Issue $issue): View
    {
        $journals = Journal::active()->get();
        return view('admin.issues.edit', compact('issue', 'journals'));
    }

    public function update(Request $request, Issue $issue): RedirectResponse
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

        $issue->update($validated);
        return redirect()->route('admin.issues.index')->with('success', 'Issue berhasil diperbarui!');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $issue->delete();
        return redirect()->route('admin.issues.index')->with('success', 'Issue berhasil dihapus!');
    }

    public function publish(Issue $issue): RedirectResponse
    {
        $issue->update(['status' => 'published', 'published_date' => now()]);

        // Publish and notify authors of articles in this issue
        $articles = $issue->articles;
        if ($articles->isEmpty()) {
            $articles = \App\Models\Article::where('issue_id', $issue->id)->get();
        }

        foreach ($articles as $article) {
            if ($article->status !== \App\Models\Article::STATUS_PUBLISHED) {
                $article->update([
                    'status'       => \App\Models\Article::STATUS_PUBLISHED,
                    'issue_id'     => $issue->id,
                    'published_at' => now(),
                ]);

                try {
                    $article->loadMissing(['author', 'journal', 'issue']);
                    if ($article->author) {
                        $article->author->notify(new \App\Notifications\ArticlePublishedNotification($article));
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Failed sending ArticlePublishedNotification for issue ' . $issue->id . ': ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', "Issue {$issue->display_title} berhasil dipublish!");
    }
}
