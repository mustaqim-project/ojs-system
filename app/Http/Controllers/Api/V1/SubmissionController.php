<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Article;
use App\Models\Journal;
use App\Services\SubmissionStateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::query()
            ->when($request->journal_id, fn($q, $id) => $q->where('journal_id', $id))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->search, fn($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->with('author', 'journal')
            ->paginate(20);

        return response()->json($articles);
    }

    public function show(Article $article)
    {
        return response()->json($article->load('author', 'journal', 'versions.files'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'journal_id' => 'required|exists:journals,id',
            'title' => 'required|string|max:500',
            'abstract' => 'required|string|min:150|max:5000',
            'keywords' => 'required|array|min:3|max:10',
            'keywords.*' => 'string|max:50',
            'language' => 'required|in:en,id',
            'section' => 'nullable|string|max:100',
        ]);

        $article = Article::create([
            ...$validated,
            'author_id' => Auth::id(),
            'status' => 'draft',
            'current_stage' => 'draft',
        ]);

        return response()->json($article, 201);
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:500',
            'abstract' => 'sometimes|string|min:150|max:5000',
            'keywords' => 'sometimes|array|min:3|max:10',
            'keywords.*' => 'string|max:50',
        ]);

        $article->update($validated);

        return response()->json($article);
    }

    public function submit(Request $request, Article $article)
    {
        $this->authorize('submit', $article);

        $stateMachine = new SubmissionStateMachine();
        $article = $stateMachine->transition($article, 'submitted');

        $article->update(['submitted_at' => now()]);

        // Notify editors
        $editors = $article->journal->users()
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['managing-editor', 'section-editor']))
            ->get();

        foreach ($editors as $editor) {
            $editor->notify(new \App\Notifications\ArticleSubmittedNotification($article));
        }

        return response()->json($article);
    }

    public function withdraw(Request $request, Article $article)
    {
        $this->authorize('withdraw', $article);

        $article->update([
            'status' => 'withdrawn',
            'current_stage' => 'withdrawn',
        ]);

        return response()->json(['message' => 'Article withdrawn successfully']);
    }
}
