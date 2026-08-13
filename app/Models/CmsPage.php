<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'journal_id',
        'slug',
        'title',
        'content',
        'status',
        'seo_meta',
        'updated_by',
    ];

    protected $casts = [
        'seo_meta' => 'json',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function versions()
    {
        return $this->hasMany(CmsPageVersion::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForJournal($query, int $journalId)
    {
        return $query->where('journal_id', $journalId);
    }
}
