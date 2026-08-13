<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

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
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'published_at' => 'datetime',
        'keywords'     => 'array',
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
        return $this->hasOne(Invoice::class);
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
