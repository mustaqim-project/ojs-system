<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants untuk kemudahan
    const STATUS_SUBMITTED           = 'submitted';
    const STATUS_UNDER_REVIEW        = 'under_review';
    const STATUS_REVISION_REQUIRED   = 'revision_required';
    const STATUS_ACCEPTED            = 'accepted';
    const STATUS_REJECTED            = 'rejected';
    const STATUS_WAITING_PAYMENT     = 'waiting_payment';
    const STATUS_PAYMENT_UPLOADED    = 'payment_uploaded';
    const STATUS_PAYMENT_VERIFICATION = 'payment_verification';
    const STATUS_PAID                = 'paid';
    const STATUS_PUBLISHED           = 'published';

    protected $fillable = [
        'journal_id',
        'issue_id',
        'author_id',
        'assigned_editor_id',
        'title',
        'slug',
        'abstract',
        'keywords',
        'language',
        'manuscript_file',
        'revision_file',
        'cover_letter',
        'status',
        'pages_start',
        'pages_end',
        'doi',
        'editor_note',
        'author_note',
        'submitted_at',
        'accepted_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'accepted_at'  => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title) . '-' . Str::random(6);
            }
            if (empty($article->submitted_at)) {
                $article->submitted_at = now();
            }
        });
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

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
        return $this->belongsTo(User::class, 'author_id');
    }

    public function assignedEditor()
    {
        return $this->belongsTo(User::class, 'assigned_editor_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // ===========================
    // STATUS HELPER METHODS
    // ===========================

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function canBePublished(): bool
    {
        // KRITIS: Artikel hanya bisa dipublish jika sudah bayar & terverifikasi
        return $this->status === self::STATUS_PAID &&
            $this->payment &&
            $this->payment->status === 'verified';
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function needsPayment(): bool
    {
        return in_array($this->status, [
            self::STATUS_WAITING_PAYMENT,
            self::STATUS_PAYMENT_UPLOADED,
            self::STATUS_PAYMENT_VERIFICATION,
        ]);
    }

    /**
     * Label status untuk ditampilkan di UI
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SUBMITTED             => 'Submitted',
            self::STATUS_UNDER_REVIEW          => 'Under Review',
            self::STATUS_REVISION_REQUIRED     => 'Revision Required',
            self::STATUS_ACCEPTED              => 'Accepted',
            self::STATUS_REJECTED              => 'Rejected',
            self::STATUS_WAITING_PAYMENT       => 'Waiting Payment',
            self::STATUS_PAYMENT_UPLOADED      => 'Payment Uploaded',
            self::STATUS_PAYMENT_VERIFICATION  => 'Payment Verification',
            self::STATUS_PAID                  => 'Paid',
            self::STATUS_PUBLISHED             => 'Published',
            default                            => ucfirst($this->status),
        };
    }

    /**
     * Warna badge status
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SUBMITTED             => 'blue',
            self::STATUS_UNDER_REVIEW          => 'yellow',
            self::STATUS_REVISION_REQUIRED     => 'orange',
            self::STATUS_ACCEPTED              => 'green',
            self::STATUS_REJECTED              => 'red',
            self::STATUS_WAITING_PAYMENT       => 'purple',
            self::STATUS_PAYMENT_UPLOADED      => 'indigo',
            self::STATUS_PAYMENT_VERIFICATION  => 'cyan',
            self::STATUS_PAID                  => 'teal',
            self::STATUS_PUBLISHED             => 'emerald',
            default                            => 'gray',
        };
    }

    /**
     * Array keywords dari string
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (!$this->keywords) return [];
        return array_map('trim', explode(',', $this->keywords));
    }

    // ===========================
    // SCOPES
    // ===========================

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForAuthor($query, int $authorId)
    {
        return $query->where('author_id', $authorId);
    }
}
