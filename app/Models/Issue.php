<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'journal_id',
        'title',
        'volume',
        'number',
        'year',
        'description',
        'published_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
        ];
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function publishedArticles()
    {
        return $this->hasMany(Article::class)->where('status', 'published');
    }

    // ===========================
    // SCOPES
    // ===========================

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // ===========================
    // ACCESSORS
    // ===========================

    public function getDisplayTitleAttribute(): string
    {
        return "Vol. {$this->volume} No. {$this->number} ({$this->year})";
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
