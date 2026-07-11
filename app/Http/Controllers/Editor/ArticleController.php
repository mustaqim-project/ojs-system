<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Review;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService) {}

    public function index(Request $request): View
    {
        $status   = $request->get('status', '');
        $query    = Article::with(['journal', 'author', 'reviews'])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $articles = $query->paginate(15)->withQueryString();

        $statuses = [
            'submitted'          => 'Baru Masuk',
            'under_review'       => 'Sedang Review',
            'revision_required'  => 'Perlu Revisi',
            'accepted'           => 'Diterima',
            'rejected'           => 'Ditolak',
            'waiting_payment'    => 'Menunggu Bayar',
            'payment_uploaded'   => 'Bukti Diupload',
            'payment_verification' => 'Verifikasi Bayar',
            'paid'               => 'Lunas',
            'published'          => 'Terpublish',
        ];

        return view('editor.articles.index', compact('articles', 'statuses', 'status'));
    }

    public function show(Article $article): View
    {
        $article->load(['journal', 'author', 'reviews.reviewer', 'payment', 'issue']);
        $reviewers = User::byRole('reviewer')->active()->get();

        return view('editor.articles.show', compact('article', 'reviewers'));
    }

    public function assignReviewer(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'reviewer_id' => ['required', 'exists:users,id'],
        ]);

        $reviewer = User::findOrFail($request->reviewer_id);
        if (!$reviewer->isReviewer()) {
            return back()->with('error', 'User yang dipilih bukan reviewer.');
        }

        try {
            $this->articleService->assignReviewer($article, $request->reviewer_id);
            return back()->with('success', 'Reviewer berhasil diassign!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function makeDecision(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'decision'    => ['required', 'in:accept,reject,revision'],
            'editor_note' => ['nullable', 'string', 'max:2000'],
        ]);

        // Hanya bisa buat keputusan jika under_review atau revision_required
        if (!in_array($article->status, ['under_review', 'revision_required', 'submitted'])) {
            return back()->with('error', 'Artikel tidak dapat diproses pada status saat ini.');
        }

        try {
            $this->articleService->makeDecision(
                $article,
                $request->decision,
                $request->editor_note
            );

            $message = match ($request->decision) {
                'accept'   => 'Artikel diterima! Invoice pembayaran telah dibuat.',
                'reject'   => 'Artikel ditolak.',
                'revision' => 'Permintaan revisi telah dikirim ke author.',
            };

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
