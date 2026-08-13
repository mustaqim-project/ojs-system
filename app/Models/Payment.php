<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'author_id',
        'amount',
        'payment_method',
        'payment_date',
        'proof_path',
        'status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'verified_at'  => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
