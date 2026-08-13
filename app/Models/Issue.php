<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'journal_id',
        'volume_id',
        'title',
        'volume',
        'number',
        'year',
        'description',
        'published_date',
        'status',
    ];

    protected $casts = [
        'published_date' => 'date',
    ];

    protected static function booted()
    {
        static::saving(function ($issue) {
            if (empty($issue->volume_id) && !empty($issue->volume) && !empty($issue->year) && !empty($issue->journal_id)) {
                $volume = Volume::firstOrCreate([
                    'journal_id' => $issue->journal_id,
                    'number'     => $issue->volume,
                    'year'       => $issue->year,
                ]);
                $issue->volume_id = $volume->id;
            }
        });
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function volume()
    {
        return $this->belongsTo(Volume::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'issue_article')
            ->withPivot('order')
            ->orderBy('order');
    }

    public function publishedArticles()
    {
        return $this->articles()->where('articles.status', 'published');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
