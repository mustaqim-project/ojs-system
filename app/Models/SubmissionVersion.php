<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class SubmissionVersion extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'submission_id',
        'version_number',
        'review_round_id',
        'response_to_reviewers_path',
        'created_by',
    ];

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function submission()
    {
        return $this->belongsTo(Article::class, 'submission_id');
    }

    public function reviewRound()
    {
        return $this->belongsTo(ReviewRound::class, 'review_round_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }
}
