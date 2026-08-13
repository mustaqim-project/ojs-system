<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $sort = request('sort', 'latest');
        $q = request('q', '');
        $category = request('category', '');
        $yearFrom = request('year_from', '');
        $yearTo = request('year_to', '');
        
        $query = Article::published()->with(['journal', 'author', 'issue']);
        
        // Search Query (q)
        if (!empty($q)) {
            $query->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('abstract', 'like', "%{$q}%")
                      ->orWhere('doi', 'like', "%{$q}%")
                      ->orWhereHas('author', function ($authorQuery) use ($q) {
                          $authorQuery->where('name', 'like', "%{$q}%");
                      });
            });
        }
        
        // Category/Section filter
        if (!empty($category)) {
            $query->where('section', $category);
        }
        
        // Year filters
        if (!empty($yearFrom)) {
            $query->whereYear('published_at', '>=', $yearFrom);
        }
        if (!empty($yearTo)) {
            $query->whereYear('published_at', '<=', $yearTo);
        }
        
        // Sorting logic
        if ($sort === 'oldest') {
            $query->oldest('published_at');
        } elseif ($sort === 'views') {
            $query->orderByDesc('views_count');
        } elseif ($sort === 'citations' || $sort === 'downloads') {
            $query->orderByDesc('downloads_count');
        } else {
            $query->latest('published_at');
        }

        $articles = $query->paginate(15)->withQueryString();

        return view('public.articles.index', compact('articles'));
    }

    public function show(string $slug): View
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->with(['journal', 'author', 'issue'])
            ->firstOrFail();

        $article->increment('views_count');

        // Artikel terkait (jurnal yang sama)
        $related = Article::published()
            ->where('journal_id', $article->journal_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('public.articles.show', compact('article', 'related'));
    }

    public function download(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $article->increment('downloads_count');
            
        // For demonstration, returning a mock PDF stream
        $content = "Mock PDF Content for Article: " . $article->title . "\n\nIn a production environment, this would serve the actual PDF file.";
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $article->slug . '.pdf"');
    }

    public function citation(string $slug, string $format)
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->with('author')
            ->firstOrFail();
            
        $authorName = $article->author ? $article->author->name : 'Unknown Author';
        $year = $article->published_at ? $article->published_at->format('Y') : date('Y');
        $journal = $article->journal ? $article->journal->name : config('app.name', 'OJS');
        
        $citation = "";
        switch (strtolower($format)) {
            case 'apa':
                $citation = "{$authorName}. ({$year}). {$article->title}. {$journal}.";
                break;
            case 'bibtex':
                $citation = "@article{{$article->slug},\n  title={{$article->title}},\n  author={{$authorName}},\n  journal={{$journal}},\n  year={{$year}}\n}";
                break;
            case 'ris':
                $citation = "TY  - JOUR\nT1  - {$article->title}\nAU  - {$authorName}\nJO  - {$journal}\nPY  - {$year}\nER  - ";
                break;
            default:
                $citation = "{$authorName} ({$year}). \"{$article->title}\". {$journal}.";
        }
        
        return response($citation)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $article->slug . '-' . $format . '.txt"');
    }
}
