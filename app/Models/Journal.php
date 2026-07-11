<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Journal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'abbreviation',
        'description',
        'issn_print',
        'issn_online',
        'cover_image',
        'publisher',
        'subject_area',
        'frequency',
        'is_active',
        'editor_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($journal) {
            if (empty($journal->slug)) {
                $journal->slug = Str::slug($journal->title);
            }
        });
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ===========================
    // ACCESSORS
    // ===========================

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-journal-cover.png');
    }
}
