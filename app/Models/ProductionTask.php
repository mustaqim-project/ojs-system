<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionTask extends Model
{
    protected $fillable = [
        'submission_id',
        'type',
        'status',
        'assigned_to',
        'file_path',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Article::class, 'submission_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'copyediting'  => 'Copy Editing',
            'layout'       => 'Layout Editing',
            'proofreading' => 'Proofreading',
            default        => ucfirst($this->type),
        };
    }
}
