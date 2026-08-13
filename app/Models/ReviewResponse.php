<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewResponse extends Model
{
    protected $fillable = [
        'review_assignment_id',
        'recommendation',
        'score',
        'rubric_scores',
        'private_comment',
        'public_comment',
        'attachment_path',
        'submitted_at',
    ];

    protected $casts = [
        'score'         => 'decimal:2',
        'rubric_scores' => 'json',
        'submitted_at'  => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(ReviewAssignment::class, 'review_assignment_id');
    }

    public function getRecommendationLabelAttribute(): string
    {
        return match ($this->recommendation) {
            'accept'         => 'Accept',
            'minor_revision' => 'Minor Revision',
            'major_revision' => 'Major Revision',
            'reject'         => 'Reject',
            default          => ucfirst($this->recommendation),
        };
    }
}
