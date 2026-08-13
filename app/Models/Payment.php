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

    // ===========================
    // COMPATIBILITY ACCESSORS
    // ===========================

    public function getInvoiceCodeAttribute()
    {
        return $this->invoice?->invoice_number;
    }

    public function getProofFileAttribute()
    {
        return $this->proof_path;
    }

    public function getProofNotesAttribute()
    {
        return $this->status === 'waiting_verification' ? $this->notes : null;
    }

    public function getAdminNotesAttribute()
    {
        return in_array($this->status, ['verified', 'rejected']) ? $this->notes : null;
    }

    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'waiting_verification' => 'Menunggu Verifikasi',
            'verified'             => 'Terverifikasi',
            'rejected'             => 'Ditolak',
        ];
        return $statusLabels[$this->status] ?? $this->status;
    }

    public function getArticleAttribute()
    {
        return $this->invoice?->submission;
    }

    public function getUploadedAtAttribute()
    {
        return $this->created_at;
    }

    public function getBankNameAttribute()
    {
        $bank = $this->invoice?->journal?->bankAccounts()?->first();
        return $bank ? $bank->bank_name : \App\Models\Setting::get('bank_name', 'Bank Transfer');
    }

    public function getBankAccountAttribute()
    {
        $bank = $this->invoice?->journal?->bankAccounts()?->first();
        return $bank ? $bank->bank_account : \App\Models\Setting::get('bank_account', '-');
    }

    public function getBankHolderAttribute()
    {
        $bank = $this->invoice?->journal?->bankAccounts()?->first();
        return $bank ? $bank->bank_holder : \App\Models\Setting::get('bank_holder', 'Journal Manager');
    }
}
