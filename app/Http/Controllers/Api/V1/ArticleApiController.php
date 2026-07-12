<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleApiController extends Controller
{
    /**
     * Tampilkan list artikel published.
     * Mendukung filter: journal_id, issue_id, keyword
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Article::published()->with(['journal', 'author', 'issue']);

        if ($request->has('journal_id')) {
            $query->where('journal_id', $request->integer('journal_id'));
        }

        if ($request->has('issue_id')) {
            $query->where('issue_id', $request->integer('issue_id'));
        }

        if ($request->has('keyword')) {
            $query->where('keywords', 'LIKE', '%' . $request->string('keyword') . '%');
        }

        $articles = $query->latest('published_at')->paginate(15);
        return ArticleResource::collection($articles);
    }

    /**
     * Detail satu artikel.
     */
    public function show(int $id): ArticleResource
    {
        $article = Article::published()->with(['journal', 'author', 'issue'])->findOrFail($id);
        return new ArticleResource($article);
    }
}
