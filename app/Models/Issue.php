<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'journal_id',
        'volume_id',
        'number',
        'title',
        'description',
        'cover_image',
        'publication_date',
        'status',
    ];

    protected $casts = [
        'publication_date' => 'date',
    ];

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

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
