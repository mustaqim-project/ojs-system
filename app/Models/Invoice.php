<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'journal_id',
        'submission_id',
        'invoice_number',
        'amount',
        'currency',
        'due_date',
        'status',
        'waived',
        'waiver_reason',
        'discount_amount',
        'approved_by',
    ];

    protected $casts = [
        'due_date'    => 'datetime',
        'amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'waived'      => 'boolean',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function submission()
    {
        return $this->belongsTo(Article::class, 'submission_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isWaived(): bool
    {
        return $this->waived || $this->status === 'waived';
    }

    public function getBalanceAttribute(): float
    {
        return $this->amount - ($this->discount_amount ?? 0);
    }
}
