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
        $article->load(['journal', 'author', 'reviews.reviewer', 'payment', 'issue', 'galleys']);
        $reviewers = User::byRole('reviewer')->active()->get();

        return view('editor.articles.show', compact('article', 'reviewers'));
    }

    public function uploadGalley(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'galley_file' => ['required', 'file', 'mimes:pdf', 'max:25600'],
            'label'       => ['nullable', 'string', 'max:50'],
        ], [
            'galley_file.required' => 'Berkas Galley PDF wajib diunggah.',
            'galley_file.mimes'    => 'Berkas Galley harus berupa file PDF (.pdf).',
            'galley_file.max'      => 'Ukuran berkas Galley maksimal 25 MB.',
        ]);

        try {
            $file = $request->file('galley_file');
            $filename = 'galley_' . $article->id . '_' . \Illuminate\Support\Str::random(8) . '.pdf';

            $targetDir = public_path('uploads/galleys');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $relativePath = 'uploads/galleys/' . $filename;

            \App\Models\ArticleGalley::create([
                'article_id'   => $article->id,
                'label'        => $request->filled('label') ? $request->label : 'PDF',
                'file_path'    => $relativePath,
                'mime_type'    => 'application/pdf',
                'size_bytes'   => file_exists(public_path($relativePath)) ? filesize(public_path($relativePath)) : null,
                'is_published' => true,
            ]);

            return back()->with('success', 'Berkas Galley / Layout PDF berhasil diunggah!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengunggah berkas Galley: ' . $e->getMessage());
        }
    }

    public function deleteGalley(Article $article, \App\Models\ArticleGalley $galley): RedirectResponse
    {
        if ($galley->article_id !== $article->id) {
            abort(403);
        }

        try {
            if ($galley->file_path && file_exists(public_path($galley->file_path))) {
                @unlink(public_path($galley->file_path));
            }
            $galley->delete();
            return back()->with('success', 'Berkas Galley berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus berkas Galley: ' . $e->getMessage());
        }
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
            $review = $this->articleService->assignReviewer($article, $request->reviewer_id);
            
            // Notify Reviewer
            try {
                $reviewer->notify(new \App\Notifications\ReviewAssignedNotification($article, $review));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed sending ReviewAssignedNotification: ' . $e->getMessage());
            }

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

            // Notify Author with dedicated academic notification
            $article->refresh();
            try {
                if ($article->author) {
                    if ($request->decision === 'accept') {
                        $article->author->notify(new \App\Notifications\ArticleAcceptedNotification($article, $request->editor_note));
                    } elseif ($request->decision === 'reject') {
                        $article->author->notify(new \App\Notifications\ArticleRejectedNotification($article, $request->editor_note));
                    } elseif ($request->decision === 'revision') {
                        $article->author->notify(new \App\Notifications\RevisionRequiredNotification($article, $request->editor_note));
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed sending decision notification: ' . $e->getMessage());
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
