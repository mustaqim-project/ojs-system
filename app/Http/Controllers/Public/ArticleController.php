<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
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

    public function download(string $slug, Request $request)
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $article->increment('downloads_count');

        $inline = $request->query('mode') === 'inline';
        $disposition = $inline ? 'inline' : 'attachment';

        // 1. Cek Berkas Galley PDF resmi hasil setting/layout Editor
        $galleyId = $request->query('galley');
        $galley = null;
        if ($galleyId) {
            $galley = $article->galleys()->find($galleyId);
        }
        if (!$galley) {
            $galley = $article->galleys()->where('is_published', true)->latest()->first() 
                ?: $article->galleys()->latest()->first();
        }

        if ($galley && $galley->file_path && file_exists(public_path($galley->file_path))) {
            return response()->file(public_path($galley->file_path), [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => $disposition . '; filename="' . $article->slug . '.pdf"',
            ]);
        }

        // 2. Fallback jika ada file naskah revisi
        if ($article->revision_file && file_exists(public_path($article->revision_file))) {
            $ext = strtolower(pathinfo($article->revision_file, PATHINFO_EXTENSION));
            $mime = $ext === 'pdf' ? 'application/pdf' : (mime_content_type(public_path($article->revision_file)) ?: 'application/octet-stream');
            return response()->file(public_path($article->revision_file), [
                'Content-Type'        => $mime,
                'Content-Disposition' => $disposition . '; filename="' . $article->slug . '.' . $ext . '"',
            ]);
        }

        // 3. Fallback jika ada file naskah awal
        if ($article->manuscript_file && file_exists(public_path($article->manuscript_file))) {
            $ext = strtolower(pathinfo($article->manuscript_file, PATHINFO_EXTENSION));
            $mime = $ext === 'pdf' ? 'application/pdf' : (mime_content_type(public_path($article->manuscript_file)) ?: 'application/octet-stream');
            return response()->file(public_path($article->manuscript_file), [
                'Content-Type'        => $mime,
                'Content-Disposition' => $disposition . '; filename="' . $article->slug . '.' . $ext . '"',
            ]);
        }

        // 4. Fallback minimal PDF jika belum ada berkas fisik
        $content = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Contents 4 0 R/Resources<<>>>>endobj\n4 0 obj<</Length 85>>stream\nBT /F1 14 Tf 50 720 Td (" . addcslashes($article->title, "()\n") . ") Tj ET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000056 00000 n \n0000000111 00000 n \n0000000212 00000 n \ntrailer<</Size 5/Root 1 0 R>>\nstartxref\n347\n%%EOF";
        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $article->slug . '.pdf"',
        ]);
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
