<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REVISION_REQUIRED = 'revision_required';
    public const STATUS_WAITING_PAYMENT = 'waiting_payment';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_PAYMENT_UPLOADED = 'payment_uploaded';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'journal_id',
        'author_id',
        'assigned_editor_id',
        'title',
        'abstract',
        'status',
        'current_stage',
        'tracking_code',
        'language',
        'section',
        'keywords',
        'author_note',
        'funding_statement',
        'conflict_of_interest',
        'ethics_statement',
        'acknowledgement',
        'license',
        'submitted_at',
        'published_at',
        'accepted_at',
        'issue_id',
        'slug',
        'manuscript_file',
        'revision_file',
        'cover_letter',
        'doi',
        'pages_start',
        'pages_end',
        'views_count',
        'downloads_count',
    ];

    protected $casts = [
        'submitted_at'    => 'datetime',
        'accepted_at'     => 'datetime',
        'published_at'    => 'datetime',
        'keywords'        => 'array',
        'pages_start'     => 'integer',
        'pages_end'       => 'integer',
        'views_count'     => 'integer',
        'downloads_count' => 'integer',
    ];

    // ========== RELATIONSHIPS ==========

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id')->withTrashed();
    }

    public function assignedEditor()
    {
        return $this->belongsTo(User::class, 'assigned_editor_id')->withTrashed();
    }

    public function versions()
    {
        return $this->hasMany(SubmissionVersion::class);
    }

    public function files()
    {
        return $this->hasManyThrough(SubmissionFile::class, SubmissionVersion::class);
    }

    public function reviewRounds()
    {
        return $this->hasMany(ReviewRound::class);
    }

    public function editorialDecisions()
    {
        return $this->hasMany(EditorialDecision::class);
    }

    public function productionTasks()
    {
        return $this->hasMany(ProductionTask::class);
    }

    public function galleys()
    {
        return $this->hasMany(ArticleGalley::class);
    }

    public function primaryGalley()
    {
        return $this->galleys()->where('is_published', true)->latest()->first() 
            ?: $this->galleys()->latest()->first();
    }

    public function doi()
    {
        return $this->hasOne(ArticleDoi::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'submission_id');
    }

    public function payment()
    {
        return $this->hasOneThrough(Payment::class, Invoice::class, 'submission_id', 'invoice_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ========== BUSINESS LOGIC ==========

    public function canBePublished(): bool
    {
        if (!$this->invoice) {
            return true;
        }

        return in_array($this->invoice->status, ['paid', 'waived']);
    }

    public function needsPayment(): bool
    {
        if ($this->status === self::STATUS_WAITING_PAYMENT) {
            return true;
        }

        if ($this->invoice && in_array($this->invoice->status, ['unpaid', 'pending', 'waiting_payment'])) {
            return true;
        }

        return false;
    }

    /**
     * Dapatkan daftar editor & admin terkait untuk notifikasi editorial
     */
    public function getEditorialRecipients(): \Illuminate\Support\Collection
    {
        $recipients = collect();

        // 1. Assigned Editor
        if ($this->assignedEditor && $this->assignedEditor->is_active) {
            $recipients->push($this->assignedEditor);
        }

        // 2. Journal Editor
        if ($this->journal && $this->journal->editor && $this->journal->editor->is_active) {
            $recipients->push($this->journal->editor);
        }

        // 3. Fallback jika belum ada editor khusus: semua admin & editor aktif
        if ($recipients->isEmpty()) {
            $editors = User::where(function ($q) {
                $q->whereIn('role', ['admin', 'editor', 'managing-editor', 'section-editor'])
                  ->orWhereHas('roles', fn ($sq) => $sq->whereIn('name', ['admin', 'super-admin', 'editor', 'managing-editor', 'section-editor']));
            })->active()->get();
            $recipients = $recipients->merge($editors);
        }

        return $recipients->unique('id');
    }

    // ========== LOCAL SCOPES ==========

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to filter articles by a specific author ID.
     * Fixes the BadMethodCallException for Article::forAuthor().
     */
    public function scopeForAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    // ========== ACCESSORS / MUTATORS ==========

    public function getKeywordsArrayAttribute()
    {
        return is_array($this->keywords) ? $this->keywords : [];
    }

    public function getPagesRangeAttribute(): ?string
    {
        if (! $this->pages_start) {
            return null;
        }

        if ($this->pages_end && $this->pages_end > $this->pages_start) {
            return "{$this->pages_start}–{$this->pages_end}";
        }

        return (string) $this->pages_start;
    }

    public function getTotalPagesAttribute(): ?int
    {
        if ($this->pages_start) {
            if ($this->pages_end && $this->pages_end >= $this->pages_start) {
                return ($this->pages_end - $this->pages_start) + 1;
            }

            return 1;
        }

        $pdfPath = $this->resolvePdfFilePath();

        return $pdfPath ? self::countPdfPages($pdfPath) : null;
    }

    /**
     * Cari path absolut file PDF artikel (prioritas: galley terbit -> galley terbaru -> revisi -> manuskrip).
     */
    public function resolvePdfFilePath(): ?string
    {
        $galleys = $this->relationLoaded('galleys')
            ? $this->galleys
            : $this->galleys()->latest()->get();

        $galley = $galleys->where('is_published', true)->sortByDesc('id')->first()
            ?: $galleys->sortByDesc('id')->first();

        $candidates = array_filter([
            $galley?->file_path,
            $this->revision_file,
            $this->manuscript_file,
        ]);

        foreach ($candidates as $relativePath) {
            if (strtolower(pathinfo($relativePath, PATHINFO_EXTENSION)) !== 'pdf') {
                continue;
            }

            $publicFull = public_path($relativePath);
            if (is_file($publicFull) && filesize($publicFull) > 0) {
                return $publicFull;
            }

            $storageFull = \Illuminate\Support\Facades\Storage::disk('public')->path($relativePath);
            if (is_file($storageFull) && filesize($storageFull) > 0) {
                return $storageFull;
            }
        }

        return null;
    }

    /**
     * Hitung jumlah halaman file PDF secara dinamis tanpa bergantung pada metadata manual.
     */
    public static function countPdfPages(string $filePath): ?int
    {
        if (! is_file($filePath) || ! is_readable($filePath) || filesize($filePath) === 0) {
            return null;
        }

        $content = @file_get_contents($filePath);
        if ($content === false || $content === '') {
            return null;
        }

        if (preg_match_all('/\/Type\s*\/Pages\b[^>]*?\/Count\s+(\d+)/s', $content, $matches) && ! empty($matches[1])) {
            $maxCount = max(array_map('intval', $matches[1]));
            if ($maxCount > 0) {
                return $maxCount;
            }
        }

        if (preg_match_all('/\/Type\s*\/Page(?![a-zA-Z])/', $content, $pageMatches) && ! empty($pageMatches[0])) {
            return count($pageMatches[0]);
        }

        return null;
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_SUBMITTED            => 'Terkirim',
            self::STATUS_UNDER_REVIEW         => 'Sedang Ditinjau',
            self::STATUS_REVISION_REQUIRED    => 'Perlu Revisi',
            self::STATUS_ACCEPTED             => 'Diterima',
            self::STATUS_REJECTED             => 'Ditolak',
            self::STATUS_WAITING_PAYMENT      => 'Menunggu Pembayaran',
            self::STATUS_PAYMENT_UPLOADED     => 'Bukti Pembayaran Diunggah',
            'payment_verification'            => 'Verifikasi Pembayaran',
            self::STATUS_PAID                 => 'Lunas',
            self::STATUS_PUBLISHED            => 'Diterbitkan',
        ];

        return $labels[$this->status] ?? ucwords(str_replace('_', ' ', (string) $this->status));
    }
}
