<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditorialDecision extends Model
{
    protected $fillable = [
        'submission_id',
        'review_round_id',
        'decided_by',
        'decision',
        'comment_to_author',
        'internal_note',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Article::class, 'submission_id');
    }

    public function reviewRound()
    {
        return $this->belongsTo(ReviewRound::class, 'review_round_id');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function getDecisionLabelAttribute(): string
    {
        return match ($this->decision) {
            'accept'         => 'Accepted',
            'reject'         => 'Rejected',
            'minor_revision' => 'Minor Revision',
            'major_revision' => 'Major Revision',
            'desk_reject'    => 'Desk Reject',
            default          => ucfirst($this->decision),
        };
    }
}
