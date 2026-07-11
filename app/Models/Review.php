<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'article_id',
        'reviewer_id',
        'status',
        'recommendation',
        'comments_to_author',
        'comments_to_editor',
        'review_file',
        'originality_score',
        'methodology_score',
        'relevance_score',
        'writing_score',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date'     => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // ===========================
    // HELPER METHODS
    // ===========================

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getAverageScoreAttribute(): ?float
    {
        $scores = array_filter([
            $this->originality_score,
            $this->methodology_score,
            $this->relevance_score,
            $this->writing_score,
        ]);

        if (empty($scores)) return null;
        return round(array_sum($scores) / count($scores), 1);
    }

    public function getRecommendationLabelAttribute(): string
    {
        return match ($this->recommendation) {
            'accept'       => 'Accept',
            'minor'        => 'Minor Revision',
            'major'        => 'Major Revision',
            'reject'       => 'Reject',
            default        => 'Pending',
        };
    }

    public function getRecommendationColorAttribute(): string
    {
        return match ($this->recommendation) {
            'accept' => 'green',
            'minor'  => 'yellow',
            'major'  => 'orange',
            'reject' => 'red',
            default  => 'gray',
        };
    }
}
