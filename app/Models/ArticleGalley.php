<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleGalley extends Model
{
    protected $fillable = [
        'article_id',
        'label',
        'file_path',
        'mime_type',
        'size_bytes',
        'is_published',
    ];

    protected $casts = [
        'size_bytes'   => 'integer',
        'is_published' => 'boolean',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('public.articles.download', ['slug' => $this->article->slug, 'galley' => $this->id]);
    }
}
