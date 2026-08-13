<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewRound extends Model
{
    protected $fillable = [
        'submission_id',
        'round_number',
        'blind_mode',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Article::class, 'submission_id');
    }

    public function assignments()
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function allAssignmentsCompleted(): bool
    {
        return $this->assignments()
            ->whereNotIn('status', ['completed', 'declined'])
            ->count() === 0;
    }
}
