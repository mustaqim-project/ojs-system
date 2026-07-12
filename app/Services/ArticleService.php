<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleService
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Submit artikel baru dari author
     */
    public function submit(array $data, int $authorId): Article
    {
        return DB::transaction(function () use ($data, $authorId) {
            // Upload manuscript file
            $manuscriptPath = $this->uploadFile(
                $data['manuscript_file'],
                'manuscripts'
            );

            // Upload cover letter jika ada
            $coverLetterPath = null;
            if (!empty($data['cover_letter'])) {
                $coverLetterPath = $this->uploadFile($data['cover_letter'], 'cover-letters');
            }

            $article = Article::create([
                'journal_id'      => $data['journal_id'],
                'author_id'       => $authorId,
                'title'           => $data['title'],
                'abstract'        => $data['abstract'],
                'keywords'        => $data['keywords'],
                'language'        => $data['language'] ?? 'id',
                'manuscript_file' => $manuscriptPath,
                'cover_letter'    => $coverLetterPath,
                'author_note'     => $data['author_note'] ?? null,
                'status'          => Article::STATUS_SUBMITTED,
                'submitted_at'    => now(),
            ]);

            return $article;
        });
    }

    /**
     * Author upload revisi
     */
    public function uploadRevision(Article $article, UploadedFile $file, ?string $note = null): Article
    {
        // Hapus file revisi lama jika ada
        if ($article->revision_file) {
            Storage::disk('public')->delete($article->revision_file);
        }

        $revisionPath = $this->uploadFile($file, 'revisions');

        $article->update([
            'revision_file' => $revisionPath,
            'author_note'   => $note,
            'status'        => Article::STATUS_UNDER_REVIEW, // Kembali ke review setelah revisi
        ]);

        return $article->fresh();
    }

    /**
     * Editor assign reviewer ke artikel
     */
    public function assignReviewer(Article $article, int $reviewerId): Review
    {
        return DB::transaction(function () use ($article, $reviewerId) {
            // Cek reviewer sudah diassign sebelumnya
            $existing = Review::where('article_id', $article->id)
                ->where('reviewer_id', $reviewerId)
                ->whereNotIn('status', ['declined'])
                ->first();

            if ($existing) {
                throw new \Exception('Reviewer sudah diassign untuk artikel ini.');
            }

            $dueDays = (int) Setting::get('review_due_days', 14);

            $review = Review::create([
                'article_id'  => $article->id,
                'reviewer_id' => $reviewerId,
                'status'      => 'pending',
                'due_date'    => now()->addDays($dueDays),
            ]);

            // Update status artikel
            $article->update(['status' => Article::STATUS_UNDER_REVIEW]);

            return $review;
        });
    }

    /**
     * Editor buat keputusan: accept / reject / revision
     */
    public function makeDecision(Article $article, string $decision, ?string $editorNote = null): Article
    {
        return DB::transaction(function () use ($article, $decision, $editorNote) {
            $newStatus = match ($decision) {
                'accept'   => Article::STATUS_ACCEPTED,
                'reject'   => Article::STATUS_REJECTED,
                'revision' => Article::STATUS_REVISION_REQUIRED,
                default    => throw new \InvalidArgumentException("Decision tidak valid: {$decision}"),
            };

            $updateData = [
                'status'      => $newStatus,
                'editor_note' => $editorNote,
            ];

            if ($decision === 'accept') {
                $updateData['accepted_at'] = now();
                // Generate invoice otomatis saat accepted
                $this->paymentService->generateInvoice($article);
                $updateData['status'] = Article::STATUS_WAITING_PAYMENT;
            }

            $article->update($updateData);

            return $article->fresh();
        });
    }

    /**
     * Admin publish artikel
     * KRITIS: Cek payment sudah verified sebelum publish
     */
    public function publish(Article $article, int $issueId): Article
    {
        // SECURITY CHECK: Pastikan sudah bayar & terverifikasi
        if (!$article->canBePublished()) {
            throw new \Exception('Artikel tidak bisa dipublish. Pastikan pembayaran sudah diverifikasi.');
        }

        $article->update([
            'status'       => Article::STATUS_PUBLISHED,
            'issue_id'     => $issueId,
            'published_at' => now(),
        ]);

        return $article->fresh();
    }

    /**
     * Upload file dengan validasi
     */
    private function uploadFile(UploadedFile $file, string $folder): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/' . $folder), $filename);
        return 'uploads/' . $folder . '/' . $filename;
    }
}
