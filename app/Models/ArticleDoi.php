<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleDoi extends Model
{
    protected $fillable = [
        'article_id',
        'doi',
        'registry',
        'registered_at',
        'response_payload',
    ];

    protected $casts = [
        'registered_at'    => 'datetime',
        'response_payload' => 'json',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function isRegistered(): bool
    {
        return !is_null($this->registered_at);
    }
}
