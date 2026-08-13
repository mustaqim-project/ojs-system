<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewAssignment extends Model
{
    protected $fillable = [
        'review_round_id',
        'reviewer_id',
        'assigned_by',
        'status',
        'due_date',
        'decline_reason',
        'reminded_at',
        'escalated_at',
        'completed_at',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'reminded_at'  => 'datetime',
        'escalated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function round()
    {
        return $this->belongsTo(ReviewRound::class, 'review_round_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function response()
    {
        return $this->hasOne(ReviewResponse::class);
    }

    public function isInvited(): bool
    {
        return $this->status === 'invited';
    }
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }
    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }
}
