<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'article_id',
        'author_id',
        'verified_by',
        'invoice_code',
        'amount',
        'currency',
        'status',
        'proof_file',
        'proof_notes',
        'admin_notes',
        'verified_at',
        'uploaded_at',
        'bank_name',
        'bank_account',
        'bank_holder',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'verified_at' => 'datetime',
            'uploaded_at' => 'datetime',
        ];
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ===========================
    // HELPER METHODS
    // ===========================

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUploaded(): bool
    {
        return $this->status === 'uploaded';
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' ' . $this->currency;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu Pembayaran',
            'uploaded' => 'Bukti Diunggah',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'yellow',
            'uploaded' => 'blue',
            'verified' => 'green',
            'rejected' => 'red',
            default    => 'gray',
        };
    }

    public function getProofFileUrlAttribute(): ?string
    {
        if ($this->proof_file) {
            return asset('storage/' . $this->proof_file);
        }
        return null;
    }
}
