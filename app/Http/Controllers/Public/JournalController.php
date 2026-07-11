<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function index(): View
    {
        $journals = Journal::active()
            ->withCount('publishedArticles')
            ->with('editor')
            ->paginate(12);

        return view('public.journals.index', compact('journals'));
    }

    public function show(string $slug): View
    {
        $journal = Journal::where('slug', $slug)
            ->active()
            ->with(['editor', 'issues' => function ($q) {
                $q->published()->orderByDesc('year')->orderByDesc('volume')->orderByDesc('number');
            }])
            ->firstOrFail();

        return view('public.journals.show', compact('journal'));
    }
}
