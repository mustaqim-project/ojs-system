<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'journal_id',
        'title',
        'body',
        'cover_image',
        'publish_at',
        'expire_at',
        'status',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'expire_at'  => 'datetime',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expire_at')->orWhere('expire_at', '>=', now());
            });
    }
}
