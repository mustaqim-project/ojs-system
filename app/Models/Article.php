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
        'views_count',
        'downloads_count',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'published_at' => 'datetime',
        'keywords'     => 'array',
        'views_count'  => 'integer',
        'downloads_count' => 'integer',
    ];

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

    public function canBePublished(): bool
    {
        if (!$this->invoice) {
            return true;
        }
        return in_array($this->invoice->status, ['paid', 'waived']);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function getKeywordsArrayAttribute()
    {
        return is_array($this->keywords) ? $this->keywords : [];
    }
}
